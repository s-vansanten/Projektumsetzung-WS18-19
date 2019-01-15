<?php
#####################################################################################################################################################################
#Verwendung: 	Einbindung im Admin-Portal durch "include 'create_json.php';"																						#
#				Im Admin-Portal setzung der globalen Varialen 	$end_lecture_time			als Datum des letzten Vorlesungstages des Semesters						#
#																$lecture_free_time_start 	als Anfangsdatum der vorlesungsfreien Zeit innerhalb des Semesters		#
#																$lecture_free_time_end		als Enddatum der vorlesungsfreien Zeit innerhalb des Semesters			#
#																																									#
#				Im Admin-Portal zum Starten der JSON-Erstellungs-Routine  die Funktion start_create($file_name) ausführen.											#
#				$file_name ist eine Variable mit dem Verweis auf eine hochgeladene Datei																			#
#																																									#
#				Im Unterordner "events/" werden dann JSON-Dateien mit folgendem Schema erstellt:																	#
#						"Studiengang"_"Jahr des Semesterstartes"_"Semesternummer".json																				#
#																																									#
#####################################################################################################################################################################
	
	include 'create_ics.php';
	
	#Ordner zum Abspeichern der JSON-Dateien
	$events_dir = "events/";
	
	#Global definierte Variable zum Umgehen von Scope-Problemen bei der Funktion "detectDelimiter"
	$file_name_dect = NULL;
	
	#Array mit zu verwendenen Farben
	#https://www.w3schools.com/cssref/css_colors.asp
	$color_array = array("#ffdfdf", "#e0e1ff", "#fff4e0", "#ddffe5", "#fbecec", "#d6e4f9", "#d9fcff", "#f4f4f4", "#e8ffe5", "#faffe0", "#e5e5e5", "#fff6f6", "#ffdcdc", "#e1c5c5", "#eeeeee");
	
	#Start-Funktion für die Routine zur Erstellung von JSON-Dateien
	#Input:		$file_name - Name einer hochgeladene CSV-Datei
	#Output:	NULL	
	function start_create($file_name){
		
		global $end_lecture_time;
		
		#Setzen der global definierte Variable zum Umgehen von Scope-Problemen bei der Funktion "detectDelimiter"
		$GLOBALS['file_name_dect'] = $file_name;
		
		
		#Einlesen der Datei
		$file = file($file_name);
		#Array aus CSV Datei erstellen
		$csv[] = array_map(	function($v){return str_getcsv($v, detectDelimiter());}, $file);
		
		#Konvertierung in UTF8 zur Eliminierung von Problemen mit Umlauten
		#via http://nazcalabs.com/blog/convert-php-array-to-utf8-recursively/
		#Löst 2 Probleme:
		#Beim zeilenweisen Durchlaufen des Arrays in Funktion "create_json_semester" kann sich das Programm aufhängen, wenn es auf die Zeile "Module mit mehreren Schwerpunkten / Teilung LV und Übung" traf wegen dem "Ü"
		#In der Funktion "create_returning" beim Aufruf der php-Funktion "preg_match_all" muss jetzt nicht mehr der Input in UTF8 konvertiert werden. Ohne Konvertierung kam es zu Problemen, da im Text "dafür" steht
		array_walk_recursive($csv, function(&$item, $key){
			if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
			}
		});
		
		#Löschen des durch das Einlesen erstelle obere Array (csv[0] enthält das Array mit den CSV-Daten, csv[1] existiert nicht)
		$csv = $csv[0];
		
		#Associative Array erstellen:
		#csv[0] enthält die Spalten-Namen
		#array_splice entfernt diese erste Zeile dann
		$arr_key = $csv[0];
		array_splice ($csv,0,1);

		#Jedem Eintrag wird der dazugehörigen Spalten-Name als Key hinzugefügt, damit nicht nach Spalten-Nummer sondern nach Spalten-Name später gesucht werden kann
		for ($row = 0; $row < count($csv); $row++) {
		  $csv[$row] = array_combine ($arr_key, $csv[$row]);
		}
			
		#Jahr wird durch Verwendung von $end_lecture_time gesetzt
		#Fall 1: Monat Januar bis Mai => Start des Semesters liegt ein Jahr vor dem Ende des Semesters
		#Fall 2: Monat Juni bis Dezember => Start des Semesters liegt im gleichem Jahr wie das Ende des Semesters
		if(date("m", $end_lecture_time) < 6 && date("m", $end_lecture_time) >= 0){
			$year = date("Y", $end_lecture_time)-1;
		}else if(date("m", $end_lecture_time) >= 6 && date("m", $end_lecture_time) <= 12){
			$year = date("Y", $end_lecture_time);
		}else{
			echo '"$end_lecture_time" falsch gesetzt';
			return;
		}
		
		create_json($csv, $year);
	}	
	
	#Funktion zur Festellung des Trennzeichen der CSV-Datei
    #wird beim Erstellen des Arrays aus der CSV-Datei verwendet
    function detectDelimiter(){
		
		#Global definierte Variable zum Umgehen von Scope-Problemen bei der Funktion "detectDelimiter"
		global $file_name_dect;
		
		#mögliche Trennzeichen
		$delimiters = array(
			';' => 0,
			',' => 0,
			"\t" => 0,
			"|" => 0
		);

		#Öffnen der Datei, Auslesung der ersten Zeile und Schließen der Datei
		$handle = fopen($file_name_dect, "r");
		$firstLine = fgets($handle);
		fclose($handle);

		#Iteration durch alle möglichen Trennzeichen und Zählung ihrer Anzahl in der ersten Zeile der CSV-Datei
		foreach ($delimiters as $delimiter => &$count) {
			$count = count(str_getcsv($firstLine, $delimiter));
		}

		#Rückgabe des Trennzeichen mit der höhsten Anzahl
		return array_search(max($delimiters), $delimiters);
	   
    }
	
	#Funktion zur Setzung eines Timestamp anhand der Übergabe der Woche und des Tages
	#Input: 	$row - Reihe (somit Modul)
	#			$week - Wochennummer
	#			$date_string
	#			$start_or_end - Variable (Wert 1 oder 2, kann auf true/false angepasst werden), der bestimmt, ob die Startzeit- oder Endzeit-Spalte zur Berechnugn des Timestamps verwendet wird
	#			$csv - Array der Module, aus CSV importiert und formatiert
	#			$year - Jahr
	function setTimestamp ($row, $week, $date_string, $start_or_end, $csv, $year){
				
		#Routine zum Anpassen der Jahres-Zahl für Sondertermine von Januar bis Ende März
		if($date_string != NULL){
			#Splitten der Zahl aus der Zeitspalte
			#aus XX.YY wird [0]=XX und [1]=YY
			$date_splited = explode(".", $date_string);
			if($date_splited[1] < 4){
				$year++;
			}
		}
		
		#Variable wird verändert, wenn $date_string != NULL und der Tag direkt berechnet werden kann
		$dayOfWeek = 0;	
		#Variable wird verändert, wenn $week != NULL und der Tag somit über die Kalenderwoche berechnet wird		
		$week_day = NULL;		
				
		#Überprüfung, ob die Kalenderwoche im nächsten Jahr liegt
		#Kann im Wintersemester der Fall sein
		#Wenn die Kalenderwoche kleiner 12 ist, sollte es sich in der Regel um das nächste Jahr handeln
		if($week <= 12){
				$year++;
		}
		
		/*
		#Extrahieren von Zahlen aus dem String
		$week_no = (float) filter_var($week, FILTER_SANITIZE_NUMBER_FLOAT);
		#Sollten die Zahl mehr als zwei Zeichen haben, werden alle Ziffern außer die ersten beiden gelöscht
		#Dies kommt vor, wenn außer der Kalenderwoche auch noch Information zum Turnus (bei unser CSV 14-Tägigkeit) in der Zelle enthalten sind ((1 Jahr = 52,14 Wochen)
		if(strlen((string)$week_no) != 2){
			$week_no = substr($week_no, 0, 2);
		}*/
		
		#Berechnen des Anfangs der Kalenderwoche über Kalenderwoche-Nummer
		if($date_string == NULL){
			#Berechnung des Beginn einer Kalenderwoche, Montags 0:00 Uhr
			$week_start = new DateTime();
			$week_start->setISODate($year,$week);
			
			#Umformatierung in UNIX-Timestamp zur einfachen Aufaddierung von Sekunden
			$date = strtotime($week_start->format('Y-m-d'));
		}
		#Berechnung des Tages aus dem übergeben Datum
		else if($week == NULL){
			#Get weekday from date			
			$date = strtotime($date_string."".$year);
			$dayOfWeek = date("N", $date);
		}
		#Fehler-Abfangung
		else{
			echo 'Funktion "setTimestamp" falsch aufgerufen';
			return;
		}		
		
		#Bestimmung, ob $dayOfWeek geändert wurde und wenn ja auf welchen Wert oder welche Spalte mit Startzeiten einen Wert enthält, sollte $dayOfWeek unverändert sein
		#$week_day beinhaltet wieviele Tage nach Montag der Timestamp gesetz werden soll
		if($dayOfWeek == "1" OR $csv[$row]['Mo B'] != NULL){
			$week_day = "0";
			if($start_or_end == "1"){
				$time = $csv[$row]['Mo B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Mo E'];
			}
		}else if($dayOfWeek == "2" OR $csv[$row]['Di B'] != NULL){
			$week_day = "1";
			if($start_or_end == "1"){
				$time = $csv[$row]['Di B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Di E'];
			}
		}else if($dayOfWeek == "3" OR $csv[$row]['Mi B'] != NULL){
			$week_day = "2";
			if($start_or_end == "1"){
				$time = $csv[$row]['Mi B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Mi E'];
			}
		}else if($dayOfWeek == "4" OR $csv[$row]['Do B'] != NULL){
			$week_day = "3";
			if($start_or_end == "1"){
				$time = $csv[$row]['Do B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Do E'];
			}
		}else if($dayOfWeek == "5" OR $csv[$row]['Fr B'] != NULL){
			$week_day = "4";
			if($start_or_end == "1"){
				$time = $csv[$row]['Fr B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Fr E'];
			}
		}else if($dayOfWeek == "6" OR $csv[$row]['Sa B'] != NULL){
			$week_day = "5";
			if($start_or_end == "1"){
				$time = $csv[$row]['Sa B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Sa E'];
			}
		}
		
		#Splitten der Zahl aus der Zeitspalte
		#aus XX.YY wird [0]=XX und [1]=YY
		$time_splited = explode(".", $time);
		
		#Überprüfen, ob der Wochentag noch addiert werden muss
		#Gescheht nur sollte $dayOfWeek geändert worden sein
		if($dayOfWeek == 0){
			#Addieren von (Tage nach Montag*Sekunden pro Tag) auf den Timestamp
			$date += $week_day*86400;
		}
		#Addieren von (Stunde*Sekunden pro Stunde) auf den Timestamp
		$date += $time_splited[0]*3600;
		#Addieren von (Minuten*Sekunden pro Minute) auf den Timestamp
		$date += $time_splited[1]*60;
		
		#Umformatierung des UNIX-Timestamp in ISO8601-Timestamp-Format
		#Es gab Probleme bei der Anzeige im Fullcalender, wenn das UNIX-Timestamp-Format gewählt wurde
		$date = date(DATE_ISO8601, $date);		
		
		#Rückgabe des Timestamps
		return $date;
	}
	
	#Angelehnt an
	#https://stackoverflow.com/questions/4128323/in-array-and-multidimensional-array
	function in_array_r($needle, $haystack){
		foreach ($haystack as $item){
			if($item['Studgang'] == $needle['Studgang'] && $item['Sem'] == $needle['Sem']){
				return true;
			}
		}
		return false;
	}
	
	#Funktion zur Erstellung einer JSON-Datei
	#Input:		$csv - Array der Module, aus CSV importiert und formatiert
	#			$year - Jahr
	#			$feiertage - Feiertage für Berlin
	#Output:	NULL
	function create_json($csv, $year){		

	
		#Feiertage-Array
		#https://www.php.de/forum/webentwicklung/php-einsteiger/1470253-feiertage-ermitteln
		$feiertage = array(
		date("Y-m-d", mktime(0,0,0,1,1,$year)),						//Neujahr
		date("Y-m-d", mktime(0,0,0,1,1,$year+1)),					//Neujahr
		date("Y-m-d", strtotime("-2 day",easter_date($year))),		//Karfreitag	
		date("Y-m-d", easter_date($year)),							//Ostersonntag
		date("Y-m-d", strtotime("+1 day",easter_date($year))),		//Ostermontag
		date("Y-m-d", mktime(0,0,0,5,1,$year)),						//Tag der Arbeit
		date("Y-m-d", strtotime("+39 day",easter_date($year))), 	//Christi Himmelfahrt
		date("Y-m-d", strtotime("+49 day",easter_date($year))),		//Pfingstsonntag
		date("Y-m-d", strtotime("+50 day",easter_date($year))),		//Pfingstmontag
		date("Y-m-d", mktime(0,0,0,10,3,$year)), 					//Tag der Deutschen Einheit
		date("Y-m-d", mktime(0,0,0,12,24,$year)),					//Heiligabend
		date("Y-m-d", mktime(0,0,0,12,25,$year)),					//1. Weihnachtstag
		date("Y-m-d", mktime(0,0,0,12,26,$year))					//2. Weihnachtstag
		);
		
		
		#Herausfiltern, welche Studiengänge und dazugehörige Semester alle in der CSV-Datei vertreten sind
		$entries = array();		
		foreach ($csv as $key => $values){
			#Problematik an dieser Stelle:
			#Bei der neuen CSV-Datei kam es beim Associative Array zum Problem, dass der Schlüssel "Studgang" nicht gefunden wurde
			#Deshalb wird hier für den Fall, dass das Associative Array Probleme macht, für den Studiengang einfach das erste Element verwendet
			#Folgeproblematik entsteht, wenn der Studiengang in der CSV nicht in der ersten Spalte steht
			if(array_key_exists('Studgang', $values)){
				if ( !(in_array_r(array('Studgang'=>$values['Studgang'], 'Sem'=>$values['Sem']), $entries)) && $values['Studgang'] != NULL){
				array_push($entries, array('Studgang'=>$values['Studgang'], 'Sem'=>$values['Sem']));
				}
			}else{
				if ( !(in_array_r(array('Studgang'=>array_values($values)[0], 'Sem'=>$values['Sem']), $entries)) && array_values($values)[0] != NULL){
				array_push($entries, array('Studgang'=>array_values($values)[0], 'Sem'=>$values['Sem']));
				}
			}
		}
		
		#Aufruf von Unterfunktion zur Erstellung einer JSON-Datei für jedes vertretene Semester eines jeden vertretenen Studienganges
		foreach ($entries as $values){			
			create_json_semester($values, $csv, $year, $feiertage);
		}		
	}
	
	#Funktion zur Erstellung einer JSON-Datei für ein bestimmtes Semester
	#Input:		$stud_sem - Objekt mit Studiengangs- und Semester-Information
	#			$csv - Array der Module, aus CSV importiert und formatiert
	#			$year - Jahr
	#			$feiertage - Feiertage für Berlin
	#Output:	NULL
	function create_json_semester($stud_sem, $csv, $year, $feiertage){		
		
		#Einbindung der globalen Variablen
		#$events_dir 	- Ordner zum Abspeichern der JSON-Dateien
		#$color_array	- Array mit zu verwendenen Farben
		global $events_dir, $color_array;
		
		#Erzeugen des ID-Zählers und setzen auf -1, da vor der ersten Verwendung der Zähler um Eins erhöht wird
		$id_counter = -1;
		#Erzeugen eines Array zum Festhalten von verwendeten IDs für die jeweilige Modul-Nummer		
		$modul_id_catcher = array();
		#Erzeugen eines Array zum Erfassen aller Event-Einträge
		$posts = array();
		
		#json-Array erstellen
		for ($row = 0; $row < count($csv); $row++){
			if($csv[$row]['Sem'] == $stud_sem['Sem']){
				$kw_start = $csv[$row]['LV-Start'];				
				
				$modul_token = $csv[$row]['Modul']." ".utf8_encode($csv[$row]['Art'])." Gruppe ".$csv[$row]['Stud-Gr'];
				
				file_put_contents("test.txt", $modul_token.PHP_EOL, FILE_APPEND);
				
				#Überprüfen, ob für die Modul-Nummer schon ein ID gesetzt wurde
				#Wenn ja, werdenen der ID sowie der dazugehörigen Farbe
				#Wenn nicht, erhöhen des ID-Zählers
				if(in_array($modul_token, $modul_id_catcher)){
					$id = array_search($modul_token, $modul_id_catcher);
					$color = $color_array[$id];
				}else{
					$id_counter++;
					$modul_id_catcher[] = $modul_token;
					$id = $id_counter;
					$color = $color_array[$id_counter];	
				}				
				
				#Fall: LV-Start is set
				if($kw_start != NULL){
					
					#Herausfiltern der Start-Kalenderwoche für die Timestamp-Funktion
					preg_match('/([0-9][0-9])/', $kw_start, $kw_start_matches);
					
					print_r($kw_start_matches[0]);
					echo "<br/>";
					
					$title = $csv[$row]['Modul']." ".utf8_encode($csv[$row]['Art'])." Gruppe ".$csv[$row]['Stud-Gr'];					
					$start_time	= setTimestamp($row, $kw_start_matches[0], NULL, "1", $csv, $year);
					$end_time = setTimestamp($row, $kw_start_matches[0], NULL, "2", $csv, $year);
					$location = $csv[$row]['Raum'];
					$textColor = "black";
					#Abfragen, ob auch in der Sondertermin-Spalte ein Wert steht
					if($csv[$row]['Sondertermine'] != NULL){
						$sonder_catch = $csv[$row]['Sondertermine'];
					}else{
						$sonder_catch = NULL;
					}
					
					$modul_entry_start = array('title'=> $title,'start'=>$start_time,'end'=>$end_time, 'sonder_catch'=>$sonder_catch, 'id'=>$id, 'color'=>$color, 'textColor'=>$textColor, 'location'=>$location);
					
					#Filterung LV-Start
					#Fall 1: ab XX. KW
					#Fall 2: ab XX. KW, XX-tätig
					#Fall 3: XX. KW bis YY. KW
					
					
					#Fall 3:
					if(preg_match('/([0-9][0-9][.][ ]KW[ ]bis[ ][0-9][0-9][.][ ]KW)/', $kw_start) === 1){
						
						#Herausfiltern der zweiten genannten Kalenderwoche aus LV-Start
						preg_match('/(bis[ ][0-9][0-9][.][ ]KW)/', $kw_start, $matches);
						preg_match('!\d+!', $matches[0], $sub_matches);
						
						$posts = array_merge($posts, create_returning($modul_entry_start, 1, $year, $feiertage, $sub_matches[0]));
					}
					#Fall 2:
					else if(preg_match('/(ab[ ][0-9][0-9][.][ ]KW,[ ][0-9][0-9])/', $kw_start) === 1){
						$posts = array_merge($posts, create_returning($modul_entry_start, 2, $year, $feiertage, NULL));
					}
					#Fall 1:
					else{
						$posts = array_merge($posts, create_returning($modul_entry_start, 1, $year, $feiertage, NULL));
					}
				#Fall: LV-Start not set && Sondertermine is set	
				}else if($kw_start == NULL && $csv[$row]['Sondertermine'] != NULL ){
					$posts = array_merge($posts, create_sondertermine($row, $csv, $year, $feiertage, $id));					
				}
			}		
		}		
		
		#Hinzufügen von Feiertagen
		$posts = array_merge($posts, create_feiertage($year));
		
		#Routine zum Erstellen einer JSON-Datei
		$file_name = $events_dir.$stud_sem['Studgang'].'_'.$year.'_'.$stud_sem['Sem'].'.json';
		$fp = fopen($file_name,'w');
		fwrite($fp, json_encode($posts));
		fclose($fp);
		
		start_create_ics($file_name);
	}
	
	#Funktion zur Erstellung von wöchentlichen oder mehr-wöchentlichen Terminen
	#Input: 	$posts - Arrays aus Ersttermin-Events
	#			$turnus - Gibt den Wochenturnus an
	#			$year - Jahr
	#			$feiertage - Feiertage für Berlin
	#			$end_kw - Kalenderwoche, in der das Modul zuletzt staatfindet - NULL wenn das Modul bis zum Ende des Semesters läuft
	#Output: 	$returning_posts - Array aus Events vom Beginn bis zum Ende des Semesters oder bis zu einer gesetzten Kalenderwoche
	function create_returning($entry, $turnus, $year, $feiertage, $end_kw){
		
		#Timestamp-Variable, welche das Ende der Vorlesungszeit setzt
		global $end_lecture_time, $lecture_free_time_start, $lecture_free_time_end;
		
		echo "entry = <br/>";
		print_r($entry);
		echo "<br/>";
		
		#Array zum Abspeichern der Event-Einträge		
		$returning_posts = array();
		
		#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format			
		$modul_start_date = strtotime($entry['start']);
		#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format
		$modul_end_date = strtotime($entry['end']);
		$title = $entry['title'];
		$id = $entry['id'];
		$color = $entry['color'];
		$textColor = $entry['textColor'];
		$location = $entry['location'];
		
		#Das Ende des Modul wird durch das Ende der Vorlesungszeit gesetzt oder durch eine gesetzte Kalenderwoche
		$end_modul_time = $end_lecture_time;		
		if($end_kw != NULL){			
			#Überprüfung, ob die Kalenderwoche im nächsten Jahr liegt
			#Kann im Wintersemester der Fall sein
			#Wenn die Kalenderwoche kleiner 12 ist, sollte es sich in der Regel um das nächste Jahr handeln
			#ALTERNATIV: Start-Termin wird um 4 Monate erhöht und das Jahr mit dem gesetzten Jahr überprüft
			#$year != date("Y", $modul_start_date+10519200)
			if($end_kw <= 12){
				$end_kw_year = $year+1;
			}else{
				$end_kw_year = $year;
			}			
			
			#end_kw wird um 1 erhöht, damit das Ende der Woche bestimmt wird
			#Berechnung des Beginn einer Kalenderwoche, Montags 0:00 Uhr
			$week_start = new DateTime();
			$week_start->setISODate($end_kw_year,$end_kw+1);
			
			#Umformatierung in UNIX-Timestamp zur einfachen Aufaddierung von Sekunden
			$end_modul_time = strtotime($week_start->format('Y-m-d'));
		}
			
		while($end_modul_time > $modul_start_date){
			#Check von Feiertagen & Check vorlesungsfreier Zeit
			if(!in_array(date("Y-m-d", $modul_start_date), $feiertage) && !($modul_start_date>$lecture_free_time_start && $modul_start_date<$lecture_free_time_end+86400)){
				#Check für Anpassung von Terminen durch Sondertermin-Spalte
				#nicht 09.06., dafür 16.06.
				if($entry['sonder_catch'] != NULL){
					$sonder_catch_entries;
					preg_match_all('/nicht [0-9][0-9][.][0-9][0-9]/', $entry['sonder_catch'] ,$sonder_catch_entries[0]);
					preg_match_all('/dafür [0-9][0-9][.][0-9][0-9]/', $entry['sonder_catch'] ,$sonder_catch_entries[1]);
					$sonder_catch_entries[0] = $sonder_catch_entries[0][0];
					$sonder_catch_entries[1] = $sonder_catch_entries[1][0];				
					
					for($i = 0; $i < count($sonder_catch_entries[0]); $i++){
						$sonder_catch_entries[0][$i] = preg_replace('/nicht /', '', $sonder_catch_entries[0][$i]);
					}
					for($i = 0; $i < count($sonder_catch_entries[1]); $i++){
						$sonder_catch_entries[1][$i] = preg_replace('/dafür /', '', $sonder_catch_entries[1][$i]);
					}
					
					for($i = 0; $i<count($sonder_catch_entries[0]); $i++){
						if(date("d-m-Y", $modul_start_date) == date("d-m-Y", strtotime($sonder_catch_entries[0][$i].".".$year))){
							#FALL: nicht XX.XX , dafür YY.YY
							if(!empty($sonder_catch_entries[1])){
								$new_modul_start_date = strtotime($sonder_catch_entries[1][$i].".".$year." ".date("H:i", $modul_start_date));
								$new_modul_end_date = strtotime($sonder_catch_entries[1][$i].".".$year." ".date("H:i", $modul_end_date));
								$returning_posts[] = array('title'=> $title,'start'=>date(DATE_ISO8601, $new_modul_start_date),'end'=>date(DATE_ISO8601, $new_modul_end_date), 'id'=>$id, 'color'=>$color, 'textColor'=>$textColor, 'location'=>$location);
							}
							#FALL: nicht XX.XX [ohne YY.YY]
							else{
								#nichts tun
							}
						}else{
							$returning_posts[] = array('title'=> $title,'start'=>date(DATE_ISO8601, $modul_start_date),'end'=>date(DATE_ISO8601, $modul_end_date), 'id'=>$id, 'color'=>$color, 'textColor'=>$textColor, 'location'=>$location);
						}
					}
				}else{
					$returning_posts[] = array('title'=> $title,'start'=>date(DATE_ISO8601, $modul_start_date),'end'=>date(DATE_ISO8601, $modul_end_date), 'id'=>$id, 'color'=>$color, 'textColor'=>$textColor, 'location'=>$location);
				}								
			}			
			
			#Erhöhung des Timestamp um (Sekunden pro Woche)*Turnus
			$modul_start_date += 604800*$turnus;
			$modul_end_date += 604800*$turnus;
		}
		
		#Rückgabe von Event-Einträgen
		return $returning_posts;
	}
	
	#Funktion zum Erstellen von Terminen anhand der angebenen Sondertermine
	#Input:		$row - Reihe im Array der Module, welche nur Sondertermine angebenen haben+
	#			$csv - Array der Module, aus CSV importiert und formatiert
	#			$year - Jahr
	#			$feiertage - Feiertage für Berlin
	#Output:	$posts - Array aus Events für die angebenen Sondertermine
	function create_sondertermine($row, $csv, $year, $feiertage, $id){		
		
		global $color_array;
		
		#Array zum Abspeichern der Termine
		$sondertermine_array = array();
		
		#Eintrag von Sonderterminen		
		$sondertermine_plan_text = $csv[$row]['Sondertermine'];	
		
		#Funktion such nach dem verwendeten Datumsschema im Eintrag von Sonderterminen und speichert diese in einem Array
		preg_match_all('/[0-9][0-9][.][0-9][0-9][.]/', $sondertermine_plan_text ,$sondertermine_array);
		#Reduzierung des drei-dimensonalen Arrays in ein zwei-dimensonales Array 
		$sondertermine_array = $sondertermine_array[0];
		
		#Array zum Abspeichern der Event-Einträge
		$posts = array();
		
		#Titel-Deklaration des Moduls
		$title = $csv[$row]['Modul']." ".utf8_encode($csv[$row]['Art'])." Gruppe ".$csv[$row]['Stud-Gr'];
		$color = $color_array[$id];
		$textColor = "black";
		$location = $csv[$row]['Raum'];
		
		#Iteration über alle gefunden Sondertermine und dabei Erstellung des Events-Eintrag für den Sondertermin
		foreach ($sondertermine_array as $entry){
			$start_time	= setTimestamp($row, NULL, $entry, "1", $csv, $year);
			$end_time = setTimestamp($row, NULL, $entry, "2", $csv, $year);			
			$posts[]= array('title'=> $title,'start'=>$start_time,'end'=>$end_time, 'id'=>$id, 'color'=>$color, 'textColor'=>$textColor, 'location'=>$location);
		}		
		
		#Rückgabe von Event-Einträgen
		return $posts;		
	}
	
	#Funktion zum Erstellen von Feiertags-Terminen
	#Input:		$year - Jahr
	#Output:	$posts - Array aus ganztägigen Events für Feiertage des Landes Berlins
	function create_feiertage($year){
				
		$posts = array();
		
		$posts[] = array('title'=> 'Neujahr','start'=>date("Y-m-d", mktime(0,0,0,1,1,$year)),'end'=>date("Y-m-d", mktime(0,0,0,1,1,$year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Neujahr','start'=>date("Y-m-d", mktime(0,0,0,1,1,$year+1)),'end'=>date("Y-m-d", mktime(0,0,0,1,1,$year+1)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Karfreitag','start'=>date("Y-m-d", strtotime("-2 day",easter_date($year))),'end'=>date("Y-m-d", strtotime("-2 day",easter_date($year))), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Ostersonntag','start'=>date("Y-m-d", easter_date($year)),'end'=>date("Y-m-d", easter_date($year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Ostermontag','start'=>date("Y-m-d", strtotime("+1 day",easter_date($year))),'end'=>date("Y-m-d", strtotime("+1 day",easter_date($year))), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Tag der Arbeit','start'=>date("Y-m-d", mktime(0,0,0,5,1,$year)),'end'=>date("Y-m-d", mktime(0,0,0,5,1,$year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Christi Himmelfahrt','start'=>date("Y-m-d", strtotime("+39 day",easter_date($year))),'end'=>date("Y-m-d", strtotime("+39 day",easter_date($year))), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Pfingstsonntag','start'=>date("Y-m-d", strtotime("+49 day",easter_date($year))),'end'=>date("Y-m-d", strtotime("+49 day",easter_date($year))), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Pfingstmontag','start'=>date("Y-m-d", strtotime("+50 day",easter_date($year))),'end'=>date("Y-m-d", strtotime("+50 day",easter_date($year))), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Tag der Deutschen Einheit','start'=>date("Y-m-d", mktime(0,0,0,10,3,$year)),'end'=>date("Y-m-d", mktime(0,0,0,10,3,$year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> 'Heiligabend','start'=>date("Y-m-d", mktime(0,0,0,12,24,$year)),'end'=>date("Y-m-d", mktime(0,0,0,12,24,$year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> '1. Weihnachtstag','start'=>date("Y-m-d", mktime(0,0,0,12,25,$year)),'end'=>date("Y-m-d", mktime(0,0,0,12,25,$year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		$posts[] = array('title'=> '2. Weihnachtstag','start'=>date("Y-m-d", mktime(0,0,0,12,26,$year)),'end'=>date("Y-m-d", mktime(0,0,0,12,26,$year)), 'allDay'=>true, 'color'=>'red', 'location'=>"Berlin");
		
		return $posts;
	}
?>
<html>
<head>
	 <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	
</head>
<body>
<?php
	#Definieren des Dateinamen
    $fileName = 'excel.csv';
    #Einlesen der Datei in ein Array
    $file = file($fileName);
	
	#Array aus CSV Datei erstellen:
    $csv[] = array_map(function($v){return str_getcsv($v, detectDelimiter());}, $file);

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
	
	#Funktion zur Festellung des Trennzeichen der CSV-Datei
    #wird beim Erstellen des Arrays aus der CSV-Datei verwendet
    function detectDelimiter(){

      #vorher global definierter Datei-Name
      global $fileName;

      #mögliche Trennzeichen
      $delimiters = array(
          ';' => 0,
          ',' => 0,
          "\t" => 0,
          "|" => 0
       );

      #Öffnen der Datei, Auslesung der ersten Zeile und Schließen der Datei
       $handle = fopen($fileName, "r");
       $firstLine = fgets($handle);
       fclose($handle);

       #Iteration durch alle möglichen Trennzeichen und Zählung ihrer Anzahl in der ersten Zeile der CSV-Datei
       foreach ($delimiters as $delimiter => &$count) {
          $count = count(str_getcsv($firstLine, $delimiter));
       }

       #Rückgabe des Trennzeichen mit der höhsten Anzahl
       return array_search(max($delimiters), $delimiters);
    }
	
	#Funktion zum Filtern des Arrays:
    function filterArray ($array) {

      global $semester, $course;
      $arr_sl = array ();

      foreach ($array as $key => $values) {
        if ( $values['Studgang'] == $course && $values['Sem'] == $semester) {
          $temp_sl = array_slice ($array, $key, 1);
          $arr_sl = array_merge ($temp_sl, $arr_sl);
        }
      }
      $array = $arr_sl;
    }
	
	#Funktion zur Setzung eines Timestamp anhand der Übergabe der Woche und des Tages
	#Input: $row - Reihe (somit Modul)
	#		$week - Wochennummer als String (muss nicht zwinged übergeben werden, da der Wert auch durch "$csv[$row]['LV-Start']" abgerufen werden kann
	#		$date_string
	#		$start_or_end - Variable (Wert 1 oder 2, kann auf true/false angepasst werden), der bestimmt, ob die Startzeit- oder Endzeit-Spalte zur Berechnugn des Timestamps verwendet wird
	function setTimestamp ($row, $week, $date_string, $start_or_end){

		#Globale Variablen
		#$csv - Array der Module, aus CSV importiert und formatiert
		#$year - Jahr	
		global $year, $csv;
		
		#Variable wird verändert, wenn $date_string != NULL und der Tag direkt berechnet werden kann
		$dayOfWeek = 0;	
		#Variable wird verändert, wenn $week != NULL und der Tag somit über die Kalenderwoche berechnet wird		
		$week_day = NULL;
		
		
		#Extrahieren von Zahlen aus dem String
		$week_no = (float) filter_var($week, FILTER_SANITIZE_NUMBER_FLOAT);
		#Sollten die Zahl mehr als zwei Zeichen haben, werden alle Ziffern außer die ersten beiden gelöscht
		#Dies kommt vor, wenn außer der Kalenderwoche auch noch Information zum Turnus (bei unser CSV 14-Tägigkeit) in der Zelle enthalten sind ((1 Jahr = 52,14 Wochen)
		if(strlen((string)$week_no) != 2){
			$week_no = substr($week_no, 0, 2);
		}
		
		#Berechnen des Anfangs der Kalenderwoche über Kalenderwoche-Nummer
		if($date_string == NULL){
			#Berechnung des Beginn einer Kalenderwoche, Montags 0:00 Uhr
			$week_start = new DateTime();
			$week_start->setISODate($year,$week_no);
			
			#Umformatierung in UNIX-Timestamp zur einfachen Aufaddierung von Sekunden
			$date = strtotime($week_start->format('Y-m-d'));
		}
		#Berechnung des Tages aus dem übergeben Datum
		else if($week == NULL){
			#Get weekday from date
			$date = strtotime($date_string.".".$year);
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
		}else if($dayOfWeek == "2" OR$csv[$row]['Di B'] != NULL){
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
	
	filterArray($csv);
	
	#Jahr - sollte später entweder durch Auslesen aus der CSV oder durch Admin-Eingabe gesetzt werden
	$year = "2018";
	#Vorlesungszeit-Ende - sollte später entweder durch Auslesen aus der CSV oder durch Admin-Eingabe gesetzt werden
	$end_lecture_time = "2018-07-28";
	//28. Juli 2018
	
	#https://stackoverflow.com/questions/4128323/in-array-and-multidimensional-array
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}
	
	#Funktion zur Erstellung einer JSON-Datei
	function create_json(){
		
		#Globale Variablen
		#$csv - Array der Module, aus CSV importiert und formatiert
		#$year - Jahr
		global $csv, $year;
		
		#Herausfiltern, welche Studiengänge und dazugehörige Semester alle in der CSV-Datei vertreten sind
		$entries = array();
		foreach ($csv as $key => $values){
			if ( !(in_array_r(array($values['Studgang'], $values['Sem']), $entries)) && $values['Studgang'] != NULL){
				array_push($entries, array('Studgang'=>$values['Studgang'], 'Sem'=>$values['Sem']));
			}
		}
		
		#Aufruf von Unterfunktion zur Erstellung einer JSON-Datei für jedes vertretene Semester eines jeden vertretenen Studienganges
		foreach ($entries as $values){
			create_json_semester($values);
		}		
	}
	
	#Funktion zur Erstellung einer JSON-Datei für ein bestimmtes Semester
	function create_json_semester($stud_sem){
		
		#Globale Variablen
		#$csv - Array der Module, aus CSV importiert und formatiert
		#$year - Jahr
		global $csv, $year;
		
		$posts = array();
		#json-Array erstellen
		#WICHTIGE Anmerkung: Anscheinend kommt es zu Problemen beim Encoden, wenn die letzte Zeile der csv-Datei mit eingelesen wird. Dort gibt es ein Ue, das den Vorgang zerstört.
		#TODO: Es müsste somit eine Anpassung bei der Filterung der Datei erfolgen.
		for ($row = 0; $row < count($csv)-2; $row++){
			if($csv[$row]['Sem'] == $stud_sem['Sem']){
				$kw_start = $csv[$row]['LV-Start'];
				#Fall: LV-Start is set
				if($kw_start != NULL){
					$title = $csv[$row]['Modul'];
					$start_time	= setTimestamp($row, $kw_start, NULL, "1");
					$end_time = setTimestamp($row, $kw_start, NULL, "2");
					$modul_entry_start = array('title'=> $title,'start'=>$start_time,'end'=>$end_time);
					#Fall: Extra-Info steht in LV-Start, hier Turnus
					if(strlen((string)(float) filter_var($kw_start, FILTER_SANITIZE_NUMBER_FLOAT)) != 2){
						$posts = array_merge($posts, create_returning($modul_entry_start, 2));
					}
					#Fall: Normal
					else{
						$posts = array_merge($posts, create_returning($modul_entry_start, 1));
					}
				#Fall: LV-Start not set && Sondertermine is set	
				}else if($kw_start == NULL && $csv[$row]['Sondertermine'] != NULL ){
					$posts = array_merge($posts, create_sondertermine($row));					
				}
				
			}		
		}
		
		#Routine zum Erstellen einer JSON-Datei
		$file_name = $stud_sem['Studgang'].'_'.$year.'_'.$stud_sem['Sem'].'.json';
		$fp = fopen($file_name,'w');
		fwrite($fp, json_encode($posts));
		fclose($fp);
	}
	
	#Funktion zur Erstellung von wöchentlichen oder mehr-wöchentlichen Terminen
	#Input: 	$posts - Arrays aus Ersttermin-Events
	#			$turnus - Gibt den Wochenturnus an
	#Output: 	$returning_posts - Array aus Events vom Beginn bis zum Ende des Semesters
	function create_returning($entry, $turnus){
		
		#Timestamp-Variable, welche das Ende der Vorlesungszeit setzt
		global $end_lecture_time;		
		
		#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format
		$end_lecture_time_unix = strtotime($end_lecture_time);
		
		#Array zum Abspeichern der Event-Einträge		
		$returning_posts = array();
		
		#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format			
		$modul_start_date = strtotime($entry['start']);
		#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format
		$modul_end_date = strtotime($entry['end']);
		$title = $entry['title'];
			
		while($end_lecture_time_unix > $modul_start_date){
				
			#TODO: Check von Feiertagen und vorlesungsfreier Zeit im Semester (Weihnachten)
			#TODO: Check von Anpassungen des Turnus durch Sondertermin-Einschränkung
			$returning_posts[] = array('title'=> $title,'start'=>date(DATE_ISO8601, $modul_start_date),'end'=>date(DATE_ISO8601, $modul_end_date));
			
			#Erhöhung des Timestamp um (Sekunden pro Woche)*Turnus
			$modul_start_date += 604800*$turnus;
			$modul_end_date += 604800*$turnus;
		}
		
		#Rückgabe von Event-Einträgen
		return $returning_posts;
	}
	
	#Funktion zum Erstellen von Terminen anhand der angebenen Sondertermine
	#Input:		$row - Reihe im Array der Module, welche nur Sondertermine angebenen haben
	#Output:	$posts - Array aus Events für die angebenen Sondertermine
	function create_sondertermine($row){
		
		#Globale Variablen
		#$csv - Array der Module, aus CSV importiert und formatiert
		#$year - Jahr
		global $csv, $year;
		
		#Array zum Abspeichern der Termine
		$sondertermine_array = array();
		
		#Eintrag von Sonderterminen		
		$sondertermine_plan_text = $csv[$row]['Sondertermine'];	
		
		#Funkiton such nach dem verwendeten Datumsschema im Eintrag von Sonderterminen und speichert diese in einem Array
		preg_match_all('/[0-9]+[0-9]+[.]+[0-9]+[0-9]/', $sondertermine_plan_text ,$sondertermine_array);
		#Reduzierung des drei-dimensonalen Arrays in ein zwei-dimensonales Array 
		$sondertermine_array = $sondertermine_array[0];
		
		#Array zum Abspeichern der Event-Einträge
		$posts = array();
		
		#Titel-Deklaration des Moduls
		$title = $csv[$row]['Modul'];
		
		#Iteration über alle gefunden Sondertermine und dabei Erstellung des Events-Eintrag für den Sondertermin
		foreach ($sondertermine_array as $entry){
			$start_time	= setTimestamp($row, NULL, $entry, "1");
			$end_time = setTimestamp($row, NULL, $entry, "2");			
			$posts[]= array('title'=> $title,'start'=>$start_time,'end'=>$end_time);
		}
		
		#Rückgabe von Event-Einträgen
		return $posts;
		
	}

	#Was passiert, wenn der Button gedrückt wird
	if(isset($_POST["ausfuehren"])){
		create_json();
	}
?>

<form action="json_sem.php" method="post">
   <input type="submit" name="ausfuehren" value="JSON erstellen"/>
</form>
</body>
</html>
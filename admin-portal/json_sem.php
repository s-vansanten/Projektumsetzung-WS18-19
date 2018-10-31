<html>
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
	#		$start_or_end - Variable (Wert 1 oder 2, kann auf true/false angepasst werden), der bestimmt, ob die Startzeit- oder Endzeit-Spalte zur Berechnugn des Timestamps verwendet wird
	function setTimestamp ($row, $week, $start_or_end){		
		global $year, $csv;
		
		#Extrahieren von Zahlen aus dem String
		$week_no = (float) filter_var($week, FILTER_SANITIZE_NUMBER_FLOAT);
		#Sollten die Zahl mehr als zwei Zeichen haben, werden alle Ziffern außer die ersten beiden gelöscht
		#Dies kommt vor, wenn außer der Kalenderwoche auch noch Information zum Turnus (bei unser CSV 14-Tägigkeit) in der Zelle enthalten sind ((1 Jahr = 52,14 Wochen)
		if(strlen((string)$week_no) != 2){
			$week_no = substr($week_no, 0, 2);
		}
		
		#Berechnung des Beginn einer Kalenderwoche, Montags 0:00 Uhr
		$week_start = new DateTime();
		$week_start->setISODate($year,$week_no);
		
		#Umformatierung in UNIX-Timestamp zur einfachen Aufaddierung von Sekunden
		$date = strtotime($week_start->format('Y-m-d'));
		
		#Bestimmung, welche Spalte mit Startzeiten einen Wert enthält
		#$week_day beinhaltet wieviele Tage nach Montag der Timestamp gesetz werden soll
		if($csv[$row]['Mo B'] != NULL){
			$week_day = "0";
			if($start_or_end == "1"){
				$time = $csv[$row]['Mo B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Mo E'];
			}
		}else if($csv[$row]['Di B'] != NULL){
			$week_day = "1";
			if($start_or_end == "1"){
				$time = $csv[$row]['Di B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Di E'];
			}
		}else if($csv[$row]['Mi B'] != NULL){
			$week_day = "2";
			if($start_or_end == "1"){
				$time = $csv[$row]['Mi B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Mi E'];
			}
		}else if($csv[$row]['Do B'] != NULL){
			$week_day = "3";
			if($start_or_end == "1"){
				$time = $csv[$row]['Do B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Do E'];
			}
		}else if($csv[$row]['Fr B'] != NULL){
			$week_day = "4";
			if($start_or_end == "1"){
				$time = $csv[$row]['Fr B'];
			}else if($start_or_end == "2"){
				$time = $csv[$row]['Fr E'];
			}
		}else if($csv[$row]['Sa B'] != NULL){
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
		
		#Addieren von (Tage nach Montag*Sekunden pro Tag) auf den Timestamp
		$date += $week_day*86400;
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
	
	#Funktion zur Erstellung einer JSON-Datei
	function create_json(){
		
		global $csv, $year;
		
		#Herausfiltern, welche Semester alle in der CSV-Datei vertreten sind
		$semesters = array();
		foreach ($csv as $key => $values){
			if ( !(in_array($values['Sem'], $semesters)) && $values['Sem'] != NULL){
				array_push($semesters, $values['Sem']);
			}
		}
		
		#Aufruf von Unterfunktion zur Erstellung einer JSON-Datei für jedes vertretene Semester
		foreach ($semesters as $values){
			create_json_semester($values);
		}
		
	}
	
	#Funktion zur Erstellung einer JSON-Datei für ein bestimmtes Semester
	function create_json_semester($semester){
		
		global $csv, $year;
		
		$posts = array();
		#json-Array erstellen
		#WICHTIGE Anmerkung: Anscheinend kommt es zu Problemen beim Encoden, wenn die letzte Zeile der csv-Datei mit eingelesen wird. Dort gibt es ein Ue, das den Vorgang zerstört.
		#Es müsste somit eine Anpassung bei dewr Filterung der Datei erfolgen.
		for ($row = 0; $row < count($csv)-2; $row++){
			if($csv[$row]['Sem'] == $semester){
				$kw_start = $csv[$row]['LV-Start'];
				if($kw_start != NULL){
					$title = $csv[$row]['Modul'];
					$start_time	= setTimestamp($row, $kw_start, "1");
					$end_time = setTimestamp($row, $kw_start, "2");			
					$posts[]= array('title'=> $title,'start'=>$start_time,'end'=>$end_time);
				}	
			}		
		}
		
		$posts = create_returning($posts);
		
		#Routine zum Erstellen einer JSON-Datei
		$file_name = $csv[$row]['Studgang'].'_'.$year.'_'.$semester.'.json';
		$fp = fopen($file_name,'w');
		fwrite($fp, json_encode($posts));
		fclose($fp);
	}
	
	#Funktion zur Erstellung von wöchentlichen Terminen
	#Input: $posts - Arrays aus Ersttermin-Events
	#Output: $returning_posts - Array aus Events vom Beginn bis zum Ende des Semesters
	function create_returning($posts){
		
		#Timestamp-Variable, welche das Ende der Vorlesungszeit setzt
		global $end_lecture_time;
		
		#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format
		$end_lecture_time = strtotime($end_lecture_time);
		
		$returning_posts = array();
		foreach($posts as $values){
			
			#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format			
			$modul_start_date = strtotime($values['start']);
			#Umformatierung des ISO8601-Timestamp in UNIX-Timestamp-Format
			$modul_end_date = strtotime($values['end']);
			$title = $values['title'];
			
			while($end_lecture_time > $modul_start_date){
				$returning_posts[] = array('title'=> $title,'start'=>date(DATE_ISO8601, $modul_start_date),'end'=>date(DATE_ISO8601, $modul_end_date));
				
				$modul_start_date += 604800;
				$modul_end_date += 604800;
			}
		}
		
		return $returning_posts;
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
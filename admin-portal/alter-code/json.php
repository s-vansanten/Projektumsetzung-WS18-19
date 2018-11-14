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
      #print_r ($array);
    }
	
	function setTimestamp ($row, $week, $start_or_end){		
		global $year, $csv;
		
		$week_no = (float) filter_var($week, FILTER_SANITIZE_NUMBER_FLOAT);
		if(strlen((string)$week_no) != 2){
			$week_no = substr($week_no, 0, 2);
		}
		
		$week_start = new DateTime();
		$week_start->setISODate($year,$week_no);
		
		$date = strtotime($week_start->format('Y-m-d'));
		
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
		
		$time_splited = explode(".", $time);
		
		
		$date += $week_day*86400;
		$date += $time_splited[0]*3600;
		$date += $time_splited[1]*60;		
		
		$date = date(DATE_ISO8601, $date);		
		
		return $date;
	}
	
	filterArray($csv);
	
	$year = "2018";
	
	function create_json(){
		
		global $csv;
		
		$posts = array();
		#json-Array erstellen
		#WICHTIGE Anmerkung: Anscheinend kommt es zu Problemen beim Encoden, wenn die letzte Zeile der csv-Datei mit eingelesen wird. Dort gibt es ein Ue, das den Vorgang zerstört.
		#Es müsste somit eine Anpassung bei dewr Filterung der Datei erfolgen.
		for ($row = 0; $row < count($csv)-2; $row++){
			$kw_start = $csv[$row]['LV-Start'];
			if($kw_start != NULL){
				$title = $csv[$row]['Modul'];
				$start_time	= setTimestamp($row, $kw_start, "1");
				$end_time = setTimestamp($row, $kw_start, "2");			
				$posts[]= array('title'=> $title,'start'=>$start_time,'end'=>$end_time);
			}			
		}
		
		$fp = fopen('test.json','w');
		fwrite($fp, json_encode($posts));
		fclose($fp);
	}

	if(isset($_POST["ausfuehren"])){
		create_json();
	}
?>

<form action="json.php" method="post">
   <input type="submit" name="ausfuehren" value="JSON erstellen"/>
</form>
</body>
</html>
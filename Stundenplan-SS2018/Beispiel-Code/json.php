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
	
	filterArray($csv);
	#print_r ($csv);
	
	
	$response = array();
	$posts = array();
	#json-Array erstellen
	#WICHTIGE Anmerkung: Anscheinend kommt es zu Problemen beim Encoden, wenn die letzte Zeile der csv-Datei mit eingelesen wird. Dort gibt es ein Ue, das den Vorgang zerstört.
	#Es müsste somit eine Anpassung bei dewr Filterung der Datei erfolgen.
	for ($row = 0; $row < count($csv)-2; $row++){
		$title = $csv[$row]['Modul'];
		$posts[]= array('title'=> $title);		
	}
	
	
	print_r($posts);
	print_r("<br />\n");
	
	$fp = fopen('test.json','w');
	fwrite($fp, json_encode($posts));
	fclose($fp);
?>
</body>
</html>
<?php

#Definieren des Dateinamen
  $fileName = 'excel2.csv';

#Einlesen der Datei in ein Array
  $file = file($fileName);

#Array aus CSV Datei erstellen:
  $csv = array_map(function($v){return str_getcsv($v, detectDelimiter());}, $file);

#Associative Array erstellen:
  #csv[0] enthält die Spalten-Namen
    $arr_key = $csv[0];

  #array_splice entfernt diese erste Zeile dann
    array_splice ($csv,0,1);

  #Jedem Eintrag wird der dazugehörigen Spalten-Name als Key hinzugefügt, damit nicht nach Spalten-Nummer sondern nach Spalten-Name später gesucht werden kann
    for ($row = 0; $row < count($csv); $row++) {
      $csv[$row] = array_combine ($arr_key, $csv[$row]);
    }

#Event-Array erstellen
  $event = array();
  $event = getEvent($csv, $event);
#JSON aus $csv:
  $CalendarJSON = json_encode($event);

#Funktion zur Festellung des Trennzeichen der CSV-Datei
#wird beim Erstellen des Arrays aus der CSV-Datei verwendet
  function detectDelimiter() {

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

#Funktion zur Generierung der Events
  function getEvent($array, $ev) {
    foreach($array as $key => $values) {
      $modul = $array[$key]['Modul'];
      $week = $array[$key]['LV-Start'];
      #Note: 14 Als Platzhalter, da in CSV-Datei momentan nur schlecht einlesbare oder gar keine Werte ("ab 14. KW" statt "14").
      $week = "14";
      if($array[$key]['Mo B'] != NULL) {
        $start = $array[$key]['Mo B'];
        $end = $array[$key]['Mo E'];
        $day = 1;
        $newEvent = array(
          'title' => $modul,
          'start' => getTimestamp($week, $day, $start),
          'end' => getTimestamp($week, $day, $end)
        );
        array_push($ev, $newEvent);
      }
      if($array[$key]['Di B'] != NULL) {
        $start = $array[$key]['Di B'];
        $end = $array[$key]['Di E'];
        $day = 2;
        $newEvent = array(
          'title' => $modul,
          'start' => getTimestamp($week, $day, $start),
          'end' => getTimestamp($week, $day, $end)
        );
        array_push($ev, $newEvent);
      }
      if($array[$key]['Mi B'] != NULL) {
        $start = $array[$key]['Mi B'];
        $end = $array[$key]['Mi E'];
        $day = 3;
        $newEvent = array(
          'title' => $modul,
          'start' => getTimestamp($week, $day, $start),
          'end' => getTimestamp($week, $day, $end)
        );
        array_push($ev, $newEvent);
      }
      if($array[$key]['Do B'] != NULL) {
        $start = $array[$key]['Do B'];
        $end = $array[$key]['Do E'];
        $day = 4;
        $newEvent = array(
          'title' => $modul,
          'start' => getTimestamp($week, $day, $start),
          'end' => getTimestamp($week, $day, $end)
        );
        array_push($ev, $newEvent);
      }
      if($array[$key]['Fr B'] != NULL) {
        $start = $array[$key]['Fr B'];
        $end = $array[$key]['Fr E'];
        $day = 5;
        $newEvent = array(
          'title' => $modul,
          'start' => getTimestamp($week, $day, $start),
          'end' => getTimestamp($week, $day, $end)
        );
        array_push($ev, $newEvent);
      }
      if($array[$key]['Sa B'] != NULL) {
        $start = $array[$key]['Sa B'];
        $end = $array[$key]['Sa E'];
        $day = 6;
        $newEvent = array(
          'title' => $modul,
          'start' => getTimestamp($week, $day, $start),
          'end' => getTimestamp($week, $day, $end)
        );
        array_push($ev, $newEvent);
      }
    }
    return $ev;
  }

#Funktion zur Generierung des Zeitstempels
  function getTimestamp($week, $day, $time) {
    $year = 2018;

    $var = 'Y'.$year.'W'.$week.$day;

    $event = date('U', strtotime($var.'+'.$time.'hours-12hours'));

    return $event;
  }

#JSON aus $csv:
  $CalendarJSON = json_encode($event);
?>

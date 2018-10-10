<html>
  <head>
    <title>Stundenplan</title>

    <meta charset="utf-8">
    <style type="text/css">



      tr {
      table-layout: fixed;
      height: 2em;
      /*Innenabstand*/
      margin: 0px;
      /*Außenabstand*/
      padding: 0.1em 0.3em;
      border-radius: 0.1em;
      border-color:#006699;
      }
      td {

      table-layout: fixed;
      height:2em;
      width: 17em;
      /*Innenabstand*/
      margin:0px;
      /*Außenabstand*/
      padding: 0.1em 0.3em;
      border-radius: 0.1em;
      box-shadow: inset 1px 3px 5px -3px rgba(0, 0, 0, 0.1);

      }

      table {
      table-layout: fixed;
      background-color: #FFFFFF ;

      border-collapse: separate;
      border-spacing: 0.1em;
      empty-cells: show;

      border-color: #006699 ;
      }
      tbody tr:nth-child(even) {
      background-color: #E1EEF4;
      color: #000;
      }

 			#zelle {
                             table-layout: fixed;
 				height: 2em;
 		                width: 15em;/*???*/
 				padding:0px;         /*Innenabstand*/
 				margin:0px;          /*Außenabstand*/
 				}

 			#termin {
                             table-layout: fixed;
 				width: 50%;
 				position: relative;
 				top: 2.25em;
 				height: 4.5em;
 				background-color: #FFFF00 ;
 				overflow: hidden;
 				padding:5px;         /*Innenabstand*/
 				margin:0px;          /*Außenabstand*/
 				-moz-border-radius: 2px;
 				-webkit-border-radius: 2px;
 				-ms-border-radius: 2px;
 				-o-border-radius: 2px;
 				-khtml-border-radius: 2px;
 				border-radius: 2px;
 				}
 			#termin:focus { /*Für Elemente, die den Fokus erhalten, z.B. durch "Durchsteppen" mit der Tabulator-Taste (CSS 2.0)*/
 				overflow:visible;
 				height: auto;
 				}
 			#termin:hover { /* für Elemente, während der Anwender mit der Maus darüber fährt (CSS 2.0)*/
 				overflow:visible;
 				height: auto;
 				}
 			#termin:active { /*für gerade angeklickte Elemente*/
 				overflow:visible;
 				height: auto;
 				}

      fieldset { border: 2px solid #C5D8E1;
      border-radius: 6px;
      background: white;
      color:#006699;
      }

      thead{
      background-color:#006699  ;
      color:#FFFFFF;
      }

      .anzeigedatum {
      padding: 40px 25px;
      width: 97.3%;
      height: 10%;
      background: #006699;
      text-align: center;

      }

      .anzeigedatum ul {
      margin: 0;
      padding: 0;
      }

      .anzeigedatum ul li {
      color: white;
      font-size: 20px;
      text-transform: uppercase;
      letter-spacing: 3px;
      }

      .anzeigedatum .zurueck {
      float: left;
      padding-top: 10px;
      }

      .anzeigedatum .vor {
      float: right;
      padding-top: 10px;
      }
    </style>
  </head>
  <body bgcolor ='#FFFFFF'>
    <H1><FONT COLOR="006699"><CENTER>Stundenplan</FONT></H1>

    <div class="content">
    <div style="font-size: 1.2em" >
      <form action="stundenplan.php" method="get" name="timetable">
        <fieldset >
          <legend>Stundenpläne für den Fachbereich 3</legend>
          <span>Kurse:</span>
          <!--/* todo css select-->
          <select name='course'>
          <option value='select' selected="selected">Studiengang wählen</option>
          <option value='VI'>Verwaltungsinformatik</option><option value='OV'>Öffentliche Verwaltung</option>
          </select>

          <script type="text/javascript">
          var kurse =[[["semester1 - Verwaltungsinformatik.html"],["semester2 - Verwaltungsinformatik.html"],["semester3 - Verwaltungsinformatik.html"],["semester4 - Verwaltungsinformatik.html"],["semester6 - Verwaltungsinformatik.html"]]];
          var ordnerpfad =".\/fb3-stundenplaene";
          </script>
          <select name='semester'>
          <option value='select' selected='selected'>Semester wählen</option>
          <option value = 2>2</option><option value = 4>4</option><option value=6>6</option>
          </select>

          <input name = "submit" type = "submit" value = "Anzeigen">
          </fieldset>
      </form>
    </div>

    <div class="anzeigedatum">
      <ul>
        <li class="zurueck">&#10094;</li>
        <li class="vor">&#10095;</li>
        <li>
          Juni<br>
          <span style="font-size:18px">2018</span><br>
          <span style="font-size:15px">11.06. - 17.06</span>
        </li>
      </ul>
    </div>

    <?php    

    #Definieren des Dateinamen
    $fileName = 'excel.csv';
    #Einlesen der Datei in ein Array
    $file = file($fileName);

    #Abfangen des Fehler beim Start der Seite, wenn Kurs und Semester noch nicht gesetzt wurden
    if (isset($_GET['semester'])){
		  $semester = $_GET['semester'];
    }
    if(isset($_GET['course'])){
    	$course = $_GET['course'];
    }

    #Variable zum Blockieren der Zellen-Erstellung sollte in der gleichen Spalte vorher eine über mehrere Zeilen spannende Zelle erstellt worden sein    
    $blockCount = array(0,0,0,0,0,0);

    #Array aus CSV Datei erstellen:
    $csv[] = array_map(function($v){return str_getcsv($v, detectDelimiter());}, $file);

    #Löschen des durch das Einlesen erstelle obere Array (csv[0] enthält das Array mit den CSV-Daten, csv[1] existiert nicht)
    $csv = $csv[0];

    #Hinweis: Im Delete Loop im Grunde obsolet..
		#Array zum Löschen einzelner Spalten (columns);
    #Zeilenweises (rows) Löschen funktioniert noch nicht:
		$del_col = array ();

		#Delete Loop:
		for ($row = 0; $row < count($csv); $row++) {
			for ($element = 0; $element < count($del_col); $element++) {
				array_splice($csv[$row], $del_col[$element]-$element, 1);
			}
		}

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
      if ( $values['Studgang'] != $course ) {
        ?>
        <script>
          alert("Momentan nur Verwaltungsinformatik möglich!");
        </script>
        <?php
      }
      $array = $arr_sl;
      #print_r ($array);
    }

    #Funktion zur Erstellung einer Tabellenzeile für jede Stunde:
    #hour: Uhrzeit, für die eine Zeile erstellt werden soll
    #table: modifizierte CSV-Daten
    function rowForHour($hour, $table){

      #Variablen
      #set: Variable zum Überprüfen, ob eine leere Zelle erstellt werden muss
      #blockCount: Variable zum Blockieren der Zellen-Erstellung sollte in der gleichen Spalte vorher eine über mehrere Zeilen spannende Zelle erstellt worden sein
      #kann bei vorherigem Aufruf der Funktion für eine andere Uhrzeit gesetzt worden sein
      #semester: Semester, welches angezeigt werden soll
      $set = false;
      global $blockCount, $semester;

      #Schreiben der Uhrzeit:
      echo"<tr><td>".$hour.":00</td>";

      #Montag:
      #Sollte vorher keine größere Tabellezelle bei einer früheren Uhrzeit erstellt worden sein, wird folgendes gemacht:
      if($blockCount[0] == 0) {

        #Geht alle Zeilen(und somit Modul-Einträge) durch
        for($row = 0; $row < count($table); $row++) {

          #Sollte der Tag und die Zeit des Modul mit der Tabellenzelle übereinstimmen,
          if($table[$row]['Mo B'] == $hour+0.00 && $table[$row]['Sem'] == $semester) {

            #wird die Größe der Zelle über die Subtraktion der End- von der Start-Uhrzeit ermittelt
            $span = $table[$row]['Mo E'] - $table[$row]['Mo B'];
            #und eine Tabellenzelle über diese Zeilengröße in der Spalte erstellt
            echo "<td style ='background-color: #6699cc' rowspan=",$span,">",$table[$row]['Modul']," ",$table[$row]['Art'],"</td>";
            #Es wird die Überprüfungsvariable als true gesetzt
            $set = true;

            #und die Blockier-Variable wird auf die Zellengröße-1 gesetzt
            $blockCount[0] = $table[$row]['Mo E'] - $table[$row]['Mo B']-1;

            #Die for-Schleife wird bei erfolgreicher Erstellung einer Zelle abgebrochen
            break;
          }
        }

        #Es wird überprüft, ob eine Zelle erstellt wird
        #Ist dies der Fall, wird die Überprüfungsvariable zurückgesetzt
        #Ist dies nicht der Fall, wird eine leere Tabellenzelle erstellt
        if(!$set){
          echo "<td> </td>";
        }
        else{
          $set = false;
        }
      }
      #Sollte eine größere Tabellenzelle bei einer früheren Uhrzeit erstellt worden sein,
      #wird die Zahl der Blockiervariable für den Tag um 1 verringert
      else{
        $blockCount[0]--;
      }

      #Dienstag:
      if($blockCount[1] == 0) {

        for($row = 0; $row < count($table); $row++) {

          if($table[$row]['Di B'] == $hour+0.00 && $table[$row]['Sem'] == $semester) {

            $span = $table[$row]['Di E'] - $table[$row]['Di B'];
            echo "<td style ='background-color: #6699cc' rowspan=",$span,">",$table[$row]['Modul']," ",$table[$row]['Art'],"</td>";
            $set = true;

            $blockCount[1] = $table[$row]['Di E'] - $table[$row]['Di B']-1;


            break;
          }
        }

        if(!$set){
          echo "<td> </td>";
        }
        else{
          $set = false;
        }
      }

      else{
        $blockCount[1]--;
      }

      #Mittwoch:
      if($blockCount[2] == 0) {

        for($row = 0; $row < count($table); $row++) {

          if($table[$row]['Mi B'] == $hour+0.00 && $table[$row]['Sem'] == $semester) {

            $span = $table[$row]['Mi E'] - $table[$row]['Mi B'];
            echo "<td style ='background-color: #6699cc' rowspan=",$span,">",$table[$row]['Modul']," ",$table[$row]['Art'],"</td>";
            $set = true;

            $blockCount[2] = $table[$row]['Mi E'] - $table[$row]['Mi B']-1;


            break;
          }
        }

        if(!$set){
          echo "<td> </td>";
        }
        else{
          $set = false;
        }
      }

      else{
        $blockCount[2]--;
      }

      #Donnerstag:
      if($blockCount[3] == 0) {

        for($row = 0; $row < count($table); $row++) {

          if($table[$row]['Do B'] == $hour+0.00 && $table[$row]['Sem'] == $semester) {

            $span = $table[$row]['Do E'] - $table[$row]['Do B'];
            echo "<td style ='background-color: #6699cc' rowspan=",$span,">",$table[$row]['Modul']," ",$table[$row]['Art'],"</td>";
            $set = true;

            $blockCount[3] = $table[$row]['Do E'] - $table[$row]['Do B']-1;


            break;
          }
        }

        if(!$set){
          echo "<td> </td>";
        }
        else{
          $set = false;
        }
      }

      else{
        $blockCount[3]--;
      }

      #Freitag:
      if($blockCount[4] == 0) {

        for($row = 0; $row < count($table); $row++) {

          if($table[$row]['Fr B'] == $hour+0.00 && $table[$row]['Sem'] == $semester) {

            $span = $table[$row]['Fr E'] - $table[$row]['Fr B'];
            echo "<td style ='background-color: #6699cc' rowspan=",$span,">",$table[$row]['Modul']," ",$table[$row]['Art'],"</td>";
            $set = true;

            $blockCount[4] = $table[$row]['Fr E'] - $table[$row]['Fr B']-1;


            break;
          }
        }

        if(!$set){
          echo "<td> </td>";
        }
        else{
          $set = false;
        }
      }

      else{
        $blockCount[4]--;
      }

      #Samstag:
      if($blockCount[5] == 0) {

        for($row = 0; $row < count($table); $row++) {

          if($table[$row]['Sa B'] == $hour+0.00 && $table[$row]['Sem'] == $semester) {

            $span = $table[$row]['Sa E'] - $table[$row]['Sa B'];
            echo "<td style ='background-color: #6699cc' rowspan=",$span,">",$table[$row]['Modul']," ",$table[$row]['Art'],"</td>";
            $set = true;

            $blockCount[5] = $table[$row]['Sa E'] - $table[$row]['Sa B']-1;


            break;
          }
        }

        if(!$set){
          echo "<td> </td>";
        }
        else{
          $set = false;
        }
      }

      else{
        $blockCount[5]--;
      }

      #Abschluss der Zeile:
      echo "</tr>";
    }

    #print_r zum Test:
    #print_r ($csv);

    #JSON aus $csv:
    $csv_JSON = json_encode($csv);
    #echo $csv_JSON;
    ?>

    <table>
      <tr>
      <td>Zeit</td>
      <td>Montag</td>
      <td>Dienstag</td>
      <td>Mittwoch</td>
      <td>Donnerstag</td>
      <td>Freitag</td>
      <td>Samstag</td>
      </tr>

      <?php
      filterArray($csv);
      #Schleife für die Stunden von 8 bis 20 Uhr
      for($i = 8; $i <= 21; $i++){
        rowForHour($i, $csv);
      }

      ?>
    </table>

<!--
    <style>
      table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
      }
    </style>

    <table>

    <?php
		#Erzeugen der HTML-Tabelle:

    echo "<tr>";
    #Beide foreach Schleifen funktionieren,
    #arr_key bzw. csv[0] für Table Header:
    #foreach ($arr_key as $key) {
    foreach ($csv[0] as $key => $value) {
      echo "<th>" . $key . "</th>";
    }
    echo "</tr>";
    for ($row = 0; $row < count($csv); $row++) {
      echo "<tr>";
        foreach ($csv[$row] as $key => $value) {
          echo "<td>" . $value . "</td>";
        }
      echo "</tr>";
    }
    ?>
    </table>
-->
  </body>
</html>

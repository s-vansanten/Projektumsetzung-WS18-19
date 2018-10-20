

<?php
 
// Konfiguration
$csvFile = "VI.csv";
$firstRowHeader = true;
$maxRows = 40;
 
// Daten auslesen und Tabelle generieren
$handle = fopen($csvFile, "r");
$counter = 0;
echo "<table class=\"csvTable\">";
while(($data = fgetcsv($handle, 999, ";")) && ($counter < $maxRows)) {
 
 
		
		
		
  echo "<tr>";
  if(($counter == 0) && $firstRowHeader) {
    echo "<th>".$data[3]."</th>";
  echo "<th>".$data[17]."</th>";
    echo "<th>".$data[18]."</th>"; 
	  echo "<th>".$data[19]."</th>";
  echo "<th>".$data[20]."</th>";
    echo "<th>".$data[21]."</th>"; 
	  echo "<th>".$data[22]."</th>";
  echo "<th>".$data[23]."</th>";
    echo "<th>".$data[24]."</th>"; 
	  echo "<th>".$data[25]."</th>";
  echo "<th>".$data[26]."</th>";
    echo "<th>".$data[27]."</th>"; 
	  echo "<th>".$data[27]."</th>";
  echo "<th>".$data[28]."</th>";

	
  }
  else {
    echo "<td>".$data[3]."</td>";
    echo "<td>".$data[17]."</td>";
    echo "<td>".$data[18]."</td>"; 
		  echo "<th>".$data[19]."</th>";
  echo "<th>".$data[20]."</th>";
    echo "<th>".$data[21]."</th>"; 
	  echo "<th>".$data[22]."</th>";
  echo "<th>".$data[23]."</th>";
    echo "<th>".$data[24]."</th>"; 
		  echo "<th>".$data[25]."</th>";
  echo "<th>".$data[26]."</th>";
    echo "<th>".$data[27]."</th>"; 
	  echo "<th>".$data[27]."</th>";
  echo "<th>".$data[28]."</th>";

   
	
  }
  echo "</tr>";
 
  $counter++;
}
echo "</table>"; 
 
fclose($handle);
 
?>
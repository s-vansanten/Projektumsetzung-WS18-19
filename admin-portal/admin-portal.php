<!DOCTYPE HTML>
<html>
<head>
<link href='style.css' rel='stylesheet' />
<title>Admin-Portal der HWR</title>
<script>

var button = document.querySelector('vorschau');
button.addEventListener('click', testFunction());

</script>
</head>
<body>


<figure> <img src="https://upload.wikimedia.org/wikipedia/de/9/90/Hochschule_f%C3%BCr_Wirtschaft_und_Recht_Berlin_logo.svg" width="604" height="120"
	alt="Logo">
	
</figure>
<form action="admin-portal-login.html" method="post" id="logout">
    <main>
        <div>
<button type="submit" id="logoutbutton" style="float: right;">Abmelden</button>
        </div>    
    </main>
</form>    
<br></br>
<br></br>
<h1>Herzlich Willkommen!</h1> 
<br></br>

<nav>
	<ul>
     
        <li><a href="1">Register</a>
			<ul>
				<li><a href="2">Studieng&aumlnge</a> <!-- Import nach dem Auswählen -->
				<ul>
			
				<li><a href="#" onclick="showButtons()">Verwaltungsinformatik</a> <!-- Import nach dem Auswählen -->
			</ul>
			</ul>
        </li>
		<li><a href="#">Versionskontrolle</a>
		</li>
	</ul> 
	
</nav>

<br>
<br>
<!-- Datei auswählen Button -->
<form method="post" enctype="multipart/form-data">
	<form  method="post" enctype="multipart/form-data">
    Select CSV file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload CSV" name="submit">
	</form>

  
  <br>
  <br>
  

  </div>
</form>

<!-- Vorschau Button , jpeg oder pdf -->
<form action="admin-portal.php" method="post">
   <input type="submit" name="vorschau" value="Vorschau"/>
</form>

<div id="myDIV">
  Fullcalender Vorschau
 
</div>


<?php
	$target_dir = "uploads/";
    if(isset($_FILES["fileToUpload"]["name"])){
      $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    }
    $uploadOk = 1;
    if(isset($target_file)){
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

      // Check if file already exists
      if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
      }
    }

    
    if(isset($_FILES["fileToUpload"]["size"])){
      // Check file size
      if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
      }
    }
    if(isset($imageFileType)){
      // Allow certain file formats
      if($imageFileType != "csv") {
        echo "Sorry, only CSV files are allowed.";
        $uploadOk = 0;
      }
    }
    if(isset($_FILES["fileToUpload"]["tmp_name"]) and isset($target_file)){
      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
      // if everything is ok, try to upload file
      } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          echo '<script type="text/javascript">alert("The file has been uploaded")</script>';
        } else {
          echo '<script type="text/javascript">alert("Sorry, there was an error uploading your file.")</script>';
        }
      } 
    }
	
	$fileName = NULL;
	
	function create_print(){
		
		global $fileName;
		#Definieren des Dateinamen
		$fileName = 'uploads/excel.csv';
		#Einlesen der Datei in ein Array
		$file = file($fileName);

		#Array aus CSV Datei erstellen:
		$csv[] = array_map(function($v){return str_getcsv($v, detectDelimiter());}, $file);

		#Löschen des durch das Einlesen erstelle obere Array (csv[0] enthält das Array mit den CSV-Daten, csv[1] existiert nicht)
		$csv = $csv[0];
		
		foreach($csv as $key => $values)
		{
			foreach ($values as $key => $entries){
				echo $entries;
				echo " ";
			}
			echo "<br />";
		}

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
	
	
	if(isset($_POST["vorschau"])){
		create_print();
	}
	
	

?>

</body>
<html>

<!DOCTYPE HTML>
<html>
<head>
 <link href='style.css' rel='stylesheet' /> 
<title>Admin-Portal der HWR</title>
<script>



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
			
				<li><a href="#" onclick="showButtons()">Verwaltungsinformatik</a> <!-- klickt man auf VI, kommt die Vorsch 2,4,6 ->Vorschau verknüpfen -->
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
	<form  method="post" enctype="multipart/form-data">
    Select CSV file to upload and create JSON files from:
    <input type="file" name="fileToUpload" id="fileToUpload">
	<p>Letzer Vorlesungstag : <input type="date" name="end_lecture_time_input" /></p>
	<p>Start Vorlesungspause : <input type="date" name="lecture_free_time_start_input" /></p>
	<p>Ende Vorlesungspause : <input type="date" name="lecture_free_time_end_input" /></p>
    <input type="submit" value="Upload CSV" name="submit">
	</form>

  
  <br>
  <br>
  
  <form action="admin-portal.php" method="post">
   <input type="submit" name="vorschau" value="Vorschau"/>
</form>

<?php
#$selectedValue = $_POST['Auswahl'];
?>  


<?php

	
	
	include 'create_json.php';
	
	$target_file = NULL;
	$target_dir = "uploads/";
    if(isset($_FILES["fileToUpload"]["name"])){
		$target_file = $target_dir .date("Y-m-d_His")."_". basename($_FILES["fileToUpload"]["name"]);
		
		#Vorlesungsfreie Zeit - wird später durch Admin-Eingabe gesetzt
		$lecture_free_time_start = strtotime($_POST['lecture_free_time_start_input']);
		$lecture_free_time_end = strtotime($_POST['lecture_free_time_end_input']);
		
		#Vorlesungszeit-Ende - sollte später entweder durch Auslesen aus der CSV oder durch Admin-Eingabe gesetzt werden
		$end_lecture_time = strtotime($_POST['end_lecture_time_input']);
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
			start_create($target_file); /*startfunktion*/
			echo '<script type="text/javascript">alert("The file has been uploaded, events are creating and stored in JSON file.")</script>';		  
        } else {
			echo '<script type="text/javascript">alert("Sorry, there was an error uploading your file.")</script>';
        }
      } 
    }	
	
	if(isset($_POST['vorschau'])){
		
		global $events_dir; /* event ordner */
		echo select_files($events_dir, 'vorschau_menu', 'Vorhandene Event-Dateien: ', 30);
	}
	
	#https://www.tutdepot.com/create-a-select-menu-from-files-or-directory/
	function select_files($dir, $select_name, $label = '', $curr_val = '', $mlength = 30) {
		if ($handle = opendir($dir)) {
			$mydir = '<form method="post" action="admin-portal.php">';
			if ($label != '') $mydir .= '
				<label for="'.$select_name.'">'.$label.'</label>';
			$mydir .= '
				<select name="'.$select_name.'" id="'.$select_name.'">';
			$curr_val = (isset($_REQUEST[$select_name])) ? $_REQUEST[$select_name] : $curr_val;
			if ($curr_val == '') {
				$mydir .= '
				<option value="" selected="selected">...</option>';
			} else {
				 $mydir .= '
				 <option value="">...</option>';
			while (false !== ($file = readdir($handle))) {
				$files[] = $file;
			}
			closedir($handle);
			sort($files);
			$counter = 0;
			foreach ($files as $val) {
				if (is_file($dir.$val)) { // show only "real" files
					$mydir .= '
				<option value="'.$val.'"';
					if ($val == $curr_val) $mydir .= ' selected="selected"';
					$name = (strlen($val) > $mlength) ? substr($val, 0, $mlength).'...' : $val.'';
					$mydir .= '>'.$name.'</option>';
					$counter++;
				}
			}
			$mydir .= '
				</select><input type="submit" value="Auswählen" name="vorschau_menu_submit"/></form>';
			}
			if ($counter == 0) {
				$mydir = 'No files!';
			} else {
				return $mydir;
			}
		}
	}
	
	
	
	if(isset($_POST['vorschau_menu_submit'])){
		global $events_dir;
		echo 'Ausgewählt ist '.$_POST['vorschau_menu'].'</br></br>';
		print_r(json_encode(file_get_contents($events_dir.''.$_POST['vorschau_menu']),JSON_PRETTY_PRINT));
		echo '</br></br></br></br>';
	}
	
?>
</body>
<html>

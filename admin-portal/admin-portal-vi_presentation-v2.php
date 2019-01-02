<!DOCTYPE HTML>
<html>
<head>
<link href='../admin-portal/style.css' rel='stylesheet' />
<link href='style/adminmainstyle.css' rel='stylesheet' />

<title>Admin-Portal der HWR</title>

</head>
<body>



<form action="admin-portal-login-presentation-v2.php" method="post" id="logout">
    <main>
        <div  >
<button type="submit" id="logoutbutton" >Abmelden</button>
        </div>    
    </main>
</form>  


<!-- <blockquote style="border-style: solid; border-color:#b5b5b5; border-width: 1px; background-color:white;"> -->
<!-- admin portal zur studienplanung als überschrift,
upload button name in importieren -->
<div id="blockquote">
<figure> <img src="https://upload.wikimedia.org/wikipedia/de/9/90/Hochschule_f%C3%BCr_Wirtschaft_und_Recht_Berlin_logo.svg" width="15%" height="15%"
	alt="Logo">
	</figure>
<br></br>
 
<div class="titleüberschriften" >
<div id="ü1" >
<h1>Herzlich Willkommen!</h1> 
</div>
<div id="ü2" >
<h2>Admin-Portal für die Lehrplanung</h2>
</div>
<br></br>
</div>
</div>
 <!--</blockquote>  -->
 
 
<div id="blockquote2">
<br> </br>



<!-- Datei auswählen Button -->
	<form  method="post" enctype="multipart/form-data">
    <!-- Select CSV file to upload and create JSON files from: -->
    <input type="file" name="fileToUpload" id="fileToUpload">
		<span class="tooltip" tabindex="0" data-tooltip="Bitte Excel-Datei auswählen"> ? </span>
	<div class="menu" style="margin-left:10px;">

	<p style="font-size:1.3em;">Letzer Vorlesungstag: <input id="levo" type="date" name="end_lecture_time_input" /></p>
	<p style="font-size:1.3em;">Start Vorlesungspause: <input id="stvo" type="date" name="lecture_free_time_start_input" /></p>
	<p style="font-size:1.3em;">Ende Vorlesungspause: <input id="evo" type="date" name="lecture_free_time_end_input" /></p>
			<span class="tooltip" tabindex="0" data-tooltip="Bitte die Daten des Vorlesungszeitraumes eingeben"> ? </span>
	</div>
	<div class="buttons" >

<br>
	
	<div class="b2" >
    <input id="btnimportieren" type="submit" value="Importieren" name="submit">

		<!--span class="tooltip" tabindex="0" data-tooltip="Gewünschte Datei hochladen" style="float:right">?</span-->

	</div>
	</form>

</div>
<br><br>
<br><br>


<input type="button" id="test_start"  value='Vorschau' onclick="javascript:toggle();";>
	<span class="tooltip" tabindex="0" data-tooltip="Kalender anzeigen"> ? </span>
	</div>
	<div id="test">
	<?php
		include 'admin_calendar_presentation.php';
	?>			
</div>



<script language="javascript">

    function toggle(){

        var ele = document.getElementById("test");

        if (ele.style.display == "block") {

            ele.style.display = "none";
        }
        else {

            ele.style.display = "block";
        }
    }
</script>

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
			start_create($target_file);
			echo '<script type="text/javascript">alert("The file has been uploaded, events are creating and stored in JSON file.")</script>';		  
        } else {
			echo '<script type="text/javascript">alert("Sorry, there was an error uploading your file.")</script>';
        }
      } 
    }
	
	#https://www.tutdepot.com/create-a-select-menu-from-files-or-directory/
	function select_files($dir, $select_name, $label = '', $curr_val = '', $mlength = 30) {
		if ($handle = opendir($dir)) {
			$mydir = '<form method="post" action="admin-portal-1.php">';
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

<html>
<head>

</head>
<body>
	<form action="upload.php" method="post" enctype="multipart/form-data">
    Select CSV file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload CSV" name="submit">
	</form>

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

    #
    $str_path = 'uploads/';
    $cls_rii =  new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator( $str_path ),
      \RecursiveIteratorIterator::CHILD_FIRST
    );
    $ary_files = array();
    foreach ( $cls_rii as $str_fullfilename => $cls_spl ) {
      if($cls_spl->isFile()){
        $ary_files[] = $str_fullfilename;
      }
    }

    $ary_files = array_combine($ary_files,array_map( "filemtime", $ary_files ));
    arsort( $ary_files );
    $str_latest_file = key( $ary_files );

    echo"Letzte hochgeladene CSV-Datei: "; 
	echo $str_latest_file; 
?>
</body>
</html>

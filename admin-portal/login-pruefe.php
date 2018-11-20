 <html>
  <head>
   <title>Pruefe Login</title>
  </head>
<?php
$username = $_POST["username"];
$passwort = $_POST["password"];
 
$pass = sha1($passwort);
 
if($username == "Lade" AND $pass=="38a54b2fecf34f57b017694db2222758fa2ad59c")
   {
   
   header ('Location: admin-portal.php');
   
   }
else
   {
    echo "<script language=\"JavaScript\">
<!--
 alert(\"Login Fehlgeschlagen!\");
//-->
</script>
";
   
   header ('Location: admin-portal-login-fehlgeschlagen.php');
   }
?>
</html>
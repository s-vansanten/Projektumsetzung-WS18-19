<!DOCTYPE HTML>
<html>
<head>
<link href='style.css' rel='stylesheet' />
<title>Admin-Portal der HWR</title>

<!-- <script>
				function pruefe(f){
					var passwort = "hwr" ;
					var name = "Lade" ;
					var text = "Falscher Kennwort!";
					if (f.username.value == name && f.password.value == passwort){
						return true ;
					}
					else{
						alert(text);
						return false ;
					}
			}
			</script> -->

 <blockquote style="border-style: solid; border-color:#b5b5b5; border-width: 1px; "> 

<link rel="shortcut icon" href="//moodle.hwr-berlin.de/pluginfile.php/1/theme_boost_campus/favicon/1539771992/favicon.png" />
</head>
<body>
<figure> <img src="https://upload.wikimedia.org/wikipedia/de/9/90/Hochschule_f%C3%BCr_Wirtschaft_und_Recht_Berlin_logo.svg" width="604" height="120"
	alt="Logo">
	</figure>
	</blockquote>


 <blockquote style="border-style: solid; border-color:#b5b5b5; border-width: 1px; ">	
 <div class="menu" style="margin-left:20px;color:#4A4D50;">
	<h1>Admin-Portal der Hochschule f&uumlr Wirtschaft und Recht</h1>
	<br>
	
<main>
<div class="menu" style="margin-left:10px;">
	<form action="login-pruefe.php"  method="post" id="login">
		<table>
			<tbody>
				<tr>
					<th>
						
						<label for="username" class="sr-only">
                                            
                                    </label>
						
					</th>
					<td>
						<input type="text" name="username" id="username" required
                                           class="form-control"
                                           value=""
                                            placeholder="Anmeldename">
						
				</tr>
				<tr>
					<th>
						
						<label for="password" class="sr-only"></label>
						
					</th>
					<td>
						<input type="password" name="password" id="password" required value=""
                                           class="form-control"
                                            placeholder="Kennwort">
						
				</tr>
				<tr>
					<td> </td>
					<td>
						<button type="submit" id="loginbutton" >Anmelden</button>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<br>
	<br>
	</div>
	</blockquote>
</main>

<style>

/** Hintergrund  **/
body {
	background-color: #fefefe ;}

blockquote{blockquote {
   padding: 5px;
   margin-top: 5px;
   width: 80%;
   margin-left: auto;
   margin-right: auto;
   margin-bottom: 1.2em;
   font-family: UnBatang, FreeSerif, Georgia, serif;
   font-size: 1.2em;
   background-color: white;
}
blockquote i {
   color: blue;
}

title{
	text-align: center;
}


	
</style>
</body>
<html>

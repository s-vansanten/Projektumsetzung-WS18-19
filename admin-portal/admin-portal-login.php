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



<link rel="shortcut icon" href="//moodle.hwr-berlin.de/pluginfile.php/1/theme_boost_campus/favicon/1539771992/favicon.png" />
</head>
<body>
<figure> <img src="https://upload.wikimedia.org/wikipedia/de/9/90/Hochschule_f%C3%BCr_Wirtschaft_und_Recht_Berlin_logo.svg" width="604" height="120"
	alt="Logo">
	
</figure>

<h1>Admin-Portal der Hochschule f&uumlr Wirtschaft und Recht</h1>
<main>
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
</main>
</body>
<html>
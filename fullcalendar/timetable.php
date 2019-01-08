<html>
    <head>
  
	<div id="box1"> 
	<figure id="logopic"> <img src="https://upload.wikimedia.org/wikipedia/de/9/90/Hochschule_f%C3%BCr_Wirtschaft_und_Recht_Berlin_logo.svg" width="604" height="120"
	alt="Logo">        
</figure>
            <meta name="viewport" content="width=device-width, initial-scale=1">
        </head>
         <body>
		 
		 
              
           <div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&#9776;</a>
  
  <span id="modulname">Verwaltungsinformatik:</span>

  <form name= "SemesterAuswahl" method="get">
  <select id="dropdown" name= "AuswahlSelect" onchange="this.form.submit()"> 
                    <option value="Semester" data-feed=""selected>Semester wählen</option>
                     <?php
					#https://www.tutdepot.com/create-a-select-menu-from-files-or-directory/					
					$dir =  "../admin-portal/events/";
					$handle = opendir($dir);
					while (false !== ($file = readdir($handle))) {
						$files[] = $file;
					}
					closedir($handle);
					sort($files);

					#TODO: Hier Ansortierung von Dateien, welche nicht mehr notwendig sind
					#das heißt: keine Datei von alten Semester
					
					/* foreach($files as $entry){
						if(is_file($dir.$entry)){
							$entry_year = array();
							preg_match('/[0-9][0-9][0-9][0-9]/', $entry ,$entry_year);
							$entry_year = $entry_year[0];							
							
							$entry_sem = array();
							preg_match('/[0-9][.]/', $entry ,$entry_sem);						
							$entry_sem = $entry_sem[0];
							$entry_sem = $entry_sem+0;
							
							$entry_major = array();
							preg_match('/[A-Z][A-Z]/', $entry ,$entry_major);
							$entry_major = $entry_major[0];
							
							#Entry_Year != Year (Bsp: 2017 != 2018) ODER Entry_Year != Jetzt-3Monate (Bsp: es ist März 2019, es gilt aber weiter 2018 ungerade Semester)
							if($entry_year != date("Y") OR $entry_year != date("Y", time()-7889400)){
								$key = array_search($entry,$files);
								if($key!==false){
									unset($files[$key]);
								}
							}
							
							#Oktober bis März => lösche gerade Semester
							if(date("n")>= 10 OR date("n")<4){
								#TODO: Hier falsch gecodet fürs Beispiel!!! == auf != ändern
								if($entry_sem %2 != 0){
									$key = array_search($entry,$files);
									if($key!==false){
										unset($files[$key]);
									}
								}	
							}
							#April bis September => lösche ungerade Semester
							else if(date("n")>= 4 && date("n")<10){
								if($entry_sem %2 != 0){
									$key = array_search($entry,$files);
									if($key!==false){
										unset($files[$key]);
									}
								}	
							}
						}
					} */
					
					$counter = 0;
					foreach ($files as $val) {
						if (is_file($dir.$val)) { // show only "real" files
							$mydir .= '
							<option value="'.$val.'" data-feed="'.$dir.$val.'"';
							
							#TODO: Hier Namensanpassung
							#Schema: VI_2018_2.json
							
							$name = "";
							
							$val_year = array();
							preg_match('/[0-9][0-9][0-9][0-9]/', $val ,$val_year);
							$val_year = $val_year[0];							
							
							$val_sem = array();
							preg_match('/[0-9][.]/', $val ,$val_sem);						
							$val_sem = $val_sem[0];
							$val_sem = $val_sem+0;
							
							$val_major = array();
							preg_match('/[A-Z][A-Z]/', $val ,$val_major);
							$val_major = $val_major[0];
							
							if($val_major == "VI"){
								$name .= "Verwaltungsinformatik ";
							}
							
							$name .= $val_sem.". Semester ".$val_year;
							
							
							$mydir .= '>'.$name.'</option>';
							$counter++;
						}
					}					
					echo $mydir;
					$selectInhalt = $_GET['AuswahlSelect'] ;
					$ausgewaehltesSemester = $dir ;
					$ausgewaehltesSemester .= $selectInhalt ;
					
					?>
     </select>
</form>
				
		<?php
		// Aus Json-Datei die Id`s rausholen 
	if($selectInhalt != ''){								// Das ganze erst machen wenn im Select irgendwas ausgewählt wurde.
		$data = file_get_contents ($ausgewaehltesSemester);	// Json-Datei auslesen.
        $json = json_decode($data, true);					// Json-Datei dekodieren und den Inhalt (Array) in eine php-Variable stecken.
				
        foreach ($json as $key => $value) { 				// Jedes Array in einer Schleife durchgehen
            foreach ($value as $key => $val) {  						
				if($key=='id'){								// und nach Id's suchen.	
					$ids[] = $val ;							// Inhalt der 'Id`s' in eine neue Variable '$ids' als Array speichern.
				}
            }           
        }
		$ids = array_unique($ids); 						// Da mehrere Array's die selbe Id haben, werden hier die Mehfachnennungen entfernt.
		foreach ($ids as $valId) {						// Da nach Entfernung der Mehrfachnennungen der alte Index bleibt, müssen die Werte des Array
			$idsWerte[] = $valId ;						// in ein neues Array '$idsWerte' gespeichert werden.
		}
		

		// Mit Hilfe von zuvor rausgefundenen Id`s  nun die Titeln rausfinden. 
		$pruefung = false ;
		for($i = 0; $i< count($idsWerte) ; $i++){
			foreach ($json as $key => $value) { 			
				foreach ($value as $key => $val) {  						
					if($key=='id' && $val==$i){	
						if($pruefung == false){
							foreach($value as $key => $val){
								if($key== 'title'){
									$pruefung = true;  			// Hier wird die Schleife gestoppt, damit nur ein Titel pro Id gespeichert wird.    
									$alleTiteln[] = $val ;		// Sonst wiederholen sich die Titel, da selbe Ids mehrfach vorkommen können.
								}
							}						
						}
					}           
				}
			} $pruefung = false ;
		}
		
			// Checkbox
		//echo "Modulauswahl: <br>";
		
		echo '<form  id= "chbox" method="post"> ';	
		echo '<span id="chechboxUeberschrift">Wählen Sie Module aus :</span> <br>' ;
		for($i = 0; $i < count($idsWerte); $i++ ){							//Wieviele Ids die gewählte Json-Datei hatte, soviele Checkboxen werden erzeugt.
			echo  '<input type="checkbox" name="Modulauswahl[]" value="' . $i . '" />' . $alleTiteln[$i] . '<br>'; 	// und mit Titeln beschriftet.
		}
		echo '<input type="submit" value="Auswählen" name="submit"  /> <br>' ;
		echo '</form> <br>' ;
		
		$ausgewaehlteIds ;
		if (isset($_POST["Modulauswahl"])){				// Es werden nur die gewählten ModulIds gespeichert.
			if ($_POST["Modulauswahl"]) {
				$modulAuswahlId =  $_POST["Modulauswahl"];
   
					foreach ($modulAuswahlId as $value) {
			
						$ausgewaehlteIds[] = $value ;
					}
			}
		}
		
			// neue Json-Datei erzeugen. Alte Json-Datei wird in einzelne Events zerlegt und eine neue generiert.
			// Es werden nur die Events genommen, welche die vom Benutzer gewählten Ids haben.
		if (@$ausgewaehlteIds != ''){
			$neueJSON = '[' ; 
			foreach($ausgewaehlteIds as $key => $wert ){
				foreach ($json as $key => $value) {
					foreach ($value as $key => $val) {
						if($key == 'id' && $val== $wert){ 		
							$zwischenJSON = json_encode($value) ;
							$neueJSON  .= $zwischenJSON . ' , ' ;	
						}					
					}           
				}
			}
			// Events raussuchen die keine 'Ids' haben um Feiertage festzustellen, da die keine Ids haben
			foreach ($json as $key => $value) {
					$pruefung2 = true;
					foreach ($value as $key => $val) {						
						if($key == 'id'){ 	
							$pruefung2 = false;							
						}							
					} 
					if($pruefung2 == true){						
						$zwischenJSON = json_encode($value) ;
						$neueJSON  .= $zwischenJSON . ' , ' ;
					}
				}
			$neueJSON .= ']' ;
			//echo $neueJSON ;
		}
		}
	?>
					
   
   <span id="navigationlink">Navigation:</span>
   <div id="divnavi">
       <a href="https://moodle.hwr-berlin.de/login/index.php" id="amoodle">Moodle &#8250;</a>
  <a href="https://www.hwr-berlin.de/" id="ahwr">HWR Homepage &#8250;</a>
   </div>
   
    <span id="downloadspan">Download static ICS:</span>
		<button id="download" onClick="download(dropdown.value)">DOWNLOAD</button>
		
		<span id="download2spezi">Download spezific ICS:</span>
		<button id="download2" onClick="download2()">DOWNLOAD</button>
 
</div>
  
             <span id="auswahl" style="font-size:20px;cursor:pointer"  onclick="openNav()">&#9776; Auswahl</span>
             <script>
function openNav() {
  document.getElementById("mySidenav").style.width = "300px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>

        
                    
                <!css ist unten zu finden - postionierung etc.!>
                <div id="calendar"></div>
                
              
	
    </body>
    
    <head>
	<script> 	var selectedFeed </script>
                <!nicht sicher ,ob alles gebraucht wird!>
		<link rel="stylesheet" href="css/jquery-ui.min.css">
        <link rel='stylesheet' href='fullcalendar.css' />
		
		<script src='lib/jquery.min.js'></script>
		<script src='lib/moment.min.js'></script>                       
		<script src='fullcalendar.js'></script>
                <script src='locale/de.js'></script>
                <script src="external/jquery/jquery.js"></script>
                <script src="jquery-ui.min.js"></script>
		<script>
            
                 //Variable selectedfeed,um einzelne selected json Dateien zu benutzen   
				 
				 var selectedFeed = <?php echo $neueJSON ; ?>
		//var selectedFeed = $('#dropdown').find(':selected').data('feed'); // hier!!!!!!!!!!!!!!!!!!!!!!
		
		
		                //Calendar-start
               $('#calendar').fullCalendar({
                
                   locale: 'de',
                   lang: 'de',
                   minTime: "07:00:00",
        maxTime: "21:00:00",
        height: 'auto',
        contentHeight: 400,
        aspectRatio: 10,
        Boolean, default: true,
                  editable: true,
                 
                    firstDay: 1,
                    displayEventTime: false,
                    //Hier werden die einzelnen Events nicht wie bisher durch eine genaue .json aufgerufen ,sondern durch mein selectedFeed (aus dem dropdown)
                    eventSources: [ {events: selectedFeed, } ],
                    eventLimit: 5,
                   //hab ein Theme (Blitzer)benutzt von https://jqueryui.com/themeroller/
                    themeSystem: 'jquery-ui',
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay,listWeek'
                    },
                });
            //mit Hilfe aus :https://stackoverflow.com/questions/45794528/fullcalendar-event-filter-select-almost-working letzter Aufruf:13.11.2018 19:35
        /*     $('#dropdown').change(onSelectChangeFeed);
                
                //Funktion um das seleltierte Semester als events zu öffen
                function onSelectChangeFeed() { 
         
			
		var feed = $(this).find(':selected').data('feed');
                
		$('#calendar').fullCalendar('removeEventSource', selectedFeed);
                
                $('#calendar').fullCalendar('addEventSource', feed);  
                
                //Übertragung feed auf selected feed, welches bei Eventsource benutzt wird
                selectedFeed = feed;
                }; */
		</script>
                 

                <style>
            
		          /**?**/
		  #script-warning {
			display: none;
			background: #eee;
			border-bottom: 1px solid #ddd;
			padding: 0 10px;
			line-height: 40px;
			text-align: center;
			font-weight: bold;
			font-size: 12px;
			color: red;
		  }
		  
		  
	 
		  /**Kalender**/
		  #calendar {
                        position: relative;
                      font-family:Crimson Text Roman;
						max-width:800px;
						margin: 80px;
						padding: 0 10px;  
						background-color: white;
						border-style: solid;
						border-color: #979A9D;
						opacity: 0,5;
                                                right:-8%;
                                                 vertical-align:middle;
                                                 color:#4A4D50;
                                                 
}
body .fc {
   font-family: Crimson Text Roman;  
}
.ui-button{
    font-family: Crimson Text Roman; 
    font-weight: lighter;
}
.fc-content{
   
           max-height:40px;
}
 .fc-hover {
    max-height:none!important; 
}
.fc-title{
         white-space: normal;
        
   }
   .fc-event {
   /** float: left;**/
    font-size: 0.6em;   
    font-family:Crimson Text Roman;
 
}



						
		  
		  
		  /**Logo**/
		logo{
			background-image:url(../Test/images/logo.svg);
			background-repeat:no-repeat;
			background-size:100% auto;
			background-position:center center;
			width:40%;
			height:80%;
			position:absolute;
			left:80%;
			top:0;
			display:block; 
			
			
			}
			


/** Hintergrund Webseite **/
body {
	background-color:#F0F0F1 ;
        width:100%;
height:100%;
	}
	
	

	#box1 {
	background-color:#FAFAFA;
	margin-top: 0px;
	margin-left: 10%;
	width: 1170px;
	height: 1500px;
       
} 
#logopic{
    position: relative;
    left:8%;
}
		
	/**Dropdown Überschrift**/
#modulname{
     position: absolute;
     left:-20%;
     bottom:90%;
     color:#4A4D50;
     font-family: Crimson Text Roman;
	font-weight: normal;
	margin-left:25%;
       text-decoration: underline overline;
       
}
#dropdown {
    bottom:86%;
     position: absolute;
     left:5%;
     background-color:#FFFFFF;
     color:#4A4D50;
     font-size:0.7em;
    
}

#chbox {
	bottom: 55%;
	position: absolute;
	left:5%;
	
	
}
#navigationlink{
         color:#4A4D50;
   left:5%;
  bottom:50%;
     position: absolute; 
     text-decoration: underline overline;
    

}
#ahwr{
           color:#4A4D50;
   left:-6%;
  bottom:42%;
     position: absolute; 
      font-size:1em;    
}
#amoodle{
           color:#4A4D50;
   left:-6%;
  bottom:45%;
     position: absolute; 
      font-size:1em;
     
}

#downloadspan{
         color:#4A4D50;
   left:5%;
  bottom:13.5%;
     position: absolute;      
}
    #download{
            left:65%;
         bottom:13.5%;
     position: absolute;
    color: #4A4D50; 
    }
#download2spezi{
    color:#4A4D50;
   left:5%;
  bottom:10%;
     position: absolute;      
}
#download2{
    left:65%;
         bottom:10%;
     position: absolute;
    color: #4A4D50;
}
#navinnen {
   background-color:#FFFFFF; 
   bottom:54.5%;
   position: absolute;
   width:80%;
   left:9%;
}

#auswahl {
    margin: 4px 4px;
    left: 13%;
    bottom: 65%;
    position: absolute;
    color:#4A4D50;
}
.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #F0F0F1;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
 
}

.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: #f1f1f1;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

/* was angepasst werden muss : obere hellgraue Rand, hellgraue Rand so breit wie der Kalender, 
Kalender Borderlininen grau, in der funktion bearbeiten und css ausgeliedern
Gewünscht wurde noch : dropdown nach links verschieben, farben der Buttons ändern*/
  

  
		</style>
        </head>	
</html> 
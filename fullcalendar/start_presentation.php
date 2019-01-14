
<html>
    <head>
<link href='css/startstyle.css' rel='stylesheet' />

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

 
  <select id="dropdown" >
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
					
					foreach($files as $entry){
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
							
							#Entry_Year != Year (Bsp: 2017 != 2018) UND Entry_Year != Jetzt-3Monate (Bsp: es ist März 2019, es gilt aber weiter 2018 ungerade Semester)
							if($entry_year != date("Y") AND $entry_year != date("Y", time()-7889400)){
								$key = array_search($entry,$files);
								if($key!==false){
									unset($files[$key]);
								}
							}
							
							#Oktober bis März => lösche gerade Semester
							if(date("n")>= 10 OR date("n")<4){
								if($entry_sem %2 == 0){
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
					}
					
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
								$name .= "";
							}
							
							$name .= $val_sem.". Semester ".$val_year;
							
							
							$mydir .= '>'.$name.'</option>';
							$counter++;
						}
					}					
					echo $mydir;
					?>
                </select>
   <span id="navigationlink">Navigation:</span>
   <div id="divnavi">
       <a href="https://moodle.hwr-berlin.de/login/index.php" id="amoodle">Moodle &#8250;</a>
  <a href="https://www.hwr-berlin.de/" id="ahwr">HWR Homepage &#8250;</a>
   </div>
   
   
</div>
  
             <span id="auswahl" onclick="openNav()">&#9776; Auswahl</span>
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
		var selectedFeed = $('#dropdown').find(':selected').data('feed');
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
                    eventSources: [ selectedFeed ],
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
            $('#dropdown').change(onSelectChangeFeed);
                
                //Funktion um das seleltierte Semester als events zu öffen
                function onSelectChangeFeed() { 
                
		var feed = $(this).find(':selected').data('feed');
                
		$('#calendar').fullCalendar('removeEventSource', selectedFeed);
                
                $('#calendar').fullCalendar('addEventSource', feed);  
                
                //Übertragung feed auf selected feed, welches bei Eventsource benutzt wird
                selectedFeed = feed;
                };
		</script>
                 

              
        </head>	
</html> 

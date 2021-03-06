<html>
<?php
	#include 'php/create_ics.php';
?>
    <body>
		<span>Verwaltungsinformatik:</span>
                <!dropdown-menu
                <!fürs dropdown muss noch ne eigene css-formatierung angelegt werden!>
		<select id="dropdown" style="font-size: 16px;background-color:#dc1010;color:white;font-family: Arial,sans-serif;font-weight: bold;margin-top:120px;">
                    <option value="Semester" data-feed=""selected>Semester wählen</option>
                    <?php
					#https://www.tutdepot.com/create-a-select-menu-from-files-or-directory/					
					$dir = "../admin-portal/events/";
					$handle = opendir($dir);
					while (false !== ($file = readdir($handle))) {
						$files[] = $file;
					}
					closedir($handle);
					sort($files);	
					

					#TODO: Hier Aussortierung von Dateien, welche nicht mehr notwendig sind
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
								$name .= "Verwaltungsinformatik ";
							}
							
							$name .= $val_sem.". Semester ".$val_year;
							
							
							$mydir .= '>'.$name.'</option>';
							$counter++;
						}
					}					
					echo $mydir;
					?>
		</select>
                    
		<!css ist unten zu finden - postionierung etc.!>
		<div id="calendar"></div>
		
		<span>Download static ICS:</span>
		<button id="download" onClick="download(dropdown.value)">DOWNLOAD TEST</button>
		
		<span>Download spezific ICS:</span>
		<button id="download" onClick="download2()">DOWNLOAD TEST</button>
		
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
                    editable: false,
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
			
		
		function download(d){
			if(d == 'Semester') return;
			
			d = d.replace("json", "ics");
			d = "../admin-portal/ics/"+d;
			window.location = d;
		}
		
		function download2(){
			//clientEvents liefert nur die derzeit angezeigten Events
			//d = $('#calendar').fullCalendar('clientEvents');
			d = $('#calendar').fullCalendar('getEventSources');			
			window.alert(d.toString());
		}
		</script>
	
                <style>
            
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
		  
		  
		  #calendar {
                        position: relative;
			max-width: 1200px;
			margin: auto;
			padding: 0 10px;     
		  }
                  
		</style>
        </head>	
</html> 
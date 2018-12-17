<html>
<?php
	#include 'php/create_ics.php';
?>
    <body>
		
                <!dropdown-menu
                <!fürs dropdown muss noch ne eigene css-formatierung angelegt werden!>
		<form  method="post" enctype="multipart/form-data">
		
		<select id="dropdown" name="dropdown" >
                    <option value="Semester" data-feed=""selected>Semester wählen</option>
                    <?php
					#https://www.tutdepot.com/create-a-select-menu-from-files-or-directory/					
					$dir = "events/";
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
		
		
		<?php
			if(isset($_POST['publish'])){
				rename("events/".$_POST['dropdown'], "public/".$_POST['dropdown']);
			}			
		?>
                <div id="freigeben">
                    <input type="submit" name="publish" value="Freigebenen" />
                </div>
		<!css ist unten zu finden - postionierung etc.!>
		<div id="calendar"></div>

		<!--<input type="submit" name="publish" value="Freigebenen" />-->
		</form>
				
    </body>
    
    <head>
                <!nicht sicher ,ob alles gebraucht wird!>
		<link rel="stylesheet" href="../fullcalendar/css/jquery-ui.min.css">
        <link rel='stylesheet' href='../fullcalendar/fullcalendar.css' />
		<script src='../fullcalendar/lib/jquery.min.js'></script>
		<script src='../fullcalendar/lib/moment.min.js'></script>                       
		<script src='../fullcalendar/fullcalendar.js'></script>
                <script src='../fullcalendar/locale/de.js'></script>
                <script src="../fullcalendar/external/jquery/jquery.js"></script>
                <script src="../fullcalendar/jquery-ui.min.js"></script>
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
			if(d == 'Semester2') return;
			window.location = d;
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
		  /** position: relative;
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
                                                 color:#4A4D50;**/
		  
		  #calendar {
                        position: relative;
			max-width: 100%;
			/**background-color: #FFFFFF;**/
			padding: 0 10px;  
                        left:0%;
                            bottom:65%;
                            color:#4A4D50;
                            font-family: Crimson Text Roman;  
                            
		  }
                  #freigeben{
                      position:absolute;
                      left:99%;
                      bottom:82%;
}
#dropdown{
   
     background-color:#FFFFFF;
     color:#4A4D50;
    
    position:absolute;
                    left:-71.1%;
                    bottom:15%;
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



		</style>
        </head>	
</html> 
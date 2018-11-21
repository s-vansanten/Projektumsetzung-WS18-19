<html>
    <body>
          <span>Verwaltungsinformatik:</span>
                <!dropdown-menu
                <!fürs dropdown muss noch ne eigene css-formatierung angelegt werden!>
		<select id="dropdown" style="font-size: 16px;background-color:#dc1010;color:white;font-family: Arial,sans-serif;font-weight: bold;margin-top:120px;">
                    <option value="Semester" data-feed=""selected>Semester wählen</option>
                    <?php
					#https://www.tutdepot.com/create-a-select-menu-from-files-or-directory/					
					$dir = "../test/events/";
					$handle = opendir($dir);
					while (false !== ($file = readdir($handle))) {
						$files[] = $file;
					}
					closedir($handle);
					sort($files);

					#TODO: Hier Ansortierung von Dateien, welche nicht mehr notwendig sind
					#das heißt: keine Datei von alten Semester
					
					$counter = 0;
					foreach ($files as $val) {
						if (is_file($dir.$val)) { // show only "real" files
							$mydir .= '
							<option value="'.$dir.$val.'" data-feed="'.$dir.$val.'"';
							
							#TODO: Hier Namensanpassung
							#Schema: VI_2018_2.json
							
							#$name = (strlen($val) > $mlength) ? substr($val, 0, $mlength).'...' : $val.'';
							$name = $val;
							$mydir .= '>'.$name.'</option>';
							$counter++;
						}
					}
					file_put_contents('test.txt', $mydir);
					file_put_contents('test2.txt', $files);
					echo $mydir;
					?>
                </select>
                    
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
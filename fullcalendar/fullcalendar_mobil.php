<html>
<head>
	            <meta name="viewport" content="width=device-width, initial-scale=1">
	<div id="box1"> 
	<figure id="logopic"> <img src="https://upload.wikimedia.org/wikipedia/de/9/90/Hochschule_f%C3%BCr_Wirtschaft_und_Recht_Berlin_logo.svg" width="100%" height="10%"
	alt="Logo">        
	</figure>

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
					$selectInhalt = $_GET['AuswahlSelect'] ;
					$ausgewaehltesSemester = $dir ;
					$ausgewaehltesSemester .= $selectInhalt ;
					
					?>
                </select>
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
			document.getElementById("mySidenav").style.width = "300";
		}

		function closeNav() {
			document.getElementById("mySidenav").style.width = "0";
		}

		window.mobilecheck = function() {
			var check = false;
			(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
			return check;
		};
			</script>


        
                    
                <!css ist unten zu finden - postionierung etc.!>
                <div id="calendar"></div>
                
              
	
</body>
<head>
                <!nicht sicher ,ob alles gebraucht wird!>
		<link rel="stylesheet" href="css/jquery-ui.min.css">
        <link rel='stylesheet' href='fullcalendar.css' />
		<link rel='stylesheet' href='startstyle.css' />
		
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
					defaultView: window.mobilecheck() ? "basicDay" : "month",
					/*defaultView: (function (){ 
					if ($(window).width() <= 768) { 
						return defaultView = 'agendaDay'; 
						function openNav() {
						document.getElementById("mySidenav").style.width = "50%";
}
						} 
						else { return defaultView = 'month'; 
						} })(),*/
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
                 

    <style>
				
			* {
				box-sizing: border-box;
			  }	
            
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
						max-width:80%;
						margin: 5%;
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
		margin-top: 2%;
        width:100%;
	}

	#box1 {
	background-color:#FAFAFA;
	margin-top: 5%;
	margin-left: 5%;
	margin-bottom: 5%;
	width: 100%;
	
       
} 
#logopic{
    position: relative;
    left:8%;
}
		
	/**Dropdown Überschrift**/
#modulname{
     position: absolute;
     left:-20%;
     bottom:69%;
     color:#4A4D50;
     font-family: Crimson Text Roman;
	font-weight: normal;
	margin-left:25%;
       text-decoration: underline overline;
       
}
#dropdown {
    bottom:65%;
     position: absolute;
     left:5%;
     background-color:#FFFFFF;
     color:#4A4D50;
     font-size:0.7em;
    
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
    margin: 3%;
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
  padding-top: 5%;
 
}

.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 10%;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: black;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 10%;
  font-size: 36px;
  margin-left: 50px;
}



@media only screen and (max-width: 768px) {
  /* For mobile phones: */
  [class*="box1"] {
    width: 100%;
  }
}

/* was angepasst werden muss : obere hellgraue Rand, hellgraue Rand so breit wie der Kalender, 
Kalender Borderlininen grau, in der funktion bearbeiten und css ausgeliedern
Gewünscht wurde noch : dropdown nach links verschieben, farben der Buttons ändern*/
    
	</style>
</head>	
</html> 
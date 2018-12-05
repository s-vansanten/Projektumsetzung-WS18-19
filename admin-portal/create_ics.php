<?php
##################################
#Verwendung: 	Einbindung durch "include 'create_ics.php'"
#
#				Aufruf der Funktion "start_create_ics($file_name)"
#				$file_name ist eine Variable mit dem Verweis auf eine hochgeladene JSON-Datei mit fullcalendar-Events
#
#
#				Im Unterordner "ics/" werden dann ICS-Dateien mit folgendem Schema erstellt:																	
#						$file_name[ohne .json].ics
#
##################################

	function start_create_ics($file_name){
		
		$dir = 'ics/';
		
		$eol = "\r\n";
		
		$start_string ='BEGIN:VCALENDAR'.$eol.
			'VERSION:2.0'.$eol.
			'PRODID:-//Projektumsetzung WS18-19//HWR Berlin//DE'.$eol.
			'METHOD:PUBLISH'.$eol.
			'BEGIN:VTIMEZONE'.$eol.
			'TZID:BerlinTime'.$eol.
			'BEGIN:STANDARD'.$eol.
			'DTSTART:16011028T030000'.$eol.
			'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10'.$eol.
			'TZOFFSETFROM:+0200'.$eol.
			'TZOFFSETTO:+0100'.$eol.
			'END:STANDARD'.$eol.
			'BEGIN:DAYLIGHT'.$eol.
			'DTSTART:16010325T020000'.$eol.
			'RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3'.$eol.
			'TZOFFSETFROM:+0100'.$eol.
			'TZOFFSETTO:+0200'.$eol.
			'END:DAYLIGHT'.$eol.
			'END:VTIMEZONE'.$eol;
		$end_string ='END:VCALENDAR';
		
		$post = $start_string;
		$entries = file_get_contents($file_name);
		$entries = json_decode($entries, true);
		
		
		#
		foreach ($entries  as $key => $entry){
			$post .= 'BEGIN:VEVENT'.$eol.
				'DTSTART;TZID=BerlinTime:'.date('Ymd', strtotime($entry['start'])).'T'.date('His', strtotime($entry['start'])).$eol.
				'DTEND;TZID=BerlinTime:'.date('Ymd', strtotime($entry['end'])).'T'.date('His', strtotime($entry['end'])).$eol.
				'LOCATION:'.$entry['location'].$eol.
				'UID:'.md5($entry['start']).$eol.
				'DTSTAMP:'.date('Ymd').'T'.date('His').$eol.
				'SUMMARY:'.$entry['title'].$eol.
				'END:VEVENT'.$eol;
		}
		
		$post .= $end_string;
		
		#Routine zum Erstellen einer ICS-Datei
		$file = $dir.pathinfo($file_name, PATHINFO_FILENAME).'.ics';
		$fp = fopen($file,'w');
		fwrite($fp, $post);
		fclose($fp);
	}


?>
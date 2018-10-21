<?php

for ($row = 0; $row < count($csv)-2; $row++){
		$kw_start = $csv [$row]['LV-Start'];
		if($lv_start !=NULL){	
				$week_start = new DateTime();
				$week_start->setISODate($year,$week);
				$week_start->format('Y-m-d\TH:i:sO');
				-> Kalenderwocher, string into int 
		
			if (($csv[$row]['Mo B'] = !Null){
				$week_start -> modify('+0 day');
				$start_time = $week_start
				$end_time = $week_start
				$start_time->modify("+{$time_splited[0]} hours");
				$end_time->modify("+{$time_splited[1]} minutes");
				
		 	}
			
			if (($csv[$row]['DI B'] = !NULL) {
			$week_start -> modify('+1 day');
				$start_time = $week_start
				$end_time = $week_start
				$start_time->modify("+{$time_splited[0]} hours");
				$end_time->modify("+{$time_splited[1]} minutes");
			
		
		}
			if (($csv[$row]['Mi B'] = !NULL) {
			$week_start -> modify('+2 day');
				$start_time = $week_start
				$end_time = $week_start
				$start_time->modify("+{$time_splited[0]} hours");
				$end_time->modify("+{$time_splited[1]} minutes");
				
		
			if (($csv[$row]['Do B']= !NULL) {
			$week_start -> modify('+3 day');
				$start_time = $week_start
				$end_time = $week_start
				$start_time->modify("+{$time_splited[0]} hours");
				$end_time->modify("+{$time_splited[1]} minutes");
			
		}
			if (($csv[$row]['Fr B'] = !NULL) {
			$week_start -> modify('+4 day');
				$start_time = $week_start
				$end_time = $week_start
				$start_time->modify("+{$time_splited[0]} hours");
				$end_time->modify("+{$time_splited[1]} minutes");
			
			
		}
			if (($csv[$row]['Sa B'] = !NULL) {
			$week_start -> modify('+5 day');
				$start_time = $week_start
				$end_time = $week_start
				$start_time->modify("+{$time_splited[0]} hours");
				$end_time->modify("+{$time_splited[1]} minutes");
				
		}
		 		
		} 	$title = $csv[$row]['Modul'];
		 		$posts[]= array('title'=> $title);			
		}
	

	}
?>
		
	   $time_splited = explode(".", $time);
	   Splitted die Zeit bei dem doppeltpunkt , zb 14:00
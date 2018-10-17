<html>
	<head>
		<link rel='stylesheet' href='fullcalendar.css' />
		<script src='lib/jquery.min.js'></script>
		<script src='lib/moment.min.js'></script>
		<script src='fullcalendar.js'></script>
		
		<script>
		$(document).ready(function(){
		var calendar = $('#calendar').fullCalendar({
			// put your options and callbacks here
			editable:false,
			header:{
				left:'prev,next today',
				center:'title',
				right:'month,agendaWeek,agendaDay'
			},
			defaultView: 'agendaWeek',
			events: [
				{
					title: 'Test',
					start: '2018-10-17T10:00:00+0100',
					end: '2018-10-17T14:00:00+0100',
				},
				{
					title: 'Test2',
					start: '2018-10-17T14:00:00+0100',
					end: '2018-10-17T18:00:00+0100',
				}
			],
			selectable:true,
			selectHelper:true,
			
		})
		});
		</script>
	</head>
	
	<body>
		<br />
		<h2 align="center"><a href="#">Testkalender</a></h2>
		<br />
		<div class="container">
		<div id="calendar"></div>
		</div>
	</body>
</html>
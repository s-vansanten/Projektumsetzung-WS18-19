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
				right:'month,agendaWeek,agendaDay,listWeek'
			},
			events: {
					url: 'php/get-events.php',
					error: function() {
						$('#script-warning').show();
					}
			},
			selectable:true,
			selectHelper:true,
			
		})
		});
		</script>
		<style>
			body {
			margin: 0;
			padding: 0;
			font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
			font-size: 14px;
		  }

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

		  #loading {
			display: none;
			position: absolute;
			top: 10px;
			right: 10px;
		  }

		  #calendar {
			max-width: 900px;
			margin: 40px auto;
			padding: 0 10px;
		  }
		</style>
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
<?php
	include("./main.php");
	checkAdmin($application);
?>
<!DOCTYPE html>
<html>
  <head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./dashboard.css" type="text/css" media="screen,print"/>
	<title>Book Group Admin Dashboard</title>
  </head>
  <body>
	<h1>Book Group Admin Dashboard</h1>
	<div class="dashboard">
		<div>
			<input type="button" onclick="window.open('./suggestions_admin.php', '_blank')" value="Manage Suggested Books">
			<input type="button" onclick="window.open('./calendar_admin.php', '_blank')" value="Manage Calendar">
		</div>
		<div>
			<input type="button" onclick="window.open('./members.php', '_blank')" value="Manage Members">
			<input type="button" onclick="window.open('./venues.php', '_blank')" value="Manage Venues">
		</div>
		<div>
			<!--<input type="button" onclick="window.open('./attendance.php', '_blank')" value="Attendance">-->
			<input type="button" onclick="window.open('./logins.php')" value="Display Recent Logins">
			<input type="button" id = "return" onclick="window.open('../calendar.php')" value="Return to Calendar">
		</div>
	</div>
  </body>
</html>

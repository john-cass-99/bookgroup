<?php
	include("./main.php");
	checkAdmin($application);
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./bookgroup_admin.css" type="text/css" media="screen,print"/>
	<title>Calendar</title>
</head>
<body>
<?php
		include( '../../inf1.php' );
?>
	<div>
		<h1 class="h1mod">Calendar</h1>
	</div>
	<div>
		<input type="button" id="AddMeeting" value="Add Event" onClick="EditRow(0)">
	</div>
	<p id="SortMsg">Click on column heading to sort</p>

	<table class="main calendar" name="tblCalendar" id="tblCalendar">
	  <tr>
		<th onclick="sortTable('tblCalendar', 0)">ID</th>
		<th onclick="sortTable('tblCalendar', 5)">Event Date/Time</th>
		<th onclick="sortTable('tblCalendar', 2)">Host</th>
		<th onclick="sortTable('tblCalendar', 3)">Venue</th>
		<th onclick="sortTable('tblCalendar', 4)">Book</th>
		<th onclick="sortTable('tblCalendar', 6)">Suggested By</th>
		<th onclick="sortTable('tblCalendar', 7)">Chosen By</th>
	  </tr>
<?php
	try {
		define( 'ID_col',    0 );
		define( 'DATE_col',  1 );
		define( 'HOST_col',  2 );
		define( 'VENUE_col', 3 );
		define( 'BOOK_col',  4 );
		define( 'SUGBY_col', 5 );
		define( 'CHOSENBY_col', 6 );

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT ID, MeetingDate, Host, Venue, Book, SuggestedBy, ChosenBy FROM vwcalendaradmin;";
		$pdos = $db->query( $sql );
		if ( $pdos ) {
			while ($row = $pdos->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				echo "\t\t<tr onClick=\"EditRow(" . $row[ID_col] . ")\">\n";
				echo "\t\t\t<td>" . str_pad(strval($row[ID_col]), 4, " ", STR_PAD_LEFT) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[DATE_col]) ? "&nbsp;" : date_format(DateTime::createFromFormat('Y-m-d H:i:s', $row[DATE_col]), 'd/m/Y H:i') ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[HOST_col])  ? "&nbsp;" : $row[HOST_col] )  . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[VENUE_col]) ? "&nbsp;" : $row[VENUE_col] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[BOOK_col])  ? "&nbsp;" : $row[BOOK_col] )  . "</td>\n";
				echo "\t\t\t<td>" . $row[DATE_col] . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[SUGBY_col])  ? "&nbsp;" : $row[SUGBY_col] )  . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[CHOSENBY_col])  ? "&nbsp;" : $row[CHOSENBY_col] )  . "</td>\n";
				echo "\t\t</tr>\n";
			}
			$pdos = null;
		}
		else
			echo "Query Failed";
	}
	catch(PDOException $ex) {
		include '../catch.php';
	}
?>
	</table>
	<script>
		function EditRow(ID) {
			window.open( './eventdetails2_admin.php?idEvent=' + ID, '_blank' );
	}

	</script>
<script src="../../scripts/sort.js"></script>

</body>
</html>

<?php
	include('./main.php');
	checkLoggedIn($application, 'calendar.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./bookgroup.css" type="text/css" media="screen,print"/>
	<title>Book Group Calendar</title>
	<script src="./dropdown.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
<?php
	include "./bookgroup_name.php";
?>
	<h1>Calendar&nbsp;of&nbsp;Meetings</h1>
	<div class="links">
		<?php include "./dropdown.php"; ?>
		<?php include "./dropdown2.php"; ?>
	</div>

	<div id="ctxMenu">
		<div id="ctxSave">Save event (.ics) file</div>
		<div id="ctxAdd2Google">Add to Google Calendar</div>
	</div>
	
	<table>
	  <tr>
		<th>Date</th>
		<th>Time</th>
		<th>Host</th>
		<th>Book</th>
		<th>Author</th>
	  </tr>
<?php
	try {

		function ics_date($date_str){
			return str_replace(" ", "T", $date_str->format("Ymd His"));
		}

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "select ID from calendar where MeetingDate >= now() order by MeetingDate limit 1;";
		$pdos = $db->query( $sql );
		$IDNext = $pdos->fetchColumn();
		//echo "<p>IDNext = $IDNext</p>";

		$pdos = $db->query("SELECT * FROM vwcalendar");
		if ( $pdos ) {
			while ($row = $pdos->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
				if ( $IDNext == $row['ID'] )
					$style = " style=\"color: DarkRed; font-weight: bold\"";
				else
					$style = "";

				$event_start = new DateTime($row['MeetingDate']);

				echo "\t\t<tr class=\"context\" data-id=\"" . $row['ID'] . "\" data-start=\"" . ics_date($event_start) . "\" data-end=\"" . ics_date($event_start->add(new DateInterval("PT3H"))) . "\">\n";
				echo "\t\t\t<td$style>" . $row['MeetingDateFmt'] . "</td>\n";
				echo "\t\t\t<td$style>" . strtolower($row['MeetingTime']) . "</td>\n";

				if ( !is_null($row['KnownAs']) )
					echo "\t\t\t<td$style>" . $row['KnownAs'] . "</td>\n";
				else if ( !is_null($row['VenueName']) ) {
					if ( is_null($row['ID']) || $row['VenueName'] == 'TBA' ) {
						echo "\t\t\t<td$style>" . $row['VenueName'] . "</td>\n";
					}
					else {
						if (is_null($row['Website']) || $row['Website']=='')
							echo "\t\t\t<td$style>" . $row['VenueName'] . "</td>\n";
						else
							echo "\t\t\t<td$style><a href=\"" . $row['Website'] . "\" target=\"_blank\">" . $row['VenueName'] . "</a></td>\n";
					}
				}
				else
					echo "\t\t\t<td$style><i>Cancelled</i></td>\n";

				if ( is_null($row['Title']) ) {
					echo "\t\t\t<td$style>TBA</td>\n";
					echo "\t\t\t<td$style>&nbsp;</td>\n";
				}
				else {
					if ($row['AmazonLink'] == '')
						echo "\t\t\t<td$style>" . $row['Title'] . "</td>\n";
					else
						echo "\t\t\t<td$style><a href=\"" . $row['AmazonLink'] . "\" target=\"_blank\">" . $row['Title'] . "</a></td>\n";
					echo "\t\t\t<td$style>" . $row['Author'] . "</td>\n";
				}
				echo "\t\t</tr>\n";
			}
			$pdos = null;
		}
		else
			echo "Query Failed";
	}
	catch(PDOException $ex) {
		include './catch.php';
	}
	print "\t</table>\n";
?>
<script>
	var clicked_row;

	$(document).ready(function(){

		$(".context").on("contextmenu", function(event){
			//alert('Context menu clicked');
			event.preventDefault();
			var ctxMenu = document.getElementById("ctxMenu");
			ctxMenu.style.display = "table";
			ctxMenu.style.left = (event.pageX - 10)+"px";
			//ctxMenu.style.top = (event.pageY - 10)+"px";
			clicked_row = this;
			var r = this.getBoundingClientRect();
			//alert(`Top = ${r.top}, Bottom = ${r.bottom}`);
			ctxMenu.style.top = window.pageYOffset + ((r.top + r.bottom) / 2 - 10) + "px";
		})

		$(".context").on("click", function(event){
			//event.preventDefault();
			ClearMenu();
		})

		$("#ctxSave").on("click", function(){

			var filename = 'BookGroup';
			if (typeof clicked_row != undefined) {
				filename += '-' + clicked_row.cells[0].innerText.replace(/\//g, "-");
			}
			filename += ".ics";
			download(filename, GetEventData(clicked_row));
			ClearMenu();
		})

		$("#ctxAdd2Google").on("click", function() {
			window.open( "./event_details.php?idEvent=" + clicked_row.getAttribute('data-id'), '_blank')
			ClearMenu();
		})

	})

	function ClearMenu() {
		var ctxMenu = document.getElementById("ctxMenu");
		ctxMenu.style.display = "none";
		ctxMenu.style.left = "";
		ctxMenu.style.top = "";
	}

	function download(filename, text) {
		var element = document.createElement('a');
		element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
		element.setAttribute('download', filename);

		element.style.display = 'none';
		document.body.appendChild(element);

		element.click();

		document.body.removeChild(element);
	}

	// row parameter is the clicked TableRow
	function GetEventData(row) {
		const preamble = "BEGIN:VCALENDAR\nVERSION:2.0\nX-WR-TIMEZONE:Europe/London\n";
		const tail = "TRANSP:OPAQUE\nEND:VEVENT\nEND:VCALENDAR";
		
		var ics = "BEGIN:VEVENT\n";
		ics += "PRODID:-//JMC Software//BookGroup//EN\n";
		ics += "UID:" + create_UUID() + "\n";
		ics += "DTSTART:" + row.getAttribute("data-start") + "\n";
		ics += "DTEND:" + row.getAttribute("data-end") + "\n";

		var host = row.cells[2].innerText;
		ics += `SUMMARY:Book Group - ${host}\n`
		
		// The book is placed in location so that it appears on the google calendar
		var book = row.cells[3].innerText;
		if (book != 'TBA') {
			var author = row.cells[4].innerText;
			if (author != '')
				book += ` by ${author}`;
				ics += `LOCATION:${book}\n`;
		}

		return preamble + ics + tail;
	}

	function create_UUID(){
    var dt = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (dt + Math.random()*16)%16 | 0;
        dt = Math.floor(dt/16);
        return (c=='x' ? r :(r&0x3|0x8)).toString(16);
    });
    return uuid;
}

</script>
</body>
</html>

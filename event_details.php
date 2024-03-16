<?php
	include('./main.php');
	checkLoggedIn($application, 'event_details.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="referrer" content="no-referrer-when-downgrade" />
	<link rel="StyleSheet" href="./event_details.css" type="text/css" media="screen,print"/>
	<title>Edit Event</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php
	try {

		// print( "<p>GET: ");
		// print_r($_GET);
		// print("</p>\n");

		// print( "<p>POST: ");
		// print_r($_POST);
		// print("</p>\n");

		if (isset($_GET['idEvent']))
			$idEvent = $_GET['idEvent'];
		else
			$idEvent = 0;

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		if ($idEvent == 0) {
			$event_data = array('MeetingDate'=>'', 'Host'=>0, 'ptVenues'=>0, 'ptBooks'=>0);
		}
		else {
			$sql = "SELECT MeetingDate, " .
			"IF(M.KnownAs IS NULL, '', M.KnownAs) AS HostName, " .
			"CONCAT_WS(', ', M.Address1, IF( M.Address2 IS NULL, '', M.Address2), M.Postcode) As HostAddress, " .
			"IF(V.VenueName IS NULL, '', V.VenueName) As VenueName, " .
			"CONCAT_WS(', ', V.Address1, IF( V.Address2 IS NULL, '', V.Address2), V.Postcode) As VenueAddress, " .
			"(IF(B.Title IS NULL, '', B.Title)) AS Book, " .
			"B.Author AS Author " .
			"FROM calendar C LEFT JOIN venues V ON C.ptVenues=V.ID " .
			"LEFT JOIN members M ON C.Host=M.ID " .
			"LEFT JOIN books B ON C.ptBooks=B.ID " .
			"WHERE C.ID=:ID;";
		
			// print("\n<p>Get SQL=$sql<br>idEvent=$idEvent\n");
			$st = $db->prepare($sql);
			$st->bindValue(':ID', $idEvent);
			$st->execute();
			$event_data = $st->fetch(PDO::FETCH_ASSOC);
			$event_data['MeetingDate'] = str_replace(" ", "T", $event_data['MeetingDate']);
		}

		// print( "<p>event_data: ");
		// print_r($event_data);
		// print("</p>\n");

	}
	catch(PDOException $ex) {
		include './catch.php';
	}
?>

<header>
	<h2>Add Event to Google Calendar</h2>
</header>
<div class="data-div" id="events">
	<div>
		<label for="txtEventDate">Event Date:</label>
		<input type="datetime-local" id="txtEventDate" name="txtEventDate" readonly value="<?php print($event_data['MeetingDate']) ?>">
	</div>
	<div>
		<label for="txtHost">Host:</label>
		<input type="text" id="txtHost" name="txtHost" readonly value="<?php print($event_data['HostName']) ?>">
	</div>
	<div>
		<label for="txtVenue">Venue:</label>
		<input type="text" id="txtVenue" name="txtVenue" readonly value="<?php print($event_data['VenueName']) ?>">
	</div>
	<div>
		<label for="txtBook">Book:</label>
		<textarea id="txtBook" name="txtBook" readonly rows="2"><?php print($event_data['Book'] . "\n by " . $event_data['Author']) ?></textarea>
	</div>
	<div>
		<input type="button" id="cmdEvent2Cal" value="Add to Google Calendar" />
		<input type="button" class="floatr" id="cmdClose" name="cmdClose" value="Close" />
	</div>
	<div id="response">
	</div>
	<div class="signout">
		<input type="button" id="signout_button" value="Sign Out from Google" />
		<input type="hidden" id="Book" name="Book" value="<?php print($event_data['Book']) ?>" />
		<input type="hidden" id="Author" name="Author" value="<?php print($event_data['Author']) ?>" />
		<input type="hidden" id="HostAddress" name="HostAddress" value="<?php print($event_data['HostAddress']) ?>" />
		<input type="hidden" id="VenueAddress" name="VenueAddress" value="<?php print($event_data['VenueAddress']) ?>" />
	</div>
</div>

<script>

	$(document).ready(function(){

		$("#cmdClose").on("click", function() {
			window.close();
		})

		/*
		* This event handles authorisation and then adds event if successful.
		*/

		$("#cmdEvent2Cal").on("click", handleAuthClick);

		$("#signout_button").on("click", handleSignoutClick);

	});

	// Code for adding the event to Google Calendar

	const CLIENT_ID = '';
	const API_KEY = '';
	const SCOPES = 'https://www.googleapis.com/auth/calendar';

	let tokenClient;
	let gapiInited = false;
	let gisInited = false;

	document.getElementById('signout_button').style.visibility = 'hidden';

	/**
	* Callback after api.js is loaded.
	*/
	function gapiLoaded() {
		gapi.load('client', initializeGapiClient);
	}

	/**
	* Callback after the API client is loaded. Loads the
	* discovery doc to initialize the API.
	*/
	async function initializeGapiClient() {
		await gapi.client.init({
		apiKey: API_KEY,
		discoveryDocs: ['https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest'],
		});
		gapiInited = true;
	}

	/**
	 * Callback after Google Identity Services are loaded.
	 */
	function gisLoaded() {
		tokenClient = google.accounts.oauth2.initTokenClient({
		client_id: CLIENT_ID,
		scope: SCOPES,
		callback: '', // defined later
		});
		gisInited = true;
	}

	/**
	*Sign in the user upon button click.
	*If successful, continue with adding the event
	*/
	function handleAuthClick() {
		tokenClient.callback = async (resp) => {
			if (resp.error !== undefined) {
				document.getElementById('response').innerText = "Event error: \n" + JSON.stringify(resp.error);
				throw (resp);
			}
			document.getElementById('signout_button').style.visibility = 'visible';
			await AddEvent();
		};

		if (gapi.client.getToken() === null) {
		// Prompt the user to select a Google Account and ask for consent to share their data
		// when establishing a new session.
		tokenClient.requestAccessToken({prompt: 'consent'});
		} else {
		// Skip display of account chooser and consent dialog for an existing session.
		tokenClient.requestAccessToken({prompt: ''});
		}
	}

	/**
	*Sign out the user upon button click.
	*/
	function handleSignoutClick() {
		const token = gapi.client.getToken();
		if (token !== null) {
		google.accounts.oauth2.revoke(token.access_token);
		gapi.client.setToken('');
		document.getElementById('signout_button').style.visibility = 'hidden';
		}
	}

	async function AddEvent() {
		var dt = document.getElementById('txtEventDate').value;
		if ( dt=="" ) {
			alert('Date and Time must be set!');
		}
		else {
			var start_date = new Date(dt);
			var end_date = new Date( start_date );
			end_date.setHours(end_date.getHours() + 3);
			console.log('\nStart Date: ' + start_date.toISOString() + '\nEnd Date: ' + end_date.toISOString() );

			// If this is a normal meeting i.e. at hosts house the title contains the host and the book is in the location.
			// This is so that all useful info is visible in the calendar without clicking the event.
			// However, if the meeting is at an outside venue the title contains the venue name,
			// the venue address goes in the location and the book in the description

			var title;
			var description;
			var location;
			
			// HOST or Venue; Location
			var txtH= document.getElementById("txtHost").value;
			var txtV= document.getElementById("txtVenue").value;

			// BOOK
			var book = document.getElementById("Book").value;
			var author = document.getElementById("Author").value;

			if ( txtH != ''){
				title = 'Book Group - ' + txtH;
				if (book == '') {
					location = document.getElementById("HostAddress").value.replace(", ,",",");
					description = 'Book to be advised.';
				}
				else {
					location = "'" + book + "' by " + author;
					description = '';
				}
			}
			else {
				title = `Book Group at ${txtV}`;
				location = document.getElementById("VenueAddress").value.replace(", ,",",");
				description = book == '' ? 'TBA' : "Reading '" + book + "' by " + author;
			}

			console.log( "Host: " + txtH + "\nVenue: " + txtV + "\nLocation: " + location + "\nBook: " + book );
			
			const event = {
				'summary': title,
				'location': location,
				'description': description,
				'start': {
					'dateTime': start_date,
					'timeZone': 'Europe/London'
				},
				'end': {
					'dateTime': end_date,
					'timeZone': 'Europe/London'
				}
			};

			const request = gapi.client.calendar.events.insert({
			'calendarId': 'primary',
			'resource': event
			});

			request.execute(function(event) {
				if (typeof(event.error) == 'undefined')
					document.getElementById('response').innerHTML = `<a href="${event.htmlLink}">Event Created (click to view)</a>`;
				else{
					document.getElementById('response').innerText = "Event error: \n" + JSON.stringify(event.error);
				}
			});
		}

	}

	</script>
	<script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
	<script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>

</body>
</html>

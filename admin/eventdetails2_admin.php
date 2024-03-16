<?php
	include("./main.php");
	checkAdmin($application);
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="referrer" content="no-referrer-when-downgrade" />
	<link rel="StyleSheet" href="./bookgroup2_admin.css" type="text/css" media="screen,print"/>
	<title>Edit Event</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<?php
	function setStatus( $db, $idBook, $newStatus )
	{
		try {
			$ust = $db->prepare("UPDATE books SET Status=:Status WHERE ID=:ID;");
			$ust->bindValue( ":Status", $newStatus );
			$ust->bindValue( ':ID', $idBook );
			$ust->execute();
		}
		catch(PDOException $ex) {
			include '../catch.php';
		}
	}

	try {
		include( '../../inf1.php' );

		// print( "<p>GET: ");
		// print_r($_GET);
		// print("</p>\n");

		// print("<p>POST: ");
		// print_r($_POST);
		// print("</p>\n");

		if (isset($_GET['idEvent']))
			$idEvent = $_GET['idEvent'];
		else
			$idEvent = 0;

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		$action = "eventdetails2_admin.php";
		if (isset($_POST['submitAction'])) {
			switch($_POST['submitAction']) {
				default:
					break;
				case 1: // Save
					$venue = isset($_POST['lstVenue']) ? $_POST['lstVenue'] : 0;
					$host = isset($_POST['lstHost']) ? $_POST['lstHost'] : 0;
					$book = $_POST['lstBook'];

					if ($idEvent == 0) { // Insert new event
						$sql= "INSERT INTO calendar ( MeetingDate, Host, ptVenues, ptBooks ) VALUES " .
							"( :EventDate, :Host, :ptVenues, :ptBooks );";
					}
					else { // Update existing event
						$sql = "UPDATE calendar SET MeetingDate=:EventDate, Host=:Host, ptVenues=:ptVenues," .
							" ptBooks=:ptBooks WHERE ID = :ID;";
					}

					$db->beginTransaction();
					$prep = $db->prepare($sql);
					if ($idEvent > 0)
						$prep->bindValue( ':ID', $idEvent );
					$prep->bindValue( ':EventDate', str_replace("T", " ", $_POST['txtEventDate']) );
					$prep->bindValue( ':Host', $host == 0 ? null : $host);
					$prep->bindValue( ':ptVenues',$venue == 0 ? null : $venue);
					$prep->bindValue( ':ptBooks', $book == 0 ? null : $book);
					$pdosu = $prep->execute();

					// If host and book are known, set ChosenBy in books
					if ($host > 0 && $book > 0) {
						$qry = $db->query("UPDATE books SET ChosenBy=$host WHERE ID=$book;");
						$qry->execute();
						setStatus( $db, $_POST['lstBook'], 2 );
					}
					// If book has been changed reset ChosenBy and Status for previous book
					if (isset($_POST['last_book_id']) && $_POST['last_book_id'] > 0 && $_POST['last_book_id'] != $book) {
						$qry = $db->query("UPDATE books SET ChosenBy=null, Status=0 WHERE ID=" . $_POST['last_book_id']);
						$qry->execute();
					}
					$db->commit();

					$prep = NULL;
					break;

				case -1: // Delete
					// First must set any ptBooks to 0
					if ( isset($_POST['lstBook']) && $_POST['lstBook'] != NULL )
						setStatus( $db, $_POST['lstBook'], 0 );

					$prepd = $db->prepare("DELETE FROM calendar WHERE ID=:ID");
					$prepd->bindValue( ':ID', $idEvent );
					$pdosi = $prepd->execute();
					$prepd = NULL;
					echo "<script>window.close();</script>";
					break;
			}
			//$event_data = array('MeetingDate'=>$_POST['txtEventDate'], 'Host'=>$_POST['lstHost'], 'ptVenues'=>$_POST['lstVenue'], 'ptBooks'=>$_POST['lstBook']);
		}
	}
	catch(PDOException $ex) {
		$db->rollBack();
		include '../catch.php';
	}

	if ($idEvent == 0) {
		$event_data = array('MeetingDate'=>'', 'Host'=>0, 'ptVenues'=>0, 'ptBooks'=>0);
	}
	else {
		$action = "eventdetails2_admin.php?idEvent=$idEvent";
		$sql = "SELECT MeetingDate, Host, ptVenues, ptBooks FROM calendar WHERE ID=:ID";
		// print("\n<p>Get SQL=$sql<br>idEvent=$idEvent\n");
		$st = $db->prepare($sql);
		$st->bindValue(':ID', $idEvent);
		$st->execute();
		$event_data = $st->fetch(PDO::FETCH_ASSOC);
		$event_data['MeetingDate'] = str_replace(" ", "T", $event_data['MeetingDate']);
		if ($event_data['ptBooks'] == null)
			$event_data['ptBooks'] = 0;
	}
		//}

		// print( "<p>event_data: ");
		// print_r($event_data);
		// print("</p>\n");

?>

<header>
	<h2><?php print($idEvent == 0 ? "Add New" : "Edit") ?> Event Details</h2>
</header>
<form id="EventDetails" name="EventDetails" action="<?php echo $action ?>" method="post">
<div class="data-div" id="events">
	<div>
		<label for="txtEventDate">Event Date:</label>
		<input type="datetime-local" class="changer" id="txtEventDate" name="txtEventDate" value="<?php print($event_data['MeetingDate']) ?>">
	</div>
	<div>
		<label for="lstHost">Host:</label>
		<select class="changer" id="lstHost" name="lstHost">
			<option value="0"></option>
		<?php
			$stmt = $db->prepare("SELECT ID, KnownAs, CONCAT_WS(', ', Address1, IF( Address2='', NULL, Address2), Postcode) As Address FROM members WHERE Deleted=0 ORDER BY KnownAs;");
			$stmt->execute();
			while ($host =	$stmt->fetch(PDO::FETCH_NUM)) {
				$sel = $host[0] == $event_data['Host'] ? ' selected' : '';
				print("\t\t\t\t<option data-host='{\"address\":\"$host[2]\"}' value=\"$host[0]\"$sel>$host[1]</option>\n");
			}
		?>
		</select>
	</div>
	<div>
		<label for="lstVenue">Venue:</label>
		<select class="changer" id="lstVenue" name="lstVenue">
			<option value="0"></option>
		<?php
			$stmt = $db->prepare("SELECT ID, VenueName, CONCAT_WS(', ', Address1, IF( Address2='', NULL, Address2), Postcode) As Address FROM venues ORDER BY VenueName;");
			$stmt->execute();
			while ($venue =	$stmt->fetch(PDO::FETCH_NUM)) {
				$sel = $venue[0] == $event_data['ptVenues'] ? ' selected' : '';
				print("\t\t\t\t<option data-venue='{\"address\":\"$venue[2]\"}' value = \"$venue[0]\"$sel>$venue[1]</option>\n");
			}
		?>
		</select>
	</div>
	<div>
		<label for="lstBook">Book:</label>
		<select class="changer" id="lstBook" name="lstBook">
			<option value="0"></option>
		<?php
			$stmt = $db->prepare("SELECT ID, Title, Author FROM books WHERE Status=0 OR ID=" . $event_data['ptBooks'] . " ORDER BY Title;");
			$stmt->execute();
			while ($book =	$stmt->fetch(PDO::FETCH_NUM)) {
				$sel = $book[0] == $event_data['ptBooks'] ? ' selected' : '';
				print("\t\t\t\t<option data-book='{\"author\":\"$book[2]\"}' value=\"$book[0]\"$sel>$book[1]</option>\n");
			}
		?>
		</select>
	</div>
	<div>
		<input type="button" class="floatr changer" id="book_details" name="book_details" value="Book Details" <?php if ($event_data['ptBooks']==0) print (' disabled') ?> />
	</div>

	<!--div id="g_id_onload"
		data-client_id="832581782910-gfi16ds7bt18adhssup022lbbjt7j4ic.apps.googleusercontent.com"
		data-callback="handleCredentialResponse">
	</div>
	<div style="text-align: center" class="g_id_signin" data-type="standard"></div-->

	<div>
		<input type="button" id="cmdEvent2Cal" value="Add to Google Calendar" />
	</div>
	<div>
		<input type="button" id="cmdDelete" value="Delete" />
		<input type="button" class="floatr" id="cmdClose" name="cmdClose" value="Close" />
		<input type="button" class="floatr" id="cmdSave" value="Save" />
	</div>
	<div id="response">
	</div>
	<div id="msg">
			<span id="msg2"></span><br />
			<input type="button" class="confirm" id = "OK" value="OK">
			<input type="button" class="confirm" id = "Cancel" value="Cancel">
	</div>
	<div class="signout">
		<input type="button" id="signout_button" value="Sign Out from Google" />
	</div>
</div>
<input type="hidden" id="submitAction" name="submitAction" value="0">
<input type="hidden" id="last_book_id" name="last_book_id" value="<?php print($event_data['ptBooks']); ?>">

</form>

<script>
	var chg = 0;
	document.EventDetails.txtEventDate.focus();

	window.onunload = refreshParent;
	function refreshParent() {
		window.opener.location.reload(true);
	}

	$(document).ready(function(){
		$(".changer").on("change", function(){
			chg = 1;
		})
		$("#cmdSave").on("click", Save)

		function Save(){
			var e = document.getElementById("txtEventDate");
			var m = $("#msg2")[0];
			if (!e.validity.valid ) {
				m.innerHTML = "Date entry is not valid!";
				return;
			}
			if (e.value.length == 0) {
				m.innerHTML = "Date is required!";
				return;
			}
			document.getElementById('submitAction').value = 1;
			document.EventDetails.submit();
		}
		
		$("#cmdDelete").on("click", function(){
			$("#msg2")[0].innerHTML = "Are you sure you want to delete this event?";
			document.getElementById('submitAction').value = -1;
			$(".confirm").show();
		})
		$("#cmdClose").on("click", function() {
			var m = $("#msg2")[0];
			if (chg != 0) {
				m.innerHTML = "Data has changed: confirm close and lose changes?";
				$(".confirm").show();
				return;
			}
				
			window.close();
		})
		$(".confirm").on("click", function() {
			if (this.value == "OK") {
				document.EventDetails.submit();
			}
			else {
				$("#msg2")[0].innerHTML = "";
				$(".confirm").hide();
			}
		})
		$("#lstBook").on("change", function(){
			var idBook = document.getElementById("lstBook").value;
			var book_details = document.getElementById("book_details");
			book_details.disabled = idBook == 0;
		})
		$("#book_details").on("click", function(){
			Save(); // Otherwise the selected book is lost on return!
			var idBook = document.getElementById("lstBook").value;
			window.open(`./bookdetails2_admin.php?idBook=${idBook}`, 'Book Details', 'resizeable, left=200, top=200, height=450, width=800, location=0');
		})
		// Implement either/or for Host and Venue
		$("#lstHost").on("change", function(){
			var host = document.getElementById("lstHost").value;
			var eVenues = document.getElementById("lstVenue");
			if (host > 0) {
				eVenues.value = 0;
				eVenues.disabled = true;
			}
			else {
				eVenues.disabled = false;
			}
		})
		$("#lstVenue").on("change", function(){
			var venue = document.getElementById("lstVenue").value;
			var eHosts = document.getElementById("lstHost");
			if (venue > 0) {
				eHosts.value = 0;
				eHosts.disabled = true;
			}
			else {
				eHosts.disabled = false;
			}
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

	//document.getElementById('authorize_button').style.visibility = 'hidden';
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
			//document.getElementById('authorize_button').innerText = 'Refresh';
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



/*	function parseJwt (token) {
		var base64Url = token.split('.')[1];
		var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
		var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
			return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
		}).join(''));

	return JSON.parse(jsonPayload);
	}; 

	function handleCredentialResponse(response) {
		console.log(JSON.stringify(parseJwt(response.credential)));
	}
*/

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
			// However, if the meeting is at an outside venue the venue goes in the location and the book in the description

			var title;
			var description;
			var location;
			
			// HOST or Venue; Location
			var selH= document.getElementById("lstHost");
			var selV= document.getElementById("lstVenue");

			// BOOK
			var sel = document.getElementById("lstBook");
			var option_b = sel.options[sel.selectedIndex];
			var book = option_b.text;
			if ( book.length > 0 ) {
				book = `'${book}'`;
				var author = $(option_b).data("book").author;
				if ( author != "" )
					book += ' by ' + author;
			}

			var option_hv = selH.options[selH.selectedIndex];
			if ( option_hv.text != ''){
				title = 'Book Group - ' + option_hv.text;
				location = book;
				description = '';
			}
			else {
				option_hv = selV.options[selV.selectedIndex];
				title = `Book Group at ${option_hv.text}`;
				location = $(option_hv).data("venue").address;
				description = 'Reading ' + book;
			}

			console.log( "Host or Venue: " + option_hv.text + "\nLocation: " + location + "\nBook: " + book + "\nAuthor: " + author );
		
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

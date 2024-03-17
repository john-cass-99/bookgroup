	// Code for adding the event to Google Calendar

	const CLIENT_ID = '';
	const API_KEY = '';
	const SCOPES = 'https://www.googleapis.com/auth/calendar';

	let tokenClient;
	let gapiInited = false;
	let gisInited = false;

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

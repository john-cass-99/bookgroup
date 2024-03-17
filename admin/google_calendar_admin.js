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

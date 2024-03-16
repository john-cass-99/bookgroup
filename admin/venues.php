<?php
	include("./main.php");
	checkAdmin($application);
?>
<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./members_venues.css" type="text/css" media="screen,print"/>
	<title>Manage Venues</title>
</head>
<body>
	<h2>Manage Venue Details</h2>
<?php

	function updateField( $db, $id, $fieldName, $newValue )
	{
		try {
			if ( $newValue == 'Null' )
				$ust = $db->prepare("UPDATE venues SET $fieldName=NULL WHERE ID=:ID;");
			else {
				$ust = $db->prepare("UPDATE venues SET $fieldName=:$fieldName WHERE ID=:ID;");
				$ust->bindValue( ":$fieldName", $newValue );
			}
			$ust->bindValue( ':ID', $id );
			$ust->execute();
			return $newValue;
		}
		catch(PDOException $ex) {
			include '../catch.php';
		}
	}

	function print_quoted( $val )
	{
		print("\"$val\"");
	}

	try {
		include( '../../inf1.php' );

		if (isset($_GET['idVenue']))
			$idVenue = $_GET['idVenue'];
		else {
			if (isset($_POST['lstVenues']))
				$idVenue = $_POST['lstVenues'];
			else
				$idVenue = 0;
		}

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		// Data for Venue Dropdown List
		$stmt = $db->prepare("SELECT ID, VenueName FROM venues ORDER BY VenueName;");
		$stmt->execute();
		$venues = $stmt->fetchAll();

		// Data for Selected Venue
		$venue_fields = "VenueName, Website, Address1, Address2, City, Postcode, Telephone, Email";

		define( 'VENUENAME',   0 );
		define( 'WEBSITE',     1 );
		define( 'ADDRESS1',    2 );
		define( 'ADDRESS2',    3 );
		define( 'CITY',        4 );
		define( 'POSTCODE',    5 );
		define( 'TELEPHONE',   6 );
		define( 'EMAIL',       7 );

		$venue_data = array( '', '', '', '', '', '', '', '' );

		if ( $idVenue > 0 ) {
			if ( isset($_POST['IsSaving']) && $_POST['IsSaving'] == 2 ) {

				// DELETE VENUE

				$sql = "DELETE FROM venues WHERE ID=:ID";
				$prepi = $db->prepare($sql);
				$prepi->bindValue( ':ID', $idVenue );
				$pdosi = $prepi->execute();
				$prepi = NULL;
				echo "<script>window.location.replace('./venues.php?idVenue=0');</script>";
			} else {
				$mst = $db->prepare("SELECT $venue_fields FROM venues WHERE ID=:ID;");
				$mst->bindValue( ':ID', $idVenue );
				$mst->execute();
				$venue_data = $mst->fetch(PDO::FETCH_NUM);

				if ( isset($_POST['IsSaving']) && $_POST['IsSaving'] == 1 ) {
					// Handle any changed venue data
					if ( isset($_POST['txtVenueName']) )
						$venue_data[VENUENAME] = updateField( $db, $idVenue, 'VenueName', $_POST['txtVenueName'] );

					if ( isset($_POST['txtWebsite']) )
						$venue_data[WEBSITE] = updateField( $db, $idVenue, 'Website', $_POST['txtWebsite'] );

					if ( isset($_POST['txtAddress1']) )
						$venue_data[ADDRESS1] = updateField( $db, $idVenue, 'Address1', $_POST['txtAddress1'] );

					if ( isset($_POST['txtAddress2']) )
						$venue_data[ADDRESS2] = updateField( $db, $idVenue, 'Address2', $_POST['txtAddress2'] );

					if ( isset($_POST['txtCity']) )
						$venue_data[CITY] = updateField( $db, $idVenue, 'City', $_POST['txtCity'] );

					if ( isset($_POST['txtPostcode']) )
						$venue_data[POSTCODE] = updateField( $db, $idVenue, 'Postcode', $_POST['txtPostcode'] );

					if ( isset($_POST['txtTelephone']) )
						$venue_data[TELEPHONE] = updateField( $db, $idVenue, 'Telephone', $_POST['txtTelephone'] );

					if ( isset($_POST['txtEmail']) )
						$venue_data[EMAIL] = updateField( $db, $idVenue, 'Email', $_POST['txtEmail'] );

				}
			}

		}
		else if ( isset($_POST['IsSaving']) && $_POST['IsSaving'] == 1 ) {

			// Add New Venue
			$ast = $db->prepare("INSERT INTO venues ( $venue_fields ) VALUES ( :VenueName, :Website, " .
				":Address1, :Address2, :City, :Postcode, :Telephone, :Email );");

			$venue_data[VENUENAME] = isset( $_POST['txtVenueName'] ) ? $_POST['txtVenueName'] : '';
			$ast->bindValue( ':VenueName', $venue_data[VENUENAME] );

			$venue_data[WEBSITE] = isset( $_POST['txtWebsite'] ) ? $_POST['txtWebsite'] : '';
			$ast->bindValue( ':Website', $venue_data[WEBSITE] );

			$venue_data[ADDRESS1] = isset( $_POST['txtAddress1'] ) ? $_POST['txtAddress1'] : '';
			$ast->bindValue( ':Address1', $venue_data[ADDRESS1] );

			$venue_data[ADDRESS2] = isset( $_POST['txtAddress2'] ) ? $_POST['txtAddress2'] : '';
			$ast->bindValue( ':Address2', $venue_data[ADDRESS2] );

			$venue_data[CITY] = isset( $_POST['txtCity'] ) ? $_POST['txtCity'] : '';
			$ast->bindValue( ':City', $venue_data[CITY] );

			$venue_data[POSTCODE] = isset( $_POST['txtPostcode'] ) ? $_POST['txtPostcode'] : '';
			$ast->bindValue( ':Postcode', $venue_data[POSTCODE] );

			$venue_data[TELEPHONE] = isset( $_POST['txtTelephone'] ) ? $_POST['txtTelephone'] : '';
			$ast->bindValue( ':Telephone', $venue_data[TELEPHONE] );

			$venue_data[EMAIL] = isset( $_POST['txtEmail'] ) ? $_POST['txtEmail'] : '';
			$ast->bindValue( ':Email', $venue_data[EMAIL] );

			try {
				$ast->execute();
				$lid = $db->query( "SELECT LAST_INSERT_ID();" );
				$newID = $lid->fetchColumn(0);
				echo "<script>window.location.replace(\"./venues.php?idVenue=$newID\");</script>";
			}
			catch(PDOException $ex) {
				include '../catch.php';
			}


		}

	}
	catch(PDOException $ex) {
		include '../catch.php';
	}
	catch(\Exception $ex) {
		print "<p>An error has occurred</p>";
		print '<p>' . $ex->getMessage() . '</p>';
		error_log( $ex->getMessage() );
	}

?>

	<form name="DataRecord" action="./venues.php" method="post">
		<div id="member_data">
			<div>
				<label for="lstVenues">Select Venue:</label>
				<select id="lstVenues" name="lstVenues" class="medium_txt" onchange="this.form.submit()">
					<option value="0">Add New Venue</option>
<?php
					foreach ( $venues as $m )
					{
						if ($idVenue > 0 )
							$sel = ( $m[0] == $idVenue ) ? " selected" : "";
						else
							$sel = '';
						echo "\t\t\t\t\t<option value = \"$m[0]\"$sel>" . $m[1] . "</option>\n";
					}
					unset( $m );
?>
				</select>
			</div>

			<div>
				<label for="txtVenueName">VenueName:</label>
				<input type="text" name="txtVenueName" class="medium_txt" 
					required value=<?php print_quoted($venue_data[VENUENAME]) ?>>
			</div>

			<div>
				<label for="txtWebsite">Website:</label>
				<input type="text" name="txtWebsite" class="medium_txt" 
					value=<?php print_quoted($venue_data[WEBSITE]) ?>></label>
			</div>

			<div>
				<label for="txtAddress1">Address1:</label>
				<input type="text" name="txtAddress1" class="medium_txt" 
					value=<?php print_quoted($venue_data[ADDRESS1]) ?>>
			</div>

			<div>
				<label for="txtAddress2">Address2:</label>
				<input type="text" name="txtAddress2" class="medium_txt" 
					value=<?php print_quoted($venue_data[ADDRESS2]) ?>>
			</div>

			<div>
				<label for="txtCity">City:</label>
				<input type="text" name="txtCity" class="medium_txt" 
					value=<?php print_quoted($venue_data[CITY]) ?>>
			</div>

			<div>
				<label for="txtPostcode">Postcode:</label>
				<input type="text" name="txtPostcode" class="medium_txt" 
					value=<?php print_quoted($venue_data[POSTCODE]) ?>>
			</div>

			<div>
				<label for="txtTelephone">Telephone:</label>
				<input type="text" name="txtTelephone" class="medium_txt" 
					value=<?php print_quoted($venue_data[TELEPHONE]) ?>>
			</div>

			<div>
				<label for="txtEmail">Email:</label>
				<input type="text" name="txtEmail" class="long_txt" value=<?php print_quoted($venue_data[EMAIL]) ?>>
			</div>

			<div id="buttons">
				<button id="cmdDelete"  onClick="DeleteRecord('Venue')">Delete</button>
				<button class="form_button" id="cmdSave"  onClick="SaveData()">Save</button>
				<button class="form_button" id="cmdClose" onClick="window.close()">Close</button>
				<input type="hidden" name="IsSaving" value="0">
			</div>
		</div>
	</form>

	<script src="./members_venues.js"></script>

  </body>
</html>

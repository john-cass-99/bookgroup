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
	<title>Manage Members</title>
</head>
<body>
	<h2>Manage Member Details</h2>
<?php

	function updateField( $db, $id, $fieldName, $newValue, &$error )
	{
		try {
			if ( $newValue == 'Null' )
				$ust = $db->prepare("UPDATE members SET $fieldName=NULL WHERE ID=:ID;");
			else {
				$ust = $db->prepare("UPDATE members SET $fieldName=:$fieldName WHERE ID=:ID;");
				$ust->bindValue( ":$fieldName", $newValue );
			}
			$ust->bindValue( ':ID', $id );
			$ust->execute();
			return $newValue;
		}
		catch(PDOException $ex) {
			$dupent = stristr($ex->getMessage(),'Duplicate entry ');
			if ($dupent){
				$error = $dupent;
			}
			else {
				if (isset($showPDOErrors) && $showPDOErrors) {
					print("<p>PDO Error: " . $ex->getMessage() . "</p>");
				}
				else {
					$error = $ex->getMessage();
					//print( "<p>An error has occurred\r\n</p>");
					error_log($ex->getMessage());
				}			
			}
			}
	}

	function print_quoted( $val )
	{
		print("\"$val\"");
	}

	// print("\n<p>POST: ");
	// print_r($_POST);
	// print("</p>\n");

	try {
		include( '../../inf1.php' );
		$showPDOErrors = TRUE;
		$error = '';

		if (isset($_GET['idMember']))
			$idMember = $_GET['idMember'];
		else {
			if (isset($_POST['lstMembers']))
				$idMember = $_POST['lstMembers'];
			else
				$idMember = 0;
		}

		$whereDeleted = ' WHERE Deleted=0';
		if (isset($_POST['showDeleted']))
			$whereDeleted = '';

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		// Need the admin status of the logged in user - must = 2 to be able to edit admin status
		$admin = 0;
		$stmt = $db->query('SELECT Admin FROM members WHERE ID=' . $_SESSION['login'][$application]['id']);
		$admin = $stmt->fetchColumn();

		// Data for Member Dropdown List
		$stmt = $db->prepare("SELECT ID,KnownAs FROM members" . $whereDeleted . " ORDER BY KnownAs;");
		$stmt->execute();
		$members = $stmt->fetchAll();

		// Data for Selected Member
		$member_fields = "Forename, Surname, KnownAs, Login, Address1, Address2, City, Postcode, Telephone, Mobile, Email, DateJoined, SuggestionsOnly, Admin";

		define( 'FORENAME',    0 );
		define( 'SURNAME',     1 );
		define( 'KNOWNAS',     2 );
		define( 'LOGIN',       3 );
		define( 'ADDRESS1',    4 );
		define( 'ADDRESS2',    5 );
		define( 'CITY',        6 );
		define( 'POSTCODE',    7 );
		define( 'TELEPHONE',   8 );
		define( 'MOBILE',      9 );
		define( 'EMAIL',       10 );
		define( 'DATEJOINED',  11 );
		define( 'SUGGESTIONS_ONLY', 12);
		define( 'ADMIN', 13);

		$member_data = array( '', '', '', '', '', '', '', '', '', '', '', '', 0, 0 );

		if ( $idMember > 0 ) {
			if ( isset($_POST['IsSaving']) && $_POST['IsSaving'] == 2 ) {

				// SET MEMBER DELETED

				$sql = "UPDATE members SET Deleted=1 WHERE ID=:ID";
				$prepi = $db->prepare($sql);
				$prepi->bindValue( ':ID', $idMember );
				$pdosi = $prepi->execute();
				$prepi = NULL;
				echo "<script>window.location.replace('./members.php?idMember=0');</script>";
			} else {
				$mst = $db->prepare("SELECT $member_fields FROM members WHERE ID=:ID;");
				$mst->bindValue( ':ID', $idMember );
				$mst->execute();
				$member_data = $mst->fetch(PDO::FETCH_NUM);
				// Convert the date string to datetime
				if ($member_data[DATEJOINED] != NULL )
					$member_data[DATEJOINED] = new \DateTime($member_data[DATEJOINED]);

				if ( isset($_POST['IsSaving']) && $_POST['IsSaving'] == 1 ) {
					// Handle any changed member data
					if ( isset($_POST['txtForename']) )
						$member_data[FORENAME] = updateField( $db, $idMember, 'Forename', $_POST['txtForename'], $error );

					if ( isset($_POST['txtSurname']) )
						$member_data[SURNAME] = updateField( $db, $idMember, 'Surname', $_POST['txtSurname'], $error );

					if ( isset($_POST['txtKnownAs']) )
						$member_data[KNOWNAS] = updateField( $db, $idMember, 'KnownAs', $_POST['txtKnownAs'], $error );

					if ( isset($_POST['txtLogin']) )
						$member_data[LOGIN] = updateField( $db, $idMember, 'Login', $_POST['txtLogin'], $error );

					if ( isset($_POST['txtAddress1']) )
						$member_data[ADDRESS1] = updateField( $db, $idMember, 'Address1', $_POST['txtAddress1'], $error );

					if ( isset($_POST['txtAddress2']) )
						$member_data[ADDRESS2] = updateField( $db, $idMember, 'Address2', $_POST['txtAddress2'], $error );

					if ( isset($_POST['txtCity']) )
						$member_data[CITY] = updateField( $db, $idMember, 'City', $_POST['txtCity'], $error );

					if ( isset($_POST['txtPostcode']) )
						$member_data[POSTCODE] = updateField( $db, $idMember, 'Postcode', $_POST['txtPostcode'], $error );

					if ( isset($_POST['txtTelephone']) )
						$member_data[TELEPHONE] = updateField( $db, $idMember, 'Telephone', $_POST['txtTelephone'], $error );

					if ( isset($_POST['txtMobile']) )
						$member_data[MOBILE] = updateField( $db, $idMember, 'Mobile', $_POST['txtMobile'], $error );

					if ( isset($_POST['txtEmail']) )
						$member_data[EMAIL] = updateField( $db, $idMember, 'Email', $_POST['txtEmail'], $error );

					if ( isset($_POST['dtJoined']) ) {
						if ( $_POST['dtJoined'] == '' ) {
							$member_data[DATEJOINED] = NULL;
							updateField( $db, $idMember, 'DateJoined', 'Null', $error );
						}
						else
							$member_data[DATEJOINED] = new \DateTime(updateField( $db, $idMember, 'DateJoined', $_POST['dtJoined'], $error ) );
					}
					else
						$member_data[DATEJOINED] = NULL;

					$member_data[SUGGESTIONS_ONLY] = isset($_POST['SuggestionsOnly']) ? 1 : 0;
					$member_data[ADMIN] = isset($_POST['admin']) ? 1 : 0;
					updatefield( $db, $idMember, 'SuggestionsOnly', $member_data[SUGGESTIONS_ONLY], $error);
					updatefield( $db, $idMember, 'Admin', $member_data[ADMIN], $error);

				}
			}

		}
		else if ( isset($_POST['IsSaving']) && $_POST['IsSaving'] == 1 ) {

			// Add New Member
			$ast = $db->prepare("INSERT INTO members (" . $member_fields . ', password' . ") VALUES ( :Forename, :Surname, :KnownAs, :Login, " .
				":Address1, :Address2, :City, :Postcode, :Telephone, :Mobile, :Email, :DateJoined, :SuggestionsOnly, :Admin, :password );");

			$member_data[FORENAME] = isset( $_POST['txtForename'] ) ? $_POST['txtForename'] : '';
			$ast->bindValue( ':Forename', $member_data[FORENAME] );

			$member_data[SURNAME] = isset( $_POST['txtSurname'] ) ? $_POST['txtSurname'] : '';
			$ast->bindValue( ':Surname', $member_data[SURNAME] );

			$member_data[KNOWNAS] = isset( $_POST['txtKnownAs'] ) ? $_POST['txtKnownAs'] : '';
			$ast->bindValue( ':KnownAs', $member_data[KNOWNAS] );

			$member_data[LOGIN] = isset( $_POST['txtLogin'] ) ? $_POST['txtLogin'] : null;
			$ast->bindValue( ':Login', $member_data[LOGIN] );

			$member_data[ADDRESS1] = isset( $_POST['txtAddress1'] ) ? $_POST['txtAddress1'] : '';
			$ast->bindValue( ':Address1', $member_data[ADDRESS1] );

			$member_data[ADDRESS2] = isset( $_POST['txtAddress2'] ) ? $_POST['txtAddress2'] : '';
			$ast->bindValue( ':Address2', $member_data[ADDRESS2] );

			$member_data[CITY] = isset( $_POST['txtCity'] ) ? $_POST['txtCity'] : '';
			$ast->bindValue( ':City', $member_data[CITY] );

			$member_data[POSTCODE] = isset( $_POST['txtPostcode'] ) ? $_POST['txtPostcode'] : '';
			$ast->bindValue( ':Postcode', $member_data[POSTCODE] );

			$member_data[TELEPHONE] = isset( $_POST['txtTelephone'] ) ? $_POST['txtTelephone'] : '';
			$ast->bindValue( ':Telephone', $member_data[TELEPHONE] );

			$member_data[MOBILE] = isset( $_POST['txtMobile'] ) ? $_POST['txtMobile'] : '';
			$ast->bindValue( ':Mobile', $member_data[MOBILE] );

			$member_data[EMAIL] = isset( $_POST['txtEmail'] ) ? $_POST['txtEmail'] : '';
			$ast->bindValue( ':Email', $member_data[EMAIL] );

			$member_data[DATEJOINED] =	isset( $_POST['dtJoined'] ) ? $_POST['dtJoined'] : '';
			$ast->bindValue( ':DateJoined', $member_data[DATEJOINED] == '' ? NULL : $member_data[DATEJOINED] );
			if ( $member_data[DATEJOINED] != NULL )
				$member_data[DATEJOINED] = new \DateTime($member_data[DATEJOINED]);

			$member_data[SUGGESTIONS_ONLY] = isset( $_POST['SuggestionsOnly']) ? 1 : 0;
			$ast->bindValue( ':SuggestionsOnly', $member_data[SUGGESTIONS_ONLY] );

			$member_data[ADMIN] = isset( $_POST['admin']) ? 1 : 0;
			$ast->bindValue( ':Admin', $member_data[ADMIN] );

			$ast->bindValue(':password', '$2y$10$XwfZ80n205FDRV6WJvFSv.DurVy5.c0t7bnCalrjc7ulhkpiUVoQ6'); // = changeme

			try {
				$ast->execute();
				$lid = $db->query( "SELECT LAST_INSERT_ID();" );
				$newID = $lid->fetchColumn(0);
				echo "<script>window.location.replace(\"./members.php?idMember=$newID\");</script>";
			}
			catch(PDOException $ex) {
				throw $ex;
			}


		}

	}
	catch(PDOException $ex) {
		$dupent = stristr($ex->getMessage(),'Duplicate entry ');
		if ($dupent){
			$error = $dupent;
		}
		else {
			if (isset($showPDOErrors) && $showPDOErrors) {
				print("<p>PDO Error: " . $ex->getMessage() . "</p>");
			}
			else {
				$error = $ex->getMessage();
				//print( "<p>An error has occurred\r\n</p>");
				error_log($ex->getMessage());
			}			
		}
}
	catch(\Exception $ex) {
		$error = $ex->getMessage();
		error_log( $ex->getMessage() );
	}

?>

	<form name="DataRecord" action="./members.php" method="post" autocomplete="off">
	<div id="member_data">
		<div>
		<label for="lstMembers">Select Member:</label>
		<select id="lstMembers" name="lstMembers" class="mid_select" onchange="this.form.submit()">
			<option value="0">Add New Member</option>
<?php
	foreach ( $members as $m )
	{
		if ($idMember > 0 )
			$sel = ( $m[0] == $idMember ) ? " selected" : "";
		else
			$sel = '';
		echo "\t\t\t<option value=\"" . $m[0] . "\"" . $sel . ">" . $m[1] . "</option>\n";
	}
	unset( $m );
?>
		</select>
		</div>
		<div>
			<label for="txtForename">Forename:</label>
			<input type="text" class="medium_txt" id="txtForename" name="txtForename"
				required value=<?php print_quoted($member_data[FORENAME]) ?>>
		</div>

		<div>
			<label for="txtSurname">Surname:</label>
			<input type="text" class="medium_txt" id="txtSurname" name="txtSurname" 
				required  value=<?php print_quoted($member_data[SURNAME]) ?>>
		</div>

		<div>
			<label for="txtKnownAs">KnownAs:</label>
			<input type="text" class="short_txt" id="txtKnownAs" name="txtKnownAs"
				required  value=<?php print_quoted($member_data[KNOWNAS]) ?>>
		</div>

		<div>
			<label for="txtLogin">Login:</label>
			<input type="text" class="short_txt" id="txtLogin" name="txtLogin" 
				required value=<?php print_quoted($member_data[LOGIN]) ?>>
		</div>

		<div>
			<label for="txtAddress1">Address Line 1:</label>
			<input type="text" class="medium_txt" id="txtAddress1" name="txtAddress1" 
				value=<?php print_quoted($member_data[ADDRESS1]) ?>>
		</div>

		<div>
			<label for="txtAddress2">Address Line 2:</label>
			<input type="text" class="medium_txt" id="txtAddress2" name="txtAddress2" 
				value=<?php print_quoted($member_data[ADDRESS2]) ?>>
		</div>

		<div>
			<label for="txtCity">City:</label>
			<input type="text" class="medium_txt" id="txtCity" name="txtCity" 
				value=<?php print_quoted($member_data[CITY]) ?>>
		</div>

		<div>
			<label for="txtPostcode">Postcode:</label>
			<input type="text" class="short_txt" id="txtPostcode" name="txtPostcode" 
				value=<?php print_quoted($member_data[POSTCODE]) ?>>
		</div>

		<div>
			<label for="txtTelephone">Telephone:</label>
			<input type="text" class="medium_txt" id="txtTelephone" name="txtTelephone" 
				value=<?php print_quoted($member_data[TELEPHONE]) ?>>
		</div>

		<div>
			<label for="txtMobile">Mobile:</label>
			<input type="text" class="medium_txt" id="txtMobile" name="txtMobile" 
				value=<?php print_quoted($member_data[MOBILE]) ?>>
		</div>

		<div>
			<label for="txtEmail">Email:</label>
			<input type="text" class="long_txt" id="txtEmail" name="txtEmail" 
				 required value=<?php print_quoted($member_data[EMAIL]) ?>>
		</div>

		<div>
			<label for="dtJoined">Date Joined:</label>
<?php
		if ( $member_data[DATEJOINED] == NULL )
			$value = NULL;
		else
			$value = date_format($member_data[DATEJOINED], "Y-m-d");
?>
			<input type="date" id="dtJoined" name="dtJoined" value=<?php print("\"$value\"") ?>>
		</div>

		<div>
			<label for="SuggestionsOnly">Suggestions Only:&nbsp;</label>
			<input type="checkbox" id="SuggestionsOnly" name="SuggestionsOnly"<?php if ($member_data[SUGGESTIONS_ONLY] != 0) print ' checked'; ?> />
		</div>
		<div>
			<label class="plain" for="admin">Admin:&nbsp;</label>
			<input type="checkbox" id="admin" name="admin" onclick="" <?php if ($member_data[ADMIN]) print(' checked');  if ($_SESSION['login'][$application]['admin'] < 2 || $idMember == $_SESSION['login'][$application]['id']) print(' disabled')?>>
		</div>

		<div id="buttons">
			<button id="cmdDelete"  onClick="DeleteRecord('Member')">Delete</button>
			<button class="form_button" id="cmdSave"  onClick="SaveData()">Save</button>
			<button class="form_button" id="cmdClose" onClick="window.close()">Close</button>
			<input type="hidden" name="IsSaving" value="0">
		</div>
		<div>
			<label class="plain" for="showDeleted">Show Deleted:&nbsp;</label>
			<input type="checkbox" id="showDeleted" name="showDeleted" onclick="DataRecord.submit()" <?php if (isset($_POST['showDeleted'])) print(' checked') ?>>
		</div>
		<div>
			<textarea id="errMessage" name="errMessage" cols="50" rows="5" readonly><?php print($error) ?></textarea>
		</div>

	</div>
	</form>

	<script src="./members_venues.js"></script>

  </body>
</html>

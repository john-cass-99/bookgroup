<?php
	include('./main.php');
	checkLoggedIn($application, './edit_details.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Edit Details</title>
	<link href="./login-style.css" rel="stylesheet" type="text/css">
	<script src="https://kit.fontawesome.com/f89b070702.js" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
<?php
	// print("<p>curdir=" . getcwd());
	// print("<p>Posted Values:</p>\n<p>");
	// print_r($_POST);
	print("</p>\n");
	$return = '';
	if (isset($_GET['return'])) {
		$return = $_GET['return'];
		// print('<p>return=' . $_GET['return'] . '</p>');
	}
	try {
		$msg = '';
		$row = array('Forename'=>'','Surname'=>'','Address1'=>'','Address2'=>'',
			'City'=>'','Postcode'=>'','Telephone'=>'','Mobile'=>'','Email'=>'');

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if(isset($_POST['save'])  && $_POST['save'] == 1) {
			$row['Forename'] = $_POST['forename'];
			$row['Surname'] = $_POST['surname'];
			$row['Address1'] = $_POST['address1'];
			$row['Address2'] = $_POST['address2'];
			$row['City'] = $_POST['city'];
			$row['Postcode'] = $_POST['postcode'];
			$row['Telephone'] = $_POST['telephone'];
			$row['Mobile'] = $_POST['mobile'];
			$row['Email'] = $_POST['email'];

			$sql = "UPDATE members SET Forename=:forename,Surname=:surname,Address1=:address1,Address2=:address2," . 
				"City=:city,Postcode=:postcode,Telephone=:telephone,Mobile=:mobile,Email=:email" . 
				" WHERE ID=:id;";
			$stmt = $db->prepare( $sql );
			$stmt->bindValue(':forename', $row['Forename']);
			$stmt->bindValue(':surname', $row['Surname']);
			$stmt->bindValue(':address1', $row['Address1']);
			$stmt->bindValue(':address2', $row['Address2']);
			$stmt->bindValue(':city', $row['City']);
			$stmt->bindValue(':postcode', $row['Postcode']);
			$stmt->bindValue(':telephone', $row['Telephone']);
			$stmt->bindValue(':mobile', $row['Mobile']);
			$stmt->bindValue(':email', $row['Email']);
			$stmt->bindValue(':id',$_SESSION['login'][$application]['id']);
			$stmt->execute();
		}
		else {
			$sql = "SELECT Forename,Surname,Address1,Address2,City,Postcode,Telephone,Mobile,Email" . 
				" FROM members WHERE ID=:id;";
			$stmt = $db->prepare( $sql );
			$stmt->bindValue(':id',$_SESSION['login'][$application]['id']);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
			if (!$row) {
				$msg = "Failed to get data!";
			}
		}
	}
	catch(PDOException $ex) {
		$msg =  "An error has occurred!";
		if (isset($showPDOErrors) && $showPDOErrors) {
			$msg .= "\n" . $ex->getMessage();
		}
		error_log( $ex->getMessage() );
	}
?>

	<div class="details">
		<h1>Edit Details for <?php print($_SESSION['login'][$application]['user']); ?></h1>
		<form id="edit_details" action="<?php print("\"$return\""); ?>" method="post" autocomplete="off">
		<table>
			<tr>
				<td><label for="forename"><i class="fas fa-user"></i></label>
				<input type="text" id="forename"  name="forename" placeholder="Forename" required 
					value="<?php print($row['Forename']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="surname"><i class="fas fa-user"></i></label>
				<input type="text" id="surname"  name="surname" placeholder="Surname" required 
					value="<?php print($row['Surname']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="address1"><i class="fas fa-address-card"></i></label>			
				<input type="text" id="address1"  name="address1" placeholder="Address line 1" 
					value="<?php print($row['Address1']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="address2"><i class="fas fa-address-card"></i></label>
				<input type="text" id="address2"  name="address2" placeholder="Address line 2" 
					value="<?php print($row['Address2']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="city"><i class="fas fa-city"></i></label>
				<input type="text" id="city"  name="city" placeholder="City" 
					value="<?php print($row['City']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="postcode"><i class="fas fa-mail-bulk"></i></label>
				<input type="text" id="postcode"  name="postcode" placeholder="Postcode" 
					value="<?php print($row['Postcode']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="telephone"><i class="fas fa-phone-square-alt"></i></label>
				<input type="text" id="telephone"  name="telephone" placeholder="Landline" 
					value="<?php print($row['Telephone']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
				<td><label for="mobile"><i class="fas fa-mobile-alt"></i></label>
				<input type="text" id="mobile"  name="mobile" placeholder="Mobile" 
					value="<?php print($row['Mobile']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
            	<td><label for="email"><i class="fas fa-envelope"></i></label>
				<input type="email" name="email" placeholder="Email" id="email" 
					value="<?php print($row['Email']) ?>" onchange="SetChange()"></td>
			</tr>

			<tr>
			<td class="msg"><?php print($msg); ?></td>
			<tr>
				<td><input id="cancel" type="button" value="Close" onclick="CancelEdit()">
				<input type="button" value="Save" onclick="SaveData()"></td>
			</tr>
		</table>
		<input type="hidden" id="save" name="save" value="0">
		</form>
		<input type="hidden" id="chg" name="chg" value="0">
	</div>
	<script>
		function SetChange() {
			if (document.getElementById('chg').value == "0") {
				document.getElementById('chg').value = "1";
				document.getElementById('cancel').value = "Cancel";
			}
		}

		function SaveData() {
			document.getElementById('save').value = "1";
			document.getElementById('chg').value = "0";
			document.getElementById('edit_details').submit();
		}

		function CancelEdit() {
			if (document.getElementById('chg').value == "1") {
					if (!confirm('Data has changed: quit and lose changes?')) {
						return;
					}
			}
			window.location = <?php print("\"$return\""); ?>;
		}
	</script>
</body>
</html>
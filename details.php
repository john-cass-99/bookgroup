<?php
	include('./main.php');
	checkLoggedIn($application, 'details.php');
?>
<!DOCTYPE html>
<html>
	<head>	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./bookgroup.css" type="text/css" media="screen,print"/>
	<title>Book Group Members</title>
	<script src="./dropdown.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
	<?php
		include "./bookgroup_name.php";
	?>
	<h1>Member&nbsp;Details</h1>
	<div class="links">
		<?php include "./dropdown.php"; ?>
		<?php include "./dropdown2.php"; ?>
	</div>
	<table>
	  <tr>
		<th>Name</th>
		<th>Address</th>
		<th>Postcode</th>
		<th>Telephone</th>
		<th>Mobile</th>
		<th>Email</th>
	  </tr>
<?php
	try {
		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT CONCAT(Forename,' ',Surname) AS Name,Address1,Postcode,Telephone,Mobile,Email FROM members WHERE Deleted=0 AND Login<>'' ORDER BY Surname;";
		$pdos = $db->query( $sql );
		if ( $pdos ) {
			while ($row = $pdos->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>$row[0]</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[1]) ? "&nbsp;" : $row[1] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[2]) ? "&nbsp;" : $row[2] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[3]) ? "&nbsp;" : $row[3] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[4]) ? "&nbsp;" : $row[4] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[5]) ? "&nbsp;" : "<a href=\"mailto:" . $row[5] . "?subject=Book%20Group\">" . $row[5] ) . "</a></td>\n";
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
?>
	</table>
</body>
</html>

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
	<title>Logins</title>
</head>
<body>
<?php
		include( '../../inf1.php' );
?>
	<div>
		<h1 class="h1mod">Recent Logins</h1>
	</div>
	<br />
	<table class="main" name="tblLogins" id="tblLogins">
		<tr>
			<th>ID</th>
			<th>Member</th>
			<th>Date/Time Logged In</th>
		</tr>	
<?php
	try {
		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql = "SELECT L.ptUser, CONCAT_WS(' ', M.Forename, M.Surname) As Name, L.LoggedIn
			FROM logins L INNER JOIN members M
				ON L.ptUser = M.ID
			ORDER BY L.LoggedIn DESC LIMIT 25";
	
		$pdos = $db->query( $sql );
		if ( $pdos ) {
			while ($row = $pdos->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>" . $row[0] . "</td>\n";
				echo "\t\t\t<td>" . $row[1] . "</td>\n";
				echo "\t\t\t<td>" . $row[2] . "</td>\n";
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
</body>
</html>
<?php
	include('./main.php');
	checkLoggedIn($application, './suggestions.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./bookgroup.css" type="text/css" media="screen,print"/>
	<title>Book Group</title>
	<script src="./dropdown.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
	<?php
		include "./bookgroup_name.php";
	?>
	<h1>Suggested&nbsp;Books</h1>
	<div class="links">
		<?php include "./dropdown.php"; ?>
		<?php include "./dropdown2.php"; ?>
	</div>
	<p id="clickMsg">Click on column heading to sort</p>
	<table id="sugBooks">
	  <tr>
		<th onclick="sortTable('sugBooks', 6)">Book Title</th>
		<th onclick="sortTable('sugBooks', 1)">Author</th>
		<th onclick="sortTable('sugBooks', 2)">Suggested By</th>
		<th onclick="sortTable('sugBooks', 5)">Date Entered</th>
		<th onclick="sortTable('sugBooks', 4)">Kindle Price</th>
	  </tr>
<?php
	   define( 'ID',        0 );
	   define( 'TITLE',     1 );
	   define( 'AUTHOR',    2 );
	   define( 'LINK',      3 );
	   define( 'DATE',      4 );
	   define( 'SUGBY',     5 );
	   define( 'STATUS',    6 );
	   define( 'PRICE',     7 );
	   define( 'SORT_DATE', 8 );

	try {
		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);        
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdos = $db->query("SELECT ID, Title, Author, AmazonLink, DateEntered, KnownAs, Status, KindlePrice, SortDate from vwsuggestions");
		if ( $pdos ) {
			while ($row = $pdos->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				echo "\t\t<tr>\n";
				echo "\t\t\t<td>" . ( is_null($row[LINK]) || $row[LINK] == '' ? $row[TITLE] : ("<a href=\"" . str_replace('&','&amp;',$row[LINK]) . "\">" . $row[TITLE] . "</a>") ) . "</td>\n";                
				echo "\t\t\t<td>" . ( is_null($row[AUTHOR]) ? "&nbsp;" : $row[AUTHOR] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[SUGBY]) ? "&nbsp;" : $row[SUGBY] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[DATE]) ? "&nbsp;" : $row[DATE] ) . "</td>\n";
				echo "\t\t\t<td style=\"text-align: right\">" . ( is_null($row[PRICE]) ? "-" : $row[PRICE] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[SORT_DATE]) ? "&nbsp;" : $row[SORT_DATE] ) . "</td>\n";
				echo "\t\t\t<td>" . $row[TITLE] . "</td>\n";
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
	<script src="../scripts/sort.js">
	</script>
</body>
</html>

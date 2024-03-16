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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<title>Suggested Books</title>
  </head>
  <body>
	<?php
		include( '../../inf1.php' );
		include( './status.php');
		// print('<p>POST:');
		// print_r($_POST);
		// print("</p>\n");

		if( !isset($_POST['filter'])) { // i.e. if initial entry, not after post 
			$twoYears = true;
			$_POST['filter'] = Status::SUGGESTED;
			$_POST['two_years'] = 'on';
		}
		else
			$twoYears = isset($_POST['two_years']) && $_POST['two_years'] == 'on';
	?>
	<form id="filter_form" name="filter_form" method="post">
	<div>
		<h1 class="h1mod">Suggested Books</h1>
	</div>
	<div>
		<input type="button" id="AddBook" value="Add Book" onClick="EditRow(0)">
	</div>
	<div>
		<select id="filter" name="filter">
<?php
	print("\t\t\t<option value=\"" . Status::SUGGESTED . "\"" . ($_POST['filter'] == Status::SUGGESTED ? ' selected' : '') . ">" . Status::ToString(Status::SUGGESTED) . "</option>\n");
	print("\t\t\t<option value=\"" . Status::CHOSEN . "\"" . ($_POST['filter'] == Status::CHOSEN ? ' selected' : '') . ">" . Status::ToString(Status::SUGGESTED) . ' &amp; ' . Status::ToString(Status::CHOSEN) . "</option>\n");
	print("\t\t\t<option value=\"" . Status::DELETED . "\"" . ($_POST['filter'] == Status::DELETED ? ' selected' : '') . ">All</option>\n");
?>			
		</select>
		<label for="two_years" style="font-size: 12pt">Restrict to 2 years data</label>
		<input type="checkbox" id="two_years" name="two_years"<?php print($twoYears ? ' checked' : '')?> />
	</div>
	</form>
	<p id="SortMsg">Click on column heading to sort</p>
	<table id="tblBooks" class="main suggestions">
	  <tr>
		<th onclick="sortTable('tblBooks', 0)">ID</th>
		<th onclick="sortTable('tblBooks', 1)">Book Title</th>
		<th onclick="sortTable('tblBooks', 2)">Author</th>
		<th onclick="sortTable('tblBooks', 3)">Status</th>
		<th onclick="sortTable('tblBooks', 4)">Suggested By</th>
		<th onclick="sortTable('tblBooks', 8)">Date Entered</th>
		<th onclick="sortTable('tblBooks', 6)">Chosen By</th>
		<th onclick="sortTable('tblBooks', 9)">Date Read</th>
	  </tr>
<?php
	try {
		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT ID,Title,Author,DateEntered,SuggestedBy,Status,ChosenBy,DateRead FROM vwsuggestionsadmin WHERE Status <= " . $_POST['filter'];
		if (isset($_POST['two_years']))
			$sql .= " AND ((to_days(now()) - to_days(DateEntered)) < 730)";
		$pdos = $db->query( $sql );
		if ( $pdos ) {
			while ($row = $pdos->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
				echo "\t\t<tr onClick=\"EditRow($row[0])\">\n";
				echo "\t\t\t<td>" . str_pad(strval($row[0]), 4, " ", STR_PAD_LEFT) . "</td>\n";
				echo "\t\t\t<td>$row[1]</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[2]) ? "&nbsp;" : $row[2] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[5]) ? "&nbsp;" : Status::ToString($row[5]) ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[4]) ? "&nbsp;" : $row[4] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[3]) ? "&nbsp;" : date_format(DateTime::createFromFormat('Y-m-d H:i:s', $row[3]), 'd/m/Y') ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[6]) ? "&nbsp;" : $row[6] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[7]) ? "&nbsp;" : date_format(DateTime::createFromFormat('Y-m-d H:i:s', $row[7]), 'd/m/Y') ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[3]) ? "&nbsp;" : $row[3] ) . "</td>\n";
				echo "\t\t\t<td>" . ( is_null($row[7]) ? "&nbsp;" : $row[7] ) . "</td>\n";
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
  <script>

	function EditRow(ID) {
		//window.open( './bookdetails2_admin.php?idBook=' + ID, 'Book Details', 'resizeable, left=200, top=200, height=450, width=800, location=0' );
		window.open( './bookdetails2_admin.php?idBook=' + ID, '_blank');
	}

	$(document).ready(function(){

		$("#filter").on("change", function(){
			document.getElementById('filter_form').submit();
		});

		$("#two_years").on("change", function(){
			document.getElementById('filter_form').submit();
		});

	});

  </script>
  <script src="../../scripts/sort.js"></script>

  </body>
</html>


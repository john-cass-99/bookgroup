<?php
	include("./main.php");
	checkAdmin($application);
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="StyleSheet" href="./bookgroup2_admin.css" type="text/css" media="screen,print"/>
	<title>Edit or Add Suggested Book</title>
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
		include( './status.php');

		// print( "<p>GET: ");
		// print_r($_GET);
		// print("</p>\n");

		// print( "<p>POST: ");
		// print_r($_POST);
		// print("</p>\n");

		if (isset($_GET['idBook']))
			$idBook = $_GET['idBook'];
		else
			$idBook = 0;

		$dateRead = '';

		$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		$action = "bookdetails2_admin.php";
		if (isset($_POST['submitAction'])) {
			switch($_POST['submitAction']) {
				default:
					break;
				case 1: // Save
					if ($idBook == 0) { // Insert new book
						$sql  = "INSERT INTO books ( Title, Author, AmazonLink, SuggestedBy, ChosenBy, DateEntered, KindlePrice ) VALUES " .
							"( :Title, :Author, :AmazonLink, :SuggestedBy, :ChosenBy, :DateEntered, :KindlePrice );";
					}
					else { // Update existing book
						$sql = "UPDATE books SET Title=:Title, Author=:Author, AmazonLink=:AmazonLink, KindlePrice=:KindlePrice," .
							" SuggestedBy=:SuggestedBy, ChosenBy=:ChosenBy, Status=:Status WHERE ID = :ID;";
					}
					$prep = $db->prepare($sql);
					if ($idBook == 0)
						$prep->bindValue( ':DateEntered', date( 'Y-m-d' ) );
					else {
						$prep->bindValue( ':ID', $idBook );
						$prep->bindValue( ':Status', $_POST['lstChosenBy'] == 0 ? Status::SUGGESTED : Status::CHOSEN );
					}
					
					$prep->bindValue( ':Title', $_POST['txtTitle'] );
					$prep->bindValue( ':Author', $_POST['txtAuthor'] );
					$prep->bindValue( ':AmazonLink', $_POST['txtAmazonLink'] );
					$prep->bindValue( ':KindlePrice', $_POST['txtKindlePrice'] == '' ? null : $_POST['txtKindlePrice'] );
					$prep->bindValue( ':SuggestedBy', $_POST['lstSuggestedBy'] == 0 ? null : $_POST['lstSuggestedBy'] );
					$prep->bindValue( ':ChosenBy', $_POST['lstChosenBy'] == 0 ? null : $_POST['lstChosenBy'] );
					$prep->execute();
					$prep = null;
					echo "<script>window.close();</script>";
					break;
				case 2: //Check if Read
					if (strlen($_POST['txtTitle']) > 0) {
						$sql = "select C.MeetingDate from calendar C inner join books B on C.ptBooks=B.ID where B.Title=:Title";
						$prep = $db->prepare($sql);
						$prep->bindValue( ':Title', $_POST['txtTitle'] );
						$prep->execute();
						$dateRead = $prep->fetchColumn();
						if ( strlen($dateRead) > 9 )
							$dateRead = substr($dateRead, 0, 10);
					}
					break;
				case -1: // Delete
					$prepd = $db->prepare("UPDATE books SET Status=:Status WHERE ID=:ID");
					$prepd->bindValue( ':ID', $idBook );
					$prepd->bindValue( ':Status', Status::DELETED );
					$prepd->execute();
					$prepd = null;
					echo "<script>window.close();</script>";
					break;
			}
			$book_data = array('Title'=>$_POST['txtTitle'], 'Author'=>$_POST['txtAuthor'], 'AmazonLink'=>$_POST['txtAmazonLink'],
				 'KindlePrice'=>$_POST['txtKindlePrice'], 'SuggestedBy'=>$_POST['lstSuggestedBy'], 'ChosenBy'=>$_POST['lstChosenBy']);
		}
		if ($idBook == 0) {
			if (!isset($_POST['submitAction'])) {
				$book_data = array('Title'=>'', 'Author'=>'', 'AmazonLink'=>'',
					'KindlePrice'=>'', 'SuggestedBy'=>0, 'ChosenBy'=>0);
			}
		}
		else {
			$action = "bookdetails2_admin.php?idBook=$idBook";
			$st = $db->prepare("SELECT Title, Author, AmazonLink, KindlePrice, SuggestedBy, ChosenBy FROM books WHERE ID=:ID");
			$st->bindValue(':ID', $idBook);
			$st->execute();
			$book_data = $st->fetch(PDO::FETCH_ASSOC);
			if ($book_data['SuggestedBy'] == null) $book_data['SuggestedBy'] = 0;
			if ($book_data['ChosenBy'] == null) $book_data['ChosenBy'] = 0;
		}

		// print( "<p>book_data: ");
		// print_r($book_data);
		// print("</p>\n");

	}
	catch(PDOException $ex) {
		include '../catch.php';
	}
?>

	<header>
		<h2><?php print($idBook == 0 ? "Add&nbsp;New" : "Edit") ?> Suggested&nbsp;Book</h2>
	</header>
	<form id="bookDetails" name="bookDetails" action="<?php echo $action ?>" method="post">
	<div id="data-books" class="data-div">
		<div>
			<label for="txtTitle">Title:</label>
			<input type="text" class="changer wide-input" autocomplete="off" id="txtTitle" name="txtTitle" value="<?php print($book_data['Title']) ?>">
			<input type="button" id="checkIfRead" name="checkIfRead" value="Check if Read" style="float: right; margin-top: 3px" />
			<?php if ($dateRead != '') print("Book with this title was discussed $dateRead") ?>
		</div>
		<div>
			<label for="txtAuthor">Author:</label>
			<input type="text" class="changer" id="txtAuthor" name="txtAuthor" value="<?php print($book_data['Author']) ?>">
		</div>
		<div>
			<label for="txtAmazonLink">Amazon Link:</label><br />
			<span id="span_link">
				<input type="text" class="changer wide-input-with-button" id="txtAmazonLink" name="txtAmazonLink" value="<?php print($book_data['AmazonLink']) ?>">
				<input type="button" id="gotoAmazon" value="Go" />
			</span>
		</div>
		<div>
			<label for="txtKindlePrice">Kindle Price:</label>
			<input type="number" class="changer" step="0.01" max="99.99" placeholder="0.00" id="txtKindlePrice" name="txtKindlePrice" value="<?php print($book_data['KindlePrice']) ?>" style="width: 100px" />
		</div>
		<div>
			<label for="lstSuggestedBy">Suggested&nbsp;By:</label>
			<select class="changer narrow-input" id="lstSuggestedBy" name="lstSuggestedBy">
				<option value="0"></option>
<?php
				// Including deleted members in list as their suggested books may still be selected
				$stmt = $db->prepare("SELECT ID, KnownAs FROM members ORDER BY KnownAs;");
				$stmt->execute();
				$members = $stmt->fetchAll(PDO::FETCH_NUM);
				foreach( $members as $member) {
					$sel = $member[0] == $book_data['SuggestedBy'] ? ' selected' : '';
					print("\t\t\t\t<option value=\"$member[0]\"$sel>$member[1]</option>\n");
				}
?>
			</select>
		</div>
		<div>
		<label for="lstChosenBy">Chosen By:</label>
			<select class="changer narrow-input" id="lstChosenBy" name="lstChosenBy">
				<option value="0"></option>
<?php
				foreach( $members as $member) {
					$sel = $member[0] == $book_data['ChosenBy'] ? ' selected' : '';
					print("\t\t\t\t<option value=\"$member[0]\"$sel>$member[1]</option>\n");
				}
?>
			</select>
		</div>
		<div class="buttons">
			<input type="button" id="cmdDelete" value="Delete" <?php if ($idBook==0) print('disabled') ?>/>
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
	</div>
	<input type="hidden" id="submitAction" name="submitAction" value="0">
	</form>

	<script>
		var chg = 0;
		document.bookDetails.txtTitle.focus();

		window.onunload = refreshParent;
		function refreshParent() {
			window.opener.location.reload(true);
		}

		$(document).ready(function(){
			$(".changer").on("change", function(){
				chg = 1;
			})
			$("#cmdSave").on("click", function(){
				var m = $("#msg2")[0];
				if (document.getElementById("txtTitle").value.length == 0 ) {
					m.innerHTML = "Title is required!";
					return;
				}
				if (document.getElementById("txtAuthor").value.length == 0 ) {
					m.innerHTML = "Author is required!";
					return;
				}
				if (document.getElementById("txtKindlePrice").value > 99.99 ) {
					m.innerHTML = "Kindle Price must be less than £100!";
					return;
				}
				document.getElementById('submitAction').value = 1;
				document.bookDetails.submit();
			})
			$("#cmdDelete").on("click", function(){
				$("#msg2")[0].innerHTML = "Are you sure you want to delete this book?";
				document.getElementById('submitAction').value = -1;
				$(".confirm").show();
			})
			$("#cmdClose").on("click", function() {
				var m = $("#msg2")[0];
				if (chg != 0) {
					m.innerHTML = "Data has changed: confirm lose changes?";
					$(".confirm").show();
					return;
				}
				window.close();
			})
			$(".confirm").on("click", function() {
				if (this.value == "OK") {
					document.bookDetails.submit();
				}
				else {
					$("#msg2")[0].innerHTML = "";
					$(".confirm").hide();
				}
			})
			$("#book_details").on("click", function(){
				var idBook = document.getElementById("lstBook").value;
				window.open(`./bookdetails_admin.php?idBook=${idBook}`, 'Book Details', 'resizeable, left=200, top=200, height=450, width=800, location=0');
			})
			$("#gotoAmazon").on("click", function(){
				var link = document.getElementById("txtAmazonLink").value;
				if (link.length > 0)
					window.open(document.getElementById("txtAmazonLink").value);
			})
			$("#checkIfRead").on("click", function(){
				document.getElementById('submitAction').value = 2;
				document.bookDetails.submit();
			})
		});
	</script>
</body>
</html>
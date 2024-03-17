<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Password Reset</title>
	<link href="login-style.css" rel="stylesheet" type="text/css">
	<script src="https://kit.fontawesome.com/f89b070702.js" crossorigin="anonymous"></script>
</head>
<body>
<?php
	include('validate.php');
	include('variables.php');
	$disabled = '';
	$hidden = '';
	$login = '';
	if (isset($_POST['username'])) {
		$login = $_POST['username'];
		// This is a postback to check and set the new password
		$message = '<p style="color: red">Error - message not set!</p>';

		// Check if both the password and confirm password fields match
		if ($_POST['cpassword'] != $_POST['password']) {
			$message = '<p style="color: red">Passwords do not match!</p>';
		}
		else {
			if(!validate_password($_POST['password'])) {
				$message = '<p style="color: red">Password must be at least 8 characters and contain at least one lower case, upper case and number.</p>';
			}
			else {
				include("db_config.php");
				$stmt = $db->prepare('UPDATE members set password=:pwd WHERE Login=:login;');
				$stmt->bindValue(':login', $_POST['username']);
				$stmt->bindValue(':pwd', password_hash($_POST['password'], PASSWORD_DEFAULT));
				$stmt->execute();
				$message = '<p>Password successfully updated</p>';
				$disabled = ' disabled';
				$hidden= ' style="display: none"';

				// Delete the key
				$stmt = $db->prepare('DELETE FROM forget_password WHERE temp_key=:temp_key');
				$stmt->bindValue(':temp_key', $_GET['key']);
				$stmt->execute();
			}
		}
	}
	else {
		$message = '<p>This link will work only once for a limited time period.</p>';
		include("db_config.php");

		// Delete any expired records from forget_password
		$db->query("DELETE FROM forget_password WHERE created < NOW() - INTERVAL 4 HOUR;");

		if(isset($_GET['key']) && isset($_GET['email'])) {
			$stmt = $db->prepare("SELECT COUNT(id) FROM forget_password WHERE email=:email and temp_key=:temp_key");
			$stmt->bindParam(':email', $_GET['email']);
			$stmt->bindParam(':temp_key', $_GET['key']);
			$stmt->execute();
			if ($stmt->fetchColumn() != 1) {
			$message = '<p style="color: red">This key is invalid or has already been used. Please verify and try again.</p>';
			$disabled = ' disabled';
			}
			else {
				// Need login to register
				$stmt = $db->query("SELECT login from members WHERE Email='" . $_GET['email'] . '\'');
				$login = $stmt->fetchColumn();
			}
		}
		else{
		header('location:index.html');
		}
	}
	?>
	<div class="login">
		<h1><?php print($bookgroup_name) ?></h1>
		<form method="post" <?php echo $hidden ?>>
			<div>
				<p>Please enter your new password:</p>
			</div>
			<label for="password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" id="password" name="password" placeholder="Password" required autofocus autocomplete="one-time-code" <?php echo $disabled ?>>
			<label for="cpassword">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" id="cpassword" name="cpassword" placeholder="Re-type Password" required autocomplete="one-time-code" <?php echo $disabled ?>>
			<input type="submit" value="Save Password" <?php echo $disabled ?>>
			<input type="hidden" id="username" name="username" value=<?php echo '"' . $login . '"' ?>>
		</form>
		<div>
			<?php echo $message . "\n" ?>
		</div>
		<div>
			<a href="<?php print($loginForm) ?>">Back to Login</a>
		</div>
	</div>
</body>
</html>

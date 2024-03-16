<?php
	include('./main.php');
	checkLoggedIn($application, './edit_details.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Change Password</title>
	<link href="login-style.css" rel="stylesheet" type="text/css">
	<script src="https://kit.fontawesome.com/f89b070702.js" crossorigin="anonymous"></script>
</head>
<body>
<?php
	include('validate.php');
	$disabled = '';
	$hidden = '';
	$login = $_SESSION['login']['bookgroup']['user'];

	// print('<p>Login = ');
	// //print_r($login);
	// print($login);
	// print("<br>\n");
	// print('POST=[');
	// print_r($_POST);
	// print("]</p>\n");
	// print("<p>current isset = " . isset($_POST['current_password']) . "</p>\n");
	// print("<p>new     isset = " . isset($_POST['new_password']) . "</p>\n");
	// print("<p>c_new   isset = " . isset($_POST['c_new_password']) . "</p>\n");

	$message = 'Please enter current and new passwords:';
	if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['c_new_password'])) {
		// This is a postback to check and set the new password
		$error_style = '<p style="color: red">';
		$message = $error_style . 'Error - message not set!</p>';

		// Check current password!
		include("db_config.php");
		$stmt = $db->prepare('SELECT password FROM members WHERE Login=:login;');
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$password = $stmt->fetchColumn();

		if (!password_verify($_POST['current_password'], $password)) {
			$message = $error_style . 'Current password is incorrect!</p>'; 
		}
		else {
			// Check if both the password and confirm password fields match
			if ($_POST['new_password'] != $_POST['c_new_password']) {
				$message = $error_style . 'Passwords do not match!</p>';
			}
			else {
				if(!validate_password($_POST['new_password'])) {
					$message = $error_style . 'Password must be at least 8 characters and contain at least one lower case, upper case and number.</p>';
				}
				else {
					$stmt = $db->prepare('UPDATE members set password=:pwd WHERE Login=:login;');
					$stmt->bindValue(':login', $login);
					$stmt->bindValue(':pwd', password_hash($_POST['new_password'], PASSWORD_DEFAULT));
					$stmt->execute();
					$message = '<p>Password successfully updated</p>';
					$disabled = ' disabled';
					$hidden= ' style="display: none"';
				}
			}
		}
	}
	?>
	<div class="login">
		<h1><?php print($bookgroup_name) ?></h1>
		<div>
			<?php echo $message . "\n" ?>
		</div>
		<form method="post" <?php echo $hidden ?>>
			<label for="current_password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" id="current_password" name="current_password" placeholder="Current Password" required autofocus autocomplete="one-time-code" <?php echo $disabled ?>>
			<label for="new_password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" id="new_password" name="new_password" placeholder="New Password" required autofocus autocomplete="one-time-code" <?php echo $disabled ?>>
			<label for="c_new_password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" id="c_new_password" name="c_new_password" placeholder="Re-type New Password" required autocomplete="one-time-code" <?php echo $disabled ?>>
			<input type="submit" value="Save Password" <?php echo $disabled ?>>
		</form>
		<div>
			<a href="<?php print($loginForm) ?>">Back to Login</a>
		</div>
	</div>
</body>
</html>

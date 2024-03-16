<?php
	include('./main.php');
	checkLoggedIn($application, 'profile.php');

// output message (errors, etc)
$msg = '';
$db = new PDO("mysql:host=$host;dbname=$login_db;charset=utf8", $dbuser, $dbpwd);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $db->prepare('SELECT password FROM accounts WHERE application=:app  AND id = :id;');
$stmt->bindValue(':app', $application);
$stmt->bindValue(':id', $_SESSION['login'][$application]['id']);
$stmt->execute();
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Handle edit profile post data
if (isset($_POST['username'], $_POST['password'], $_POST['cpassword'], $_POST['email'])) {
	// Make sure the submitted registration values are not empty.
	if (empty($_POST['username']) || empty($_POST['email'])) {
		$msg = 'The input fields must not be empty!';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$msg = 'Please provide a valid email address!';
	} else if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
	    $msg = 'Username must contain only letters and numbers!';
	} else if (!empty($_POST['password']) && (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)) {
		$msg = 'Password must be between 5 and 20 characters long!';
	} else if ($_POST['cpassword'] != $_POST['password']) {
		$msg = 'Passwords do not match!';
	}
	if (empty($msg)) {
		// Check if new username or email already exists in database
		$stmt = $pdo->prepare('SELECT COUNT(*) FROM accounts WHERE (username = ? OR email = ?) AND username != ? AND email != ?');
		$stmt->execute([ $_POST['username'], $_POST['email'], $_SESSION['name'], $account['email'] ]);
		if ($result = $stmt->fetchColumn()) {
			$msg = 'Account already exists with that username and/or email!';
		} else {
			// no errors occured, update the account...
			$stmt = $pdo->prepare('UPDATE accounts SET username = ?, password = ?, email = ? WHERE id = ?');
			// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
			$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
			$stmt->execute([$_POST['username'], $password, $_POST['email'], $_SESSION['id']]);
			// Update the session variables
			$_SESSION['name'] = $_POST['username'];
			// profile updated redirect the user back to the profile page and not the edit profile page
			header('Location: profile.php');
			exit;
		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Profile Page</title>
		<link href="login-style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="home.php"><i class="fas fa-home"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<?php if (!isset($_GET['action'])): ?>
		<div class="content profile">
			<h2>Profile Page</h2>
			<div class="block">
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?php $_SESSION['login'][$application]['user']?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?php $account['password']?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?php $account['email']?></td>
					</tr>
				</table>
				<a class="profile-btn" href="profile.php?action=edit">Edit Details</a>
			</div>
		</div>
		<?php elseif ($_GET['action'] == 'edit'): ?>
		<div class="content profile">
			<h2>Edit Profile Page</h2>
			<div class="block">
				<form action="profile.php?action=edit" method="post">
					<label for="username">Username</label>
					<input type="text" value="<?=$_SESSION['name']?>" name="username" id="username" placeholder="Username">
					<label for="password">Password</label>
					<input type="password" name="password" id="password" placeholder="Password">
					<label for="cpassword">Confirm Password</label>
					<input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password">
					<br>
					<input class="profile-btn" type="submit" value="Save">
					<p><?=$msg?></p>
				</form>
			</div>
		</div>
		<?php endif; ?>
	</body>
</html>

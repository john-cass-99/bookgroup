<?php

include( "./main.php" );
// checkLoggedIn($application, './register.html');

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['cpassword'])) {
	// Could not get the data that should have been sent.
	exit('Please complete the registration form!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password'])) {
	// One or more values are empty.
	exit('Please complete the registration form');
}
// Username must contain only characters and numbers.
if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
    exit('Username is not valid!');
}
// Password must be between 5 and 20 characters long.
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
	exit('Password must be between 5 and 20 characters long!');
}
// Check if both the password and confirm password fields match
if ($_POST['cpassword'] != $_POST['password']) {
	exit('Passwords do not match!');
}

try {
	$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $db->prepare('SELECT count(id) FROM members WHERE Login = :user');
	$stmt->bindValue(':user', $_POST['username']);
    $stmt->execute();
    $userexists = $stmt->fetchColumn();
    
	// Store the result so we can check if the account exists in the database.
	$user = $_POST['username'];
	define('SUCCESS','You have successfully');
	if ($userexists > 0) {
		$stmt = $db->prepare('UPDATE members set password=:pwd WHERE Login=:login;');
		$msg = SUCCESS . " updated the password for \"$user\"";
	} 
	else {
		$stmt = $db->prepare('INSERT INTO members (Login, password) VALUES (:login, :pwd);');
		$msg = SUCCESS . " added user \"$user\"";
	}
	$stmt->bindValue(':login', $_POST['username']);
	$stmt->bindValue(':pwd', password_hash($_POST['password'], PASSWORD_DEFAULT));
	$stmt->execute();
	exit($msg);

}
catch(PDOException $ex) {
    include './catch.php';
	exit("Failed to connect to MySQL: \n" . $ex->getMessage());
}
?>
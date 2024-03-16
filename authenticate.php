<?php
session_start();
include('./variables.php');
include( "../inf1.php" );

if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}

try {
	$db = new PDO("mysql:host=$host;dbname=$bookgroup_db;charset=utf8", $dbuser, $dbpwd);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if ($stmt = $db->prepare('SELECT ID, password, Admin FROM members WHERE Login = :user')) {
		$stmt->bindValue(':user', $_POST['username']);
		$stmt->execute();
		$account = $stmt->fetch(PDO::FETCH_ASSOC);

	}
}
catch(PDOException $ex) {
    if (isset($showPDOErrors) && $showPDOErrors) {
        exit("PDO Error: " . $ex->getMessage());
    }
    else {
        error_log($ex->getMessage());
		exit('Failed');
    }
}

if ($account) {
	// Use this code for debugging
	// $x = "<p>id=" . $account['ID'] . ", Admin = " . $account['Admin'];
	// if (isset($_SESSION['startform']))
	// 	$x .= ", Start Form=" .  $_SESSION['startForm'];
	// $x .= "</p>\n";
	// exit($x);

	if (password_verify($_POST['password'], $account['password'])) {
		session_regenerate_id();
		$_SESSION['login'][$application] = array('id'=>$account['ID'], 'user'=>$_POST['username'], 'admin'=>$account['Admin']);
		try {
			$log = $db->prepare("UPDATE members SET LastLoggedIn=NOW() WHERE Login=:user");
			$log->bindValue(':user', $_POST['username']);
			$log->execute();

			// 28/11/2023 Added logins table to record successful logins
			// 11/02/2024 UNLESS it's me - don't record my logins as they are numerous!
			if ($account['ID'] != 2) {
				$log = $db->prepare("INSERT INTO logins (ptUser, LoggedIn) VALUES (:id, NOW())");
				$log->bindValue(':id', $account['ID']);
				$log->execute();
			}

			// 11/02/2024 Limited saved logins to MAX_SAVED_LOGINS
			define( "MAX_SAVED_LOGINS", 25, false);
			$stmt = $db->query("SELECT MAX(ID) FROM logins");
			$IDmax = $stmt->fetchColumn();
			$stmt = $db->query("SELECT MIN(ID) FROM logins");
			$IDmin = $stmt->fetchColumn();
			if ($IDmax - $IDmin >= MAX_SAVED_LOGINS) {
				$log = $db->prepare("DELETE FROM logins WHERE ID<:limit");
				$log->bindValue(':limit', $IDmax - MAX_SAVED_LOGINS + 1 );
				$log->execute();
			}
		}
		catch(PDOException $ex) {
			if (isset($showPDOErrors) && $showPDOErrors) {
				exit("PDO Error: " . $ex->getMessage());
			}
			else {
				error_log($ex->getMessage());
				exit('Failed');
			}
		}
		if (isset($_SESSION['startForm']) && $_SESSION['startForm'] != '') {
			// 	// print("<p>startForm=");
			// 	// print_r($_SESSION['startForm']);
			// 	// print("</p>\n");
			header('Location: ' . $_SESSION['startForm']);
			unset($_SESSION['startForm']);
			exit;
		}
		// else {
		// 	// print("startForm not set");
		header('Location: '  . $initialForm);
		exit;
		// }
	}
    else {
		// print("<p>Failed to verify password</p>\n");
		header('Location: '  . $loginForm);
	}
}
else {
	// print("<p>account entry not found</p>\n");
	header('Location: ' . $loginForm);
}
?>

<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
unset($_SESSION['startForm']);
include('./variables.php');
include( "../inf1.php" );

function checkLoggedIn($application, $startForm) {
	global $loginForm;
	if (!isset($_SESSION['login'][$application])) {
		$_SESSION['startForm'] = $startForm;
		header('Location: ' . $loginForm);
		exit;
	}
}
?>

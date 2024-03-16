<?php
session_start();
unset($_SESSION['startForm']);
include('../variables.php');
include( "../../inf1.php" );

function checkAdmin($application) {
	if (!isset($_SESSION['login'][$application]['admin']) || $_SESSION['login'][$application]['admin'] < 1) {
		header("Location: access_denied.html");
		exit();
	}
}

?>

<?php
session_start();
include('./variables.php');
if(isset($_SESSION['login'][$application]))
    unset($_SESSION['login'][$application]);
// Redirect to the login page:
header("Location: $loginForm");
?>
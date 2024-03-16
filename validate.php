<?php

/*
	John Cass 02/03/2024
	Function for validating a password
	Current specification: > 8 chars; at least one of lower case, upper case, digit
	Returns TRUE if all conditions are met
*/

define ('MIN_PWD_LENGTH', 8);

function validate_password($password) {
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$number    = preg_match('@[0-9]@', $password);

	return $uppercase && $lowercase && $number && strlen($password) >= MIN_PWD_LENGTH;

}

?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<title>Login</title>
	<link href="login-style.css" rel="stylesheet" type="text/css">
	<script src="https://kit.fontawesome.com/f89b070702.js" crossorigin="anonymous"></script>
</head>
<body>
	<?php
		include("./variables.php");
	?>
	<div class="login">
		<table id="logo">
			<tr>
				<td><h1><?php print($bookgroup_name) ?><br />Login</h1></td>
			</tr>
		</table>
		<form action="./authenticate.php" method="post">
			<label for="username">
				<i class="fas fa-user"></i>
			</label>
			<input type="text" name="username" placeholder="Username" id="username" required autofocus>
			<label for="password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" name="password" placeholder="Password" id="password" required>
			<div class="moveup">
				<a class="forgot" href="forgot_password.php">(Forgot password?)</a>
			</div>
			<div>
				<input type="submit" value="Login">
			</div>
		</form>
	</div>
</body>
</html>
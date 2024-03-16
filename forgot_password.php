<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="login-style.css" rel="stylesheet" type="text/css">
	<script src="https://kit.fontawesome.com/f89b070702.js" crossorigin="anonymous"></script>
	<title>Forgot Password</title>
</head>
<body>
<?php
/*
	  Author  : Suresh Pokharel
	  Email   : suresh.wrc@gmail.com
	  GitHub  : github.com/suresh021
	  URL     : psuresh.com.np
	  Modified Feb 2024 by JMC to use PHPMailer
*/ 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include ('db_config.php');
// Delete any expired records from forget_password
$db->query("DELETE FROM forget_password WHERE created < NOW() - INTERVAL 4 HOUR;");

require $autoload;
$mail = new PHPMailer(TRUE);
$message="Please enter your email to recover your password";
$ok = true;
$hidden = '';

session_start();
if($_SERVER["REQUEST_METHOD"] == "POST"){
	try{
		$stmt = $db->prepare("SELECT Email  from members where Email=:Email");
		$stmt->bindValue(':Email', $_POST['email']);
		$stmt->execute();
		$email_reg = $stmt->fetchColumn();

		//$email_reg=mysqli_real_escape_string($dbconfig,$_POST['email']);
		//$details=mysqli_query($dbconfig,"SELECT fullname,email FROM user WHERE email='$email_reg'");
		if ($email_reg == $_POST['email']) { //if the given email is in database, ie. registered
			$message="Please check your email inbox or spam folder and follow the steps";
			$hidden = ' style="display: none"';
			//generating the random key
			$key=md5(time()+123456789% rand(4000, 55000000));
			//insert this temporary key into database
			//$sql_insert=mysqli_query($dbconfig,"INSERT INTO forget_password(email,temp_key) VALUES('$email_reg','$key')");
			$db->query("INSERT INTO forget_password(email,temp_key) VALUES('$email_reg','$key')");
			//sending email about update
			$mail->setFrom('john@ridgehill-dev.uk', 'John Cass');
			$mail->addAddress($email_reg);
			//$to      = $email_reg;
			$mail->Subject = 'Changing Password';
			//$subject = 'Changing password DEMO';
			$mail->Body = "Please copy the link and paste in your browser address bar". "\r\n"."$host_for_link/bookgroup/password_reset.php?key=".$key."&email=".$email_reg;
			//$headers = 'From:Gentle Heart Foundation' . "\r\n";
			$mail->addReplyTo('jmcass99@gmail.com', 'John Cass');
			//mail($to, $subject, $msg, $headers);
			
			// SMTP parameters.
			$mail->isSMTP(); // Tells PHPMailer to use SMTP.
			$mail->Host = $mail_server; // SMTP server address.
			$mail->SMTPAuth = TRUE; // Use SMTP authentication.
			$mail->SMTPSecure = 'tls'; // Set the encryption system.
			$mail->Username = $mail_user; // SMTP authentication username.
			$mail->Password = $mail_pwd; // SMTP authentication password.
			$mail->Port = 587; // Set the SMTP port.
			$mail->send(); // Finally send the mail.
		}
		else{
			$message='Sorry! No account associated with this email';
			$ok = false;
		}
	}
	catch(Exception $ex) {
		$message = $ex->errorMessage();
		$ok = false;
	}
}
?>
	<div class="login">
		<h1><?php print($bookgroup_name) ?></h1>
		<?php
			$c = $ok ? '' : ' style="color: red"';
			echo "<p$c>$message</p>";
		?>
		<form method="post" <?php echo $hidden ?>>
			<label for="email">
				<i class="fas fa-envelope"></i>	
			</label>
			<input id="email" name='email' value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" placeholder="Email" autofocus required autocomplete="one-time-code">
			<div class="higher">			
				<input type="submit" name="submit" value="Send Email">
			</div>
		</form>
		<div class="higher">
				<a href="index.html">Back to Login</a>
			</div>
	</div>
  </body>
</html>

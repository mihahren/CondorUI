<?php
include_once 'functions.php';

session_start();	//start new or continue current session

if (isset($_POST['username']))	//check for username credentials
{
	$username = $_POST['username'];
} 
else
{
	$username = $_SESSION['username'];
}

if (isset($_POST['password']))	//check for password credentials
{
	$password = $_POST['password'];
} 
else
{
	$password = $_SESSION['password'];
}

if(!isset($username))
{
?>
<!DOCTYPE html PUBLIC "-//W3C/DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title> Please Log In for Access </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
	<h1> Login Required </h1>
	<p>You must log in to access this area of the site. If you are not a registered user, <a href="user_management\signup.php">click here</a> to sign up for instant access!</p>
	<p><form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
		Username: <input type="text" name="username" size="8" /><br />
		Password: <input type="password" name="password" SIZE="8" /><br />
		<input type="submit" value="Log in" />
	</form></p>
</body>
</html>
<?php
	exit;
}

$_SESSION['username'] = $username;
$_SESSION['password'] = $password;

dbConnect('condor_users');

$query = "SELECT * FROM users WHERE username = '$username' AND password = PASSWORD('$password')";

$result = mysql_query($query);

if (!$result)
{
	error('Error 5.\\nA database error occurred while checking your login details.\\nIf this error persists, please contact miha.hren88@gmail.com.');
}

if (mysql_num_rows($result) == 0)
{
	unset($_SESSION['username']);
	unset($_SESSION['password']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title> Access Denied </title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
	<h1> Access Denied </h1>
	<p>Your username or password is incorrect, or you are not a registered user on this site. To try logging in again, click <a href="<?php echo $_SERVER['PHP_SELF']?>">here</a>. To register for instant access, click <a href="user_management\signup.php">here</a>.</p>
</body>
</html>
<?php
	exit;
}
?>
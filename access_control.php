<?php
include_once "functions.php";

session_start();	//zacne nov ali nadaljuje obstojec session

if (isset($_POST['username']))	//preveri vnesen username
{
	$_SESSION['username'] = $_POST['username'];
	unset($_POST['username']);
} 

if (isset($_POST['password']))	//preveri vnesen password
{
	$_SESSION['password'] = $_POST['password'];
	unset($_POST['password']);
} 

if (!isset($_SESSION['username']))
{
	$_SESSION['access'] = "login";
}
else
{
	$database_link = dbConnect('condor_users');

	$query = "SELECT * FROM users WHERE username = '".$_SESSION['username']."' AND password = PASSWORD('".$_SESSION['password']."')";
	$result = mysql_query($query, $database_link);

	if (mysql_num_rows($result) == 0)
	{
		$_SESSION['access'] = "no_access";
	}
	else
	{
		$_SESSION['access'] = "access";
		$_SESSION['login_id'] = mysql_result($result,0,'userid');
	}


	if ($_POST['logout'] == "logout")
	{
		unset($_SESSION['login_id']);
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		
		$_SESSION['access'] = "login";
	}
	
	mysql_close($database_link);
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{ 
header('Location: '.$_SERVER['PHP_SELF']);
}
else
{
	if ($_SESSION['access'] == "no_access" || $_SESSION['access'] == "login")
	{
		unset($_SESSION['login_id']);
		unset($_SESSION['username']);
		unset($_SESSION['password']);
	}
}
?>
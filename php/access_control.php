<?php
include_once 'functions.php';

session_start();	//zacne nov ali nadaljuje obstojec session

$access = "";

if (isset($_POST['username']))	//preveri vnesen username
{
	$_SESSION['username'] = $_POST['username'];
} 

if (isset($_POST['password']))	//preveri vnesen password
{
	$_SESSION['password'] = $_POST['password'];
} 

if (!isset($_SESSION['username']))
{
	$access = "login";
}
else
{
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];
	
	$database_link = dbConnect('condor_users');

	$query = "SELECT * FROM users WHERE username = '$username' AND password = PASSWORD('$password')";
	$result = mysql_query($query, $database_link);

	if (!$result)
	{
		error('Error 5.\\nA database error occurred while checking your login details.\\nIf this error persists, please contact miha.hren88@gmail.com.');
	}

	if (mysql_num_rows($result) == 0)
	{
		$access = "no_access";
	}
	else
	{
		$access = "access";
		$login_id = mysql_result($result,0,'userid');
	}


	if ($_POST["logout"] == "logout")
	{
		unset($_SESSION['username']);
		unset($_SESSION['password']);
		unset($_POST["logout"]);
		
		$access = "login";
	}
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{ 
header('Location: '.$_SERVER['PHP_SELF']); 
}
else
{
	if ($access == "no_access" || $access == "login")
	{
		unset($_SESSION['username']);
		unset($_SESSION['password']);
	}
}
?>
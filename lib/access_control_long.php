<?php
include_once "functions.php";
include_once "classes.php";

$userManager = new UserManager();	//zacne nov ali nadaljuje obstojec session

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
	$userManager->loginUser($_SESSION['username'], $_SESSION['password']);

	if ($_POST['logout'] == "logout")
	{
		$userManager->logoutUser();
	}
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

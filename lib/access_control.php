<?php
include_once "functions.php";
include_once "classes.php";

$userManager = new UserManager();	//zacne nov ali nadaljuje obstojec session

if($_SERVER['REQUEST_METHOD'] == "POST")
{ 
	header('Location: '.$_SERVER['PHP_SELF']);

	if (isset($_POST['username']) || isset($_POST['password']))	//preveri vnesen username in password
	{
		$userManager->loginUser($_POST['username'], $_POST['password']);
	}

	if ($_POST['logout'] == "logout")
	{
		$userManager->logoutUser();
	}
}

?>

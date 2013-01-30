<?php
include_once "functions.php";
include_once "classes.php";

$userManager = new UserManager();	//zacne nov ali nadaljuje obstojec session

if($_SERVER['REQUEST_METHOD'] == "POST")
{ 
	header('Location: '.$_SERVER['PHP_SELF']);

	if (isset($_POST['username']) || isset($_POST['password']))	//preveri vnesen username in password
	{
		if($userManager->loginUser($_POST['username'], $_POST['password']))
			$_SESSION['custom_error']['login'] = "Dobrodošli v uporabniškem vmesniku za HTCondor";
		else
			$_SESSION['custom_error']['login'] = "Napačni podatki!";
	}

	if (isset($_POST['logout']))
	{
		$userManager->logoutUser();
	}
}
?>

<?php
include_once "functions.php";
include_once "access_control.php";

//obdelava post requesta
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po menujih
	$_SESSION['menu_1'] = $_POST['menu_1'];
	$_SESSION['menu_2'] = $_POST['menu_2'];
}

//switch stavek za kontrolo dostopa
switch ($_SESSION['access'])
{
case "login":

	echo "Prosim vpisite svoje uporabnisko ime in geslo";
	break;
	
case "no_access":

	echo "Prosim vpisite svoje uporabnisko ime in geslo";
	$_SESSION['custom_error'] = "Prislo je do napake!";
	break;
	
case "access":
	
	//switch stavek za kontrolo glavnega menuja
	switch ($_SESSION['menu_1'])
	{
	case "basic":
	
		include "basic.php";
		break;
		
	case "advanced":
		
		include "advanced.php";
		break;
	}
	break;
}

//obdelava errorjev
if (!empty($_SESSION['custom_error']))
{
	echo "<div id='custom_error'>";
	if (is_array($_SESSION['custom_error']))
	{
		for ($i=0; $i<(count($_SESSION['custom_error'])); $i++)
		{
			echo $_SESSION['custom_error'][$i]."<br />";
		}
	}
	else
	{
		echo $_SESSION['custom_error'];
	}
	echo "</div>";
}

//reset pomoznih globalnih spremenljivk
if($_SERVER['REQUEST_METHOD'] != "POST")
{
	unset($_SESSION['custom_error']);
}
?>
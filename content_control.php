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



?>
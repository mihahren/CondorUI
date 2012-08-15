<?php
include_once "functions.php";
include_once "access_control.php";

//obdelava errorjev
if (!empty($_SESSION['custom_error']))
{
	$uniqueArray = array_unique(flattenArray($_SESSION['custom_error']));

	echo "<div id='custom_error' title='".count($uniqueArray)."'>";
		foreach ($uniqueArray as $value)
		{
			echo $value."<br />";
		}
	echo "</div>";
}

//reset pomoznih globalnih spremenljivk
if($_SERVER['REQUEST_METHOD'] != "POST")
{
	unset($_SESSION['custom_error']);
}
?>
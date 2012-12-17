<?php
include_once "functions.php";
include_once "access_control.php";

//obdelava errorjev
if (!empty($_SESSION['custom_error']))
{
	$uniqueArray = array_unique(flattenArray($_SESSION['custom_error']));

	echo "<div id='custom_error' title='".count($uniqueArray)."'><div id='main_alert' class='alert alert-block'>
		<button type='button' id='main_alert_button' class='close' data-dismiss='alert'>&times;</button>";
		foreach ($uniqueArray as $value)
		{
			echo $value."<br />";
		}
	echo "</div></div>";
}

//reset pomoznih globalnih spremenljivk
if($_SERVER['REQUEST_METHOD'] != "POST")
{
	unset($_SESSION['custom_error']);
}
?>

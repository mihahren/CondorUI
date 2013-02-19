<?php
include_once "functions.php";
include_once "access_control.php";

//preveri, kaksno stanje mora imet alert toggle gumb
if (($_SESSION['errorstatus'] == 1) && (!empty($_SESSION['custom_error'])))
{
	//obdelava errorjev
	$uniqueArray = array_unique(flattenArray($_SESSION['custom_error']));

	echo "<div id='custom_error_mobile' title='".count($uniqueArray)."'><div id='mobile_alert' class='alert alert-block hidden-desktop' style='margin-top:20px'>
		<button type='button' id='alert_button_mobile' class='close' data-dismiss='alert'>&times;</button>";
		foreach ($uniqueArray as $value)
		{
			echo $value."<br />";
		}
	echo "</div></div>";
	
	echo "<div id='custom_error_desktop' title='".count($uniqueArray)."'>
		<div id='desktop_alert' class='custom-popover bottom visible-desktop'>
			<div class='arrow'></div>
			<h3 class='custom-popover-title'>Pozor<button type='button' id='alert_button_desktop' class='close'>&times;</button></h3>
			<div class='custom-popover-content'>
				<p>";
					foreach ($uniqueArray as $value)
					{
						echo $value."<br />";
					}
				echo "</p>
			</div>
		</div>
	 </div>";
}

//reset pomoznih globalnih spremenljivk
if($_SERVER['REQUEST_METHOD'] != "POST")
{
	unset($_SESSION['custom_error']);
}
?>

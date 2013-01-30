<?php
include_once "functions.php";
include_once "access_control.php";
include_once "classes.php";

if($_SERVER['REQUEST_METHOD'] != "POST")
{
	//hranjenje statsev
	$_SESSION['stats']['userid'] = $_SESSION['login_id'];
	$_SESSION['stats']['browser'] = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['stats']['ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['stats']['date_visited'] = time();
	$_SESSION['stats']['page'] = $_SERVER['PHP_SELF'];
	$_SESSION['stats']['from_page'] = $_SERVER['HTTP_REFERER'];
	$_SESSION['stats']['submit_cluster'] = 0;
	$_SESSION['stats']['submit_proc'] = 0;

	//stats od submitanih datotek
	$flattenedArray = flattenArray($_SESSION['custom_error']['submits']);

	foreach ($flattenedArray as $value)
	{
		if (strpos($value, "job(s) submitted to cluster") !== false)
		{
			$tempArray = explode(" ", $value);
		
			$_SESSION['stats']['submit_cluster']++;
			$_SESSION['stats']['submit_proc'] += (int)$tempArray[0];
		}
	}

	//obdelava statsev
	$statsTracker = new StatsTracker();
	$statsTracker->storeStats($_SESSION['stats']);
	unset($_SESSION['stats']);
}
?>

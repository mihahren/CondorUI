<div id="footer">
<?php
include_once "error_function.php";
include_once "stats_tracking.php";

if(!($_SERVER['REQUEST_METHOD'] == "POST") && !(($_SESSION['access'] == "access") || ($_SESSION['access'] == "admin")))
{
	$_SESSION['access'] = "login";
}
?>
</div>

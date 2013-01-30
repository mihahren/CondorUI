<div id="footer">
<?php
include "stats_tracking.php";
include "error_tracking.php";

if(!($_SERVER['REQUEST_METHOD'] == "POST") && !(($_SESSION['access'] == "access") || ($_SESSION['access'] == "admin")))
{
	$_SESSION['access'] = "login";
}
?>
</div>

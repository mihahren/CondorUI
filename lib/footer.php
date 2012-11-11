<div id="footer">
<?php include "error_function.php";

if(!($_SERVER['REQUEST_METHOD'] == "POST") && !(($_SESSION['access'] == "access") || ($_SESSION['access'] == "admin")))
{
	$_SESSION['access'] = "login";
}
?>
</div>

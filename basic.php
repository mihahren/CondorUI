<?php
include_once "functions.php";
include_once "access_control.php";

if ($_SESSION['access'] == "access")
{
?>

<!-- Tukaj pride content -->

<?php
}
else
{
	echo "Prislo je do napake!";
}
?>
<?php
include_once "functions.php";
include_once "access_control.php";
include_once "classes.php";

//hranjenje statsev
$_SESSION['stats']['userid'] = $_SESSION['login_id'];
$_SESSION['stats']['browser'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['stats']['ip'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['stats']['date_visited'] = time();
$_SESSION['stats']['page'] = $_SERVER['PHP_SELF'];
$_SESSION['stats']['from_page'] = $_SERVER['HTTP_REFERER'];

//obdelava statsev

$statsTracker = new StatsTracker();
$statsTracker->storeStats($_SESSION['stats']);
unset($_SESSION['stats']);
?>

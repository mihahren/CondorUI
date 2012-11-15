<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

switch ($_SESSION['access'])
{
case "access":

	header('Location: index.php');
	break;	

case "no_access":

	$_SESSION['custom_error']['index_login'] = "Napacni podatki ali pa je vas trial cas potekel!";
	break;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Signup</title>
		<link rel="stylesheet" type="text/css" href="css/global_css.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="js/global_jquery.js"></script>
	</head>
	<body>
		<!-- header, ki vsebuje glavo z login, logout menujem ter odsek za prikazovanje sporocil -->
		<?php include_once "lib/header.php";?>

		<!-- content panel, ki prikazuje glavni del aplikacije -->
		<div id="content_panel">
			<?php include_once "lib/register.php";?>
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

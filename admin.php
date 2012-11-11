<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Admin</title>
		<link rel="stylesheet" type="text/css" href="css/global_css.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="js/global_jquery.js"></script>
		<style type="text/css">#admin_button {border: 1px solid red;}</style>
	</head>
	<body>
		<!-- header, ki vsebuje glavo z login, logout menujem ter odsek za prikazovanje sporocil -->
		<?php include_once "lib/header.php";?>
		
		<!-- content panel, ki prikazuje glavni del aplikacije -->
		<div id="content_panel">
<?php	
		//switch stavek za kontrolo dostopa
		switch ($_SESSION['access'])
		{
		case "login":

			echo "Prosim vpisite svoje uporabnisko ime in geslo!";
			break;
			
		case "no_access":

			echo "Prosim vpisite svoje uporabnisko ime in geslo!";
			$_SESSION['custom_error']['admin_login'] = "Napacni podatki ali pa je vas trial cas potekel!";
			break;

		case "access":

			echo "Prosim vpisite uporabnisko ime in geslo z administratorskimi pravicami!";
			$_SESSION['custom_error']['admin_noaccess'] = "Napacni podatki! Racun nima dostopa do te strani!";
			break;
			
		case "admin":
			
			echo "Si v admin predelu. Under construction!<br/>
				  <a href='signup.php'>dodaj uporabnika</a> (napredno)";
			break;
		}
?>		
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>
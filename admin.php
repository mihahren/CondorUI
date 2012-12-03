<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";
include_once "lib/stats_variables.php";

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
		case "no_access":

			echo "<div class='admin_text'>Prosim vpisite svoje uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['admin_login'] = "Napacni podatki!";
			break;

		case "time_out":

			echo "<div class='index_text'>Prosim vpisite drugo uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['admin_login'] = "Vas trial cas je potekel!";
			break;

		case "access":

			echo "<div class='admin_text'>Prosim vpisite uporabnisko ime in geslo z administratorskimi pravicami!</div>";
			$_SESSION['custom_error']['admin_login'] = "Racun nima dostopa do te strani!";
			break;
			
		case "admin":
?>			
			<?php include "lib/register.php";?>

			<div id='admin_24h_graph' class="graph_box">
<?php
				drawLineGraph($array_last_24h,"file_last_24h.png","Stevilo uporabnikov zadnjih 24 ur",25,24.4,29,5,2,2);
				echo "<img src='images/file_last_24h.png'/>";
?>
			</div>

			<?php include "lib/user_editor.php";?>

			<div id='admin_365d_graph' class="graph_box">
<?php
				drawBarGraph($array_last_year,"file_last_year.png","Stevilo uporabnikov v zadnjem letu",25,25,18,26,6,2);
				echo "<img src='images/file_last_year.png'/>";
?>
			</div>

			<div id='admin_popular_graph' class="graph_box">
<?php
				drawPieChart($array_page,"file_page.png","Najbolj obiskane strani",450,250,2);
				echo "<img src='images/file_page.png'/>";
?>
			</div>
<?php
			break;

		default:

			echo "<div class='basic_text'>Prosim vpisite svoje uporabnisko ime in geslo!</div>";
			break;
		}
?>		
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

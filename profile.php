<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/stats_variables.php";

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Profile</title>
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
<?php	
		//switch stavek za kontrolo dostopa
		switch ($_SESSION['access'])
		{
		case "no_access":

			echo "<div class='profile_text'>Prosim vpisite svoje uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['profile_login'] = "Napacni podatki!";
			break;

		case "time_out":

			echo "<div class='index_text'>Prosim vpisite drugo uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['profile_login'] = "Vas trial cas je potekel!";
			break;
			
		case "access":
		case "admin":

			include "lib/user_editor.php";
		
?>
			<div id='profile_popular_graph' class="graph_box">
<?php
				drawPieChart($array_page_user,"file_page.png","Najbolj obiskane strani uporabnika",400,220,2);
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

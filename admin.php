<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CondorUI - Admin</title>
		<!-- jQuery -->
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<!-- Bootstrap -->
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.css" rel="stylesheet">
		<script type="text/javascript" src="js/bootstrap.js"></script>
		<!-- Custom scripts and css -->
		<script type="text/javascript" src="js/global_jquery.js"></script>
		<link href="css/global_css.css" rel="stylesheet">
	</head>
	<body>
		<!-- header, ki vsebuje glavo z login, logout menujem ter odsek za prikazovanje sporocil -->
		<?php include_once "lib/header.php";?>
		
		<!-- content panel, ki prikazuje glavni del aplikacije -->
		<div id="content_panel" class="container">
		
<?php	
		//switch stavek za kontrolo dostopa
		switch ($_SESSION['access'])
		{
		case "no_access":

			echo "<div class='hero-unit'>Prosim vpisite svoje uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['admin_login'] = "Napačni podatki!";
			break;

		case "time_out":

			echo "<div class='hero-unit'>Prosim vpisite drugo uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['admin_login'] = "Vas trial cas je potekel!";
			break;

		case "access":

			echo "<div class='hero-unit'>Prosim vpisite uporabnisko ime in geslo z administratorskimi pravicami!</div>";
			$_SESSION['custom_error']['admin_login'] = "Račun nima dostopa do te strani!";
			break;
			
		case "admin":
?>			
			<div class="row-fluid">
				<div class="span12">
					<h1>Admin</h1>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="span4">
					<?php include "lib/register.php";?>
				</div>
				<div class="span8">
					<div class="generic_box" style="background-color:#f5f5f5">
						<?php include_once "lib/charts/chart_24h.php"; ?>
						<img src="lib/charts/chart_24h.png" style="max-width:100%;" />
					</div>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="span4" >
					<?php include "lib/user_editor.php";?>
				</div>
				<div class="span8">
					<div class="generic_box" style="background-color:#f5f5f5">
						<?php include "lib/charts/chart_365d.php"; ?>
						<img src="lib/charts/chart_365d.png" style="max-width:100%;" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="span4"></div>
				<div class="span8">
					<div class="generic_box" style="background-color:#f5f5f5">
						<?php include "lib/charts/chart_pages.php"; ?>
						<img src="lib/charts/chart_pages.png" style="max-width:100%;" />
					</div>
				</div>
			</div>
<?php
			break;

		default:

			echo "<div class='hero-unit'>Prosim vpišite svoje uporabniško ime in geslo!</div>";
			break;
		}
?>		
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

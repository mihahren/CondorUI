<?php
error_reporting(0);
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/stats_variables.php";

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CondorUI - Profil</title>
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
		<div class="container">
<?php	
		//switch stavek za kontrolo dostopa
		switch ($_SESSION['access'])
		{
		case "no_access":

			echo "<div class='hero-unit'>Prosim vpišite svoje uporabniško ime in geslo!</div>";
			$_SESSION['custom_error']['profile_login'] = "Napačni podatki!";
			break;

		case "time_out":

			echo "<div class='hero-unit'>Prosim vpišite drugo uporabniško ime in geslo!</div>";
			$_SESSION['custom_error']['profile_login'] = "Vaš trial čas je potekel!";
			break;
			
		case "access":
		case "admin":
?>
			<div class="row-fluid">
				<div class="span12">
					<h1>Profil</h1>
				</div>
			</div>
			
			<div class="row">
				<div class="span4">
					<?php include "lib/user_editor.php"; ?>
				</div>
				<div class="span6">
					<div class="generic_box" style="background-color:#f5f5f5">
						<?php include "lib/charts/chart_pages_user.php"; ?>
						<img src="lib/charts/chart_pages_user.png" style="max-width:100%;" />
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="span4">
					<?php include "lib/user_options.php"; ?>
				</div>
				<div class="span6">
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

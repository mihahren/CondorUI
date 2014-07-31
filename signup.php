<?php
error_reporting(0);
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CondorUI - Registracija</title>
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
		<?php include "lib/header.php";?>

		<!-- content panel, ki prikazuje glavni del aplikacije -->
		<div class="container">
		
			<div class="row-fluid">
				<div class="span12">
					<h1>Registracija</h1>
				</div>
			</div>
		
			<div class="row">
				<div class="span4">
					<?php include "lib/register.php";?>
				</div>
				<div class="span8">
				
				</div>
			</div>
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include "lib/footer.php";?>
	</body>
</html>

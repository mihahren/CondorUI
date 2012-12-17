<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Index</title>
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
				$_SESSION['custom_error']['basic_login'] = "Napacni podatki!";
				break;

			case "time_out":

				echo "<div class='hero-unit'>Prosim vpisite drugo uporabnisko ime in geslo!</div>";
				$_SESSION['custom_error']['basic_login'] = "Vas trial cas je potekel!";
				break;
				
			case "access":
			case "admin":
?>
				<div class="btn-group">
					<button id="basic_file_button" class="btn btn-primary">Upload Files</button>
					<button class="btn disabled">Avtomatsko uploada, ustvari privzeto submit datoteko ter doda v condor queue.</button>
				</div>
				
				<form method="post" id="basic_file_form" enctype="multipart/form-data" style="visibility:hidden;">
					<input type="hidden" name="create_submit_file" value="true" />
					<input type="file" name="file[]" id="basic_file_upload" multiple/><br />
				</form>
				<div id="output_box"></div>
<?php
				break;

			default:

				echo "<div class='hero-unit'>Prosim vpisite svoje uporabnisko ime in geslo!</div>";
				break;
			}
?>		
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

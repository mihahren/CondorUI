<?php
include_once "functions.php";
include_once "access_control.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>index</title>
		<link rel="stylesheet" type="text/css" href="global_css.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="global_jquery.js"></script>
		<style type="text/css">#advanced_button {border: 1px solid red;}</style>
	</head>
	<body>
		<!-- header, ki vsebuje glavo z login, logout menujem ter odsek za prikazovanje sporocil -->
		<?php include_once "header.php";?>
		
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
			$_SESSION['custom_error'][6] = "Napacni podatki!";
			break;
			
		case "access":
?>
			<div id="input_box">
				<div class="button_wrapper" id="queue_button">
					<span class="button_text">Condor Queue</span>
				</div>
				<div class="button_wrapper" id="status_button">
					<span class="button_text">Condor Status</span>
				</div>
				<div class="button_wrapper" id="submit_button">
					<span class="button_text">File Management</span>
				</div>
			</div>
			<div id="output_box">
				Izberi eno izmed moznosti na levi.
			</div>		
<?php
		}
?>		
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "footer.php";?>
	</body>
</html>
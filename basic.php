<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>basic</title>
		<link rel="stylesheet" type="text/css" href="css/global_css.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="js/global_jquery.js"></script>
		<style type="text/css">#basic_button {border: 1px solid red;}</style>
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
				<div id="basic_input_wrapper">
					<div class="button_wrapper" id="basic_file_button">
						<span class="button_text">Upload Files</span>
					</div>
				</div>
				
				<div id="basic_output_wrapper">
					Avtomatsko uploada, ustvari privzeto submit datoteko ter doda v condor queue.
				</div>
				
				<form method="post" id="basic_file_form" enctype="multipart/form-data" style="visibility:hidden;">
					<input type="hidden" name="create_submit_file" value="true" />
					<input type="file" name="file[]" id="basic_file_upload" multiple/><br />
				</form>
<?php
			}
?>		
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "footer.php";?>
	</body>
</html>

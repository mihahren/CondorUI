<?php
error_reporting(0);
include_once "lib/functions.php";
include_once "lib/access_control.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CondorUI - Nadzorna plošča</title>
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

				echo "<div class='hero-unit'>Prosim vpišite svoje uporabniško ime in geslo!</div>";
				$_SESSION['custom_error']['basic_login'] = "Napačni podatki!";
				break;

			case "time_out":

				echo "<div class='hero-unit'>Prosim vpišite drugo uporabniško ime in geslo!</div>";
				$_SESSION['custom_error']['basic_login'] = "Vaš trial čas je potekel!";
				break;
				
			case "access":
			case "admin":
?>
				<div class="row-fluid">
					<div class="span12">
						<h1>Nadzorna Plošča</h1>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<ul class="nav nav-tabs nav-stacked">
							<li class="ctr_pnl_menu_btn" ><a id="condor_manager_button">Upravljanje s HTCondor</a></li>
							<li class="ctr_pnl_menu_btn" ><a id="file_manager_button">Upravljanje z datotekami</a></li>
						</ul>
						<ul class="nav nav-tabs nav-stacked">
							<li class="ctr_pnl_menu_btn" ><a id="zip_upload_button">Prenos ZIP datoteke</a></li>
							<li class="ctr_pnl_menu_btn" ><a id="ida_curves_button">IDA krivulje</a></li>
							<li class="ctr_pnl_menu_btn" ><a id="ida_curves_zip_button">IDA krivulje - ZIP</a></li>
						</ul>
					</div>
					<div class="span9">
						<div id="output_box_control_panel">
<?php
							switch ($_SESSION['cp_menu'])
							{
							case "file":
							
								echo "<script type='text/javascript'>submitAjax('ajax/control_panel_ajax_file_manager.php', '#output_box_control_panel');</script>";
								break;	
							
							case "zip":
							
								echo "<script type='text/javascript'>submitAjax('ajax/control_panel_ajax_zip.php', '#output_box_control_panel');</script>";
								break;	
							
							case "ida":
							
								echo "<script type='text/javascript'>submitAjax('ajax/control_panel_ajax_ida.php', '#output_box_control_panel');</script>";
								break;
								
							case "ida-zip":
							
								echo "<script type='text/javascript'>submitAjax('ajax/control_panel_ajax_ida_zip.php', '#output_box_control_panel');</script>";
								break;
							
							case "condor":
							default:
								
								echo "<script type='text/javascript'>submitAjax('ajax/control_panel_ajax_condor_manager.php', '#output_box_control_panel');</script>";
								break;							
							}
?>						
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

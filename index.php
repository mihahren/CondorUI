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
			$_SESSION['custom_error']['index_login'] = "Napacni podatki!";
			break;

		case "time_out":

			echo "<div class='hero-unit'>Prosim vpisite drugo uporabnisko ime in geslo!</div>";
			$_SESSION['custom_error']['index_login'] = "Vas trial cas je potekel!";
			break;
			
		case "access":
		case "admin":
?>
			<div class="row-fluid">
				<div class="span8">
					<div class="row-fluid">
						<div class="span12">
							<div class="btn-group visible-desktop" style="margin-bottom:20px">
								<button id="home_file_button" class="btn btn-primary">Upload Files</button>
								<button class="btn disabled">Ustvari privzet submit file</button>
							</div>
							<div class="btn-group hidden-desktop" style="margin-bottom:20px">
								<button id="home_file_button" class="btn btn-primary">Upload Files</button>
								<button class="btn disabled">Ustvari submit</button>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="tabbable" >
								<ul class="nav nav-tabs" style="margin-bottom:-1px">
<?php
									if ($_SESSION['menu'] == "computer_status")
									{
										$lastSubmitsActive = "";
										$computerStatusActive = "class='active'";
									}
									else
									{
										$lastSubmitsActive = "class='active'";
										$computerStatusActive = "";
									}
								
									echo "<li id='button_last_submits' ".$lastSubmitsActive."><a href='#tab_last_submits' data-toggle='tab'>Zadnji submiti</a></li>";
									echo "<li id='button_computer_status' ".$computerStatusActive."><a href='#tab_computer_status' data-toggle='tab'>Status racunalnikov</a></li>";
?>
								</ul>
								<div class="tab-content" style="padding:10px;border:1px solid #dddddd">
									<div class="tab-pane active" id="tab_last_submits">
										<?php include "ajax/home_ajax_content.php";?>
									</div>
									<div class="tab-pane" id="tab_computer_status">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="span4">
					<div class="row-fluid">
						<div class="span12">
							<div class="generic_box" style="background-color:#f5f5f5">
								<?php include_once "lib/charts/chart_loggedin.php"; ?>
								<img src="lib/charts/chart_loggedin.png" style="max-width:100%;" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<form method="post" id="home_form" enctype="multipart/form-data" style="visibility:hidden">
				<input type="hidden" name="create_submit_file" value="true" />
				<input type="file" name="file[]" id="home_file_upload" multiple/><br />
			</form>
<?php
			break;

		default:

			echo "<div class='hero-unit'>Prosim vpisite svoje uporabnisko ime in geslo!</div>";
			break;
		}
?>		
		</div>
		<div id="output_box"></div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
		<div class="popover">
			<div class="arrow"></div>
			<h3 class="popover-title">Popover bottom</h3>
			<div class="popover-content">
				<p>Sed posuere consectetur est at lobortis. Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis vestibulum.</p>
			</div>
		</div>
	</body>
</html>

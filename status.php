<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>CondorUI - Stanje</title>
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
			<div class="row-fluid">
				<div class="span12">
					<h1>Stanje</h1>
				</div>
			</div>				
			<div class="row-fluid">
				<div class="span4">
					<h3>Delovanje naprav</h3>
					<div id="output_box_condor_computers" class="generic_box" style="min-height:368px">
						<?php echo "<script type='text/javascript'>submitAjax('ajax/status_ajax_computers.php', '#output_box_condor_computers');</script>"; ?>
					</div>
				</div>
				<div class="span8">
					<h3>Stanje naprav</h3>
					<div id="output_box_condor_status" class="generic_box" style="min-height:368px">
						<?php echo "<script type='text/javascript'>submitAjax('ajax/status_ajax_status.php', '#output_box_condor_status');</script>"; ?>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span4">
					<h3>Aktivnost zadnjih 7 dni</h3>
					<div id="output_box_condor_graph" class="generic_box" style="background-color:#f5f5f5">
						<?php include_once "lib/charts/chart_condor_7_days.php"; ?>
						<img src="lib/charts/chart_condor_7_days.png" style="max-width:100%;" />
					</div>					
				</div>
				<div class="span4">
					<h3>Skupno vnosov</h3>
					<div id="output_box_condor_q" class="generic_box" style="min-height:224px">
						<?php echo "<script type='text/javascript'>submitAjax('ajax/status_ajax_q.php', '#output_box_condor_q');</script>"; ?>
					</div>
				</div>
				<div class="span4">
					<h3>Skupno stanje naprav</h3>
					<div id="output_box_condor_status_total" class="generic_box" style="min-height:224px">
						<?php echo "<script type='text/javascript'>submitAjax('ajax/status_ajax_status_total.php', '#output_box_condor_status_total');</script>"; ?>
					</div>				
				</div>
			</div>
		</div>
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

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
			<div class="row-fluid">
				<div class="span12">
					<h1>Povezave</h1>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span3 margin-correction">
					<a href="http://www3.fgg.uni-lj.si"><img src="img/ul_fgg_logo.jpg"></img></a>
				</div>
				<div class="span3 margin-correction">
					<a href="http://www.uni-lj.si/o_univerzi_v_ljubljani.aspx"><img src="img/ul_logo.jpg"></img></a>
				</div>
				<div class="span3 margin-correction">
					<a href="http://php.net/"><img src="img/php_logo.jpg"></img></a>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span3 margin-correction">
					<a href="http://research.cs.wisc.edu/htcondor/"><img src="img/htcondor_logo.jpg"></img></a>
				</div>
				<div class="span3 margin-correction">
					<a href="http://www.mysql.com/"><img src="img/mysql_logo.jpg"></img></a>
				</div>
				<div class="span3 margin-correction">
					<a href="http://www.pchart.net"><img src="img/pchart_logo.jpg"></img></a>
				</div>
			</div>	
			<div class="row-fluid">
				<div class="span3 margin-correction">
					<a href="http://jquery.com/"><img src="img/jquery_logo.jpg"></img></a>
				</div>
			</div>
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

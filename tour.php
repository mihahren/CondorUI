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
					<h1>Predstavitev</h1>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<img class="img-polaroid" src="">
					</img>
				</div>
				<div class="span6">
					<p>Testno besedilo. Zanima me, kako zgleda v vec vrsticah. Bo sedaj ze dovolj besed?</p>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<img class="img-polaroid" src="">
					</img>
				</div>
				<div class="span6">
					<p>Testno besedilo. Zanima me, kako zgleda v vec vrsticah. Bo sedaj ze dovolj besed?</p>
				</div>
			</div>	
			<div class="row-fluid">
				<div class="span6">
					<img class="img-polaroid" src="">
					</img>
				</div>
				<div class="span6">
					<p>Testno besedilo. Zanima me, kako zgleda v vec vrsticah. Bo sedaj ze dovolj besed?</p>
				</div>
			</div>	
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

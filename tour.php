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
				<div class="span5">
					<a href="status.php"><img class="img-polaroid" src="img/promo_2.jpg" /></a>
				</div>
				<div class="span6">
					<p align="justify"><b>Status</b> je obsežna stran, katere primarni namen je pregled trenutnega stanja HTCondor sistema. Razdeljena je na pet področij, ki so logično porazdeljeni glede na posredovaneo informacijo. To so delovanje naprav, aktivnost v zadnjih sedmih dneh, stanje naprav, skupno število trenutnih HTCondor vnosov in stanje naprav na ravni arhitekture strojne opreme.</p>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span5">
					<a href="control_panel.php" id="tour_fm"><img class="img-polaroid" src="img/promo_fm.jpg" /></a>
				</div>
				<div class="span6">
					<p align="justify">Vmesnik za <b>upravljanje z datotekami</b> omogoča kar nekaj funkcionalnosti. Primarna je sigurno snemanje izhodnih datotek, ki jih proizvede HTCondor ob uspešnem zaključku računanja. Poleg tega omogoča tudi nalaganje novih datotek, katere lahko pošljemo v izvajanje, če vsebujejo pravilne končnice. Podprte so .submit, .sub in .condor. Poleg predložnih datotek imajo posebno funkcionalnost omogočeno tudi datoteke z zip končnico, katere lahko s pritiskom na ustrezni gumb razpakiramo. Zaradi potrebe po boljši organizaciji je omogočeno tudi ustvarjanje novih map. Uporabnik lahko tako enostavno loči projekte med sabo. Nenazadnje pa je omogočeno tudi brisanje datotek in map, da uporabniku ni potrebno skrbeti za odvečne datoteke, ki jih več ne potrebuje.</p>
				</div>
			</div>	
			<div class="row-fluid">
				<div class="span5">
					<a href="control_panel.php" id="tour_ida"><img class="img-polaroid" src="img/promo_ida.jpg" /></a>
				</div>
				<div class="span6">
					<p align="justify">Za <b>računanje IDA krivulj</b> je bilo potrebno ustvariti nove funkcije za samodejno ustvarjanje predložne datoteke. Ta se ustvari na podlagi parametrov, kot so izbira akcelelograma in končnega časa računanja, katere poda uporabnik. Poleg tega je omogočeno podajanje poljubnega števila primerov. Vse to je izvedeno s pomočjo ajax principa in JavaScript kode. S pritiskom na gumb + ali – se dodaja oziroma odvzema število vnosov v predložno datoteko. Izbira parametrov se vnese ročno preko besedilnega polja, le akcelelogram izberemo iz vnaprej pripravljenega seznama, ki se ustvari na podlagi akcelelogramov, ki so na voljo.</p>
				</div>
			</div>	
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

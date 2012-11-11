//////////////////////////
/// STRUKTURA PROGRAMA ///
//////////////////////////

access_control.php // kontrola dostopa
functions.php // globalne funkcije za dostop do baze podatkov, condor ukazi in ostalo
index.php // prva stran
header.php // vsebuje navigacijske gumbe in login box
footer.php // small print in error funkcija
error_function.php // pomozna error funkcija
basic.php // glavna stran za basic menu, vsebuje header, footer, gumbe
advanced.php // glavna stran za advanced menu, vsebuje header, footer, gumbe
basic_ajax_content.php // vsebina, ki se izpisuje znotraj output <div> v basic.php, po principu ajax
advanced_ajax_content.php // vsebina, ki se izpisuje znotraj output <div> v advanced.php, po principu ajax
file_manager.php // upravlja ustvarjanje, nalaganje, brisanje datotek in map
global_css.css // globalni CSS file
global_jquery.js // globalni javascript (jQuery) file
classes.php // file z vsemi razredi za OOP

razno/signup.php // obrazec za vpisovanje v condor_users bazo, tabela users

/////////////////////
/// BAZA PODATKOV ///
/////////////////////

Baza podatkov naj bo ustvarjena z naslednjimi oznakami:

CREATE DATABASE condor_users;

CREATE TABLE users (
  	userid int unsigned not null auto_increment primary key,
	username varchar(100) not null,
	password varchar(42) not null,
	email varchar(100) not null,
	isadmin int(1),
	registertime varchar(100) not null,
	activetime varchar(100) not null
);

username, password in host za bazo podatkov se poda. ko se ustvari nov UserManager objekt. Lahko se tudi postavi privzete vrednosti v classes.php

Za vnos uporabnikov v condor_users bazo je priporocljivo uporabiti signup.php, ki se nahaja v mapi razno. Vnosi lahko potekajo tudi rocno, vendar je potrebno paziti, saj so passwordi hashani s PASSWORD() funkcijo vgrajeno v MySQL.

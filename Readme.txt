//////////////////////////
/// STRUKTURA PROGRAMA ///
//////////////////////////

access_control.php // kontrola dostopa
functions.php // globalne funkcije za dostop do baze podatkov, condor ukazi in ostalo
index.php // prva stran
header.php // vsebuje navigacijske gumbe in login box
footer.php // small print in error funkcija
error_function.php // pomozna error funkcija
admin.php // stran za urejanje uporabnikov, gledanje naprednih statistik
profile.php // profilna stran uporabnika, za spreminjanje imena, gesla
file_manager.php // upravlja ustvarjanje, nalaganje, brisanje datotek in map
global_css.css // globalni CSS file
global_jquery.js // globalni javascript (jQuery) file
classes.php // file z vsemi razredi za OOP
tour.php // stran za pregled funkcij
links.php // povezave
status.php // stran za pregled stanja condor sistema
control_panel.php // stran za upravljanje s condorjem in datotekami
ajax datoteke // sodijo k ustreznim osnovnim datotekam

razno/signup.php // obrazec za vpisovanje v condor_users bazo, tabela users

/////////////////////
/// BAZA PODATKOV ///
/////////////////////

Baza podatkov naj bo ustvarjena z naslednjimi oznakami:

CREATE DATABASE condor_users;

CREATE TABLE users (
	userid int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	username varchar(100) NOT NULL,
	password varchar(42) NOT NULL,
	email varchar(100) NOT NULL,
	isadmin int(1),
	registertime varchar(100) NOT NULL,
	activetime varchar(100) NOT NULL
);

CREATE TABLE stats (
	statid int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	userid int UNSIGNED DEFAULT '0',
	browser varchar(255) NOT NULL DEFAULT '',
	ip varchar(15) NOT NULL DEFAULT '',
	date_visited int unsigned NOT NULL DEFAULT '0',
	page varchar(100) NOT NULL DEFAULT '',
	from_page varchar(150) NOT NULL DEFAULT '',
	submit_cluster int unsigned NOT NULL DEFAULT '0',
	submit_proc int unsigned NOT NULL DEFAULT '0',
	FOREIGN KEY (userid) REFERENCES users(userid)
);

username, password in host za bazo podatkov se poda. ko se ustvari nov UserManager objekt. Lahko se tudi postavi privzete vrednosti v classes.php

Za vnos uporabnikov v condor_users bazo je priporocljivo uporabiti signup.php, ki se nahaja v mapi razno. Vnosi lahko potekajo tudi rocno, vendar je potrebno paziti, saj so passwordi hashani s PASSWORD() funkcijo vgrajeno v MySQL.

/////////////////
/// BOOTSTRAP ///
/////////////////

@navbarBackground: #a10010
@navbarBackgroundHighlight: #b80012
@navbarText: @white
@navbarLinkColor: @white
@navbarLinkColorActive: @white
@red: #a10010

//////////////
/// CONDOR ///
//////////////

Condor je potrebno konfigurirati s sledecim ukazom, da je omogoceno posiljanje preko ostalih userjev:
QUEUE_ALL_USERS_TRUSTED = TRUE

uporabnik, ki je onznacen pod +Owner atributom, mora imeti pravice za pisanje v log, error in output datoteko.

///////////////////
/// PERMISSIONS ///
///////////////////

http://www.elated.com/articles/understanding-permissions/

7 : read, write and execute
6 : read and write
5 : read and execute
4 : read only
3 : write and execute
2 : write only
1 : execute only
0 : no permissions



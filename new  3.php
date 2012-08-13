<?php
include_once "functions.php";
$out = "";

condor_remove("-all", $out);
	
foreach($out as $value)
	echo $value."<br />";
?>
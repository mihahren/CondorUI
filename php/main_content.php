<?php
include_once "functions.php";
include_once "access_control.php";

//glavni switch stavek za izbiro menuja
$out = Array("");
switch($_GET["podatek"])
{
case queue:
	condor_q($out);
	print_cmd($out);
	break;

case status:
	condor_status($out);
	print_cmd($out);
	break;

case submit:
	include "file_manager.php";
	break;
}
?>
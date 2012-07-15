<?php
include_once "functions.php";

//switch stavek
$in = "";
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
	$in = '..\upload\poskus.submit';
	condor_submit($in, $out);
	print_cmd($out);
	break;
}
?>
<?php
include_once "functions.php";

//switch stavek
$in = "";
$out = Array("");
switch($_GET["podatek"])
{
case queue:
	condor_q($out);
	break;
case status:
	condor_status($out);
	break;
case submit:
	$in = 'upload\poskus.submit';
	condor_submit($in, $out);
	break;
case remove:
	$in = '-h';
	condor_remove($in, $out);
	break;
}

for ($i=0; $i<=(count($out)-1); $i++)
{
	echo "<pre>$out[$i]<pre>";
}
?>
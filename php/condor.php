<?php
//definirane funkcije in ostali ukazi
function condor_status(&$output)
{
	exec('condor_status 2>&1', $output);
}

function condor_q(&$output)
{
	exec('condor_q 2>&1', $output);
}

function condor_submit($input, &$output)
{
	exec('condor_submit -n miha '.$input.' 2>&1', $output);
}

function condor_remove($input, &$output)
{
	exec('condor_rm '.$input.' 2>&1', $output);
}

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
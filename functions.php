<?php
//vstavi database host, username, password
$dbhost='127.0.0.1';
$dbuser='root';
$dbpass='mihius88';

function dbConnect($db="")
{
	global $dbhost, $dbuser, $dbpass;
	
	$db_link = mysql_connect($dbhost,$dbuser,$dbpass);

	if (!$db_link)
	{
		echo 'Database connection failed.';
		exit;
	}
	
	$db_handle = mysql_select_db($db, $db_link);
	
	if(!$db_handle)
	{
		echo 'Error selecting database.';
		exit;
	}
	
	return $db_link;
}

//definirane condor funkcije
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

//sprinta izpise cmd konzole vrstico za vrstico
function print_cmd($input)
{
	if (is_array($input))
	{
		for ($i=0; $i<(count($input)); $i++)
		{
			echo "<pre>$input[$i]</pre>";
		}
	}
	else
	{
		echo $input;
	}
}
?>

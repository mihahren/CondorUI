<?php
//Error funkcija
function error($msg)
{
?>

<html>
<head>
	<title>Error</title>
	<script language="JavaScript">
	<!--
	alert("<?php echo $msg?>");
	history.back();
	//-->
	</script>
</head>
<body>
</body>
</html>

<?php
exit;
}

//set database host, username, password
$dbhost='127.0.0.1';
$dbuser='root';
$dbpass='mihius88';

function dbConnect($db="")
{
	global $dbhost, $dbuser, $dbpass;
	
	$db_link = mysql_connect($dbhost,$dbuser,$dbpass); //create a mysql server link

	if (!$db_link) //check if the database connection failed
	{
		echo 'Database connection failed.';
		exit;
	}
	
	$db_handle = mysql_select_db($db, $db_link); //select the provided database through the link
	
	if(!$db_handle) //check if selection failed
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
?>

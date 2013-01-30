<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po straneh
	if (isset($_POST['page_number_computers']))
		$_SESSION['current_page']['page_number_computers'] = $_POST['page_number_computers'];
}

//array za izpis racunalnikov
condor_generic('condor_status -xml -attributes Name',$codnorOutput);
$stringOutput = convertString($codnorOutput);

$xml = simplexml_load_string($stringOutput);

$condorArray = array();
$iter = 0;

foreach ($xml->c as $c)
{
	foreach ($c->a as $a)
	{
		$line = (string)($a->Children());
		$condorArray[$iter][(string)$a['n']] = substr(strstr(trim($line), '@'), 1);
	}

	$iter++;
}

//izpis imen iz txt datoteke v array
$computerArray = array();
$readFile = fopen("../lib/computers.txt","r");
$iter = 0;

while(!feof($readFile))
{
	$line = fgets($readFile);

	if (!empty($line))
	{
		$computerArray[$iter]['Name'] = trim($line);
		$iter++;
	}
}

fclose($readFile);

//preveri vse racunalnike iz seznama in jih oznaci po potrebi
for ($i=0;$i<count($computerArray);$i++)
{
	$computerArray[$i]['Status'] = false;

	for ($j=0;$j<count($condorArray);$j++)
	{
		if ($computerArray[$i]['Name'] == $condorArray[$j]['Name'])
		{
			$computerArray[$i]['Status'] = true;
			break;
		}
	}
}

//izpise seznam
$condorStatus = new CondorManager($computerArray, 15, $_SESSION['current_page']['page_number_computers']);
$condorStatus->drawCondorComputersTable();
$condorStatus->drawPageNavigation("ajax/status_ajax_computers.php","#output_box_condor_computers","page_number_computers");

echo "<div id='computers_selector'></div>";
include "../lib/error_tracking.php";
?>

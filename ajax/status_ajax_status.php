<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{		
	//spremenljivke za navigiranje po straneh
	if (isset($_POST['page_number_status']))
		$_SESSION['current_page']['page_number_status'] = $_POST['page_number_status'];
}

// prva tabela s statusom posameznih racunalnikov
condor_generic('condor_status -xml -attributes Name,OpSys,Arch,State,Activity,LoadAvg,Memory',$codnorOutput);
$stringOutput = convertString($codnorOutput);

$xml = simplexml_load_string($stringOutput);

$condorStatusArray = array();
$iter = 0;

foreach ($xml->c as $c)
{
	foreach ($c->a as $a)
	{
		$condorStatusArray[$iter][(string)$a['n']] = (string)($a->Children());
	}

	$iter++;
}

$condorStatus = new CondorManager($condorStatusArray, 15, $_SESSION['current_page']['page_number_status']);
$condorStatus->drawCondorStatusTable();
$condorStatus->drawPageNavigation("ajax/status_ajax_status.php","#output_box_condor_status","page_number_status");

echo "<div id='status_selector'></div>";
include "../lib/error_tracking.php";
?>

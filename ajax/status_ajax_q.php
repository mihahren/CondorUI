<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po straneh
	if (isset($_POST['page_number_status_q']))
		$_SESSION['current_page']['page_number_status_q'] = $_POST['page_number_status_q'];
}

//array za pridobitev podatkov
condor_generic('condor_q -xml -attributes ClusterID,Owner',$codnorOutput);
$stringOutput = convertString($codnorOutput);

$xml = simplexml_load_string($stringOutput);

$condorArray = array();
$iter = 0;

foreach ($xml->c as $c)
{
	foreach ($c->a as $a)
	{
		$condorArray[$iter][(string)$a['n']] = (string)($a->Children());
	}

	$iter++;
}

//for zanka, ki grupira vse elemente z isto arhitekturo/operacijskim istemom
if (!empty($condorArray))
{
	$tempArray[0]['Owner'] = $condorArray[0]['Owner'];
	$tempArray[0]['Total'] = 0;
	$tempArray[0]['Total_cluster'] = 1;
	$prevID = $condorArray[0]['ClusterID'];
	$tempNames[0] = $tempArray[0]['Owner'];
	$iter = 0;
}

for ($i=0;$i<count($condorArray);$i++)
{
	foreach ($tempNames as $key => $value)
	{
		if (($condorArray[$i]['Owner']) == $value)
		{
			$iter = $key;
			break;
		}
		else
		{
			$iter = count($tempNames);
		}
	}

	$tempArray[$iter]['Total']++;

	if ($prevID != $condorArray[$i]['ClusterID'])
	{
		$tempArray[$iter]['Owner'] = $condorArray[$i]['Owner'];
		$tempNames[$iter] = $tempArray[$iter]['Owner'];

		$tempArray[$iter]['Total_cluster']++;
	
		$prevID = $condorArray[$i]['ClusterID'];
	}
}


//izpise seznam
$condorStatusQ = new CondorManager($tempArray, 15, $_SESSION['current_page']['page_number_status_q']);
$condorStatusQ->drawCondorStatusQTable();
$condorStatusQ->drawPageNavigation("ajax/status_ajax_q.php","#output_box_condor_q","page_number_status_q");

echo "<div id='computers_selector'></div>";
include "../lib/error_tracking.php";
?>

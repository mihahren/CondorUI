<?php
error_reporting(0);
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{		
	//spremenljivke za navigiranje po straneh za total
	if (isset($_POST['page_number_total']))
		$_SESSION['current_page']['page_number_total'] = $_POST['page_number_total'];
}

//array s skupnim stevilom racunalnikov
condor_generic('condor_status -xml -attributes OpSys,Arch,State',$codnorOutput);
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

//for zanka, ki grupira vse elemente z isto arhitekturo/operacijskim sistemom
$tempArch[0] = $condorStatusArray[0]['Arch']."/".$condorStatusArray[0]['OpSys'];
$iter = 0;

for ($i=0;$i<count($condorStatusArray);$i++)
{
	foreach ($tempArch as $key => $value)
	{
		if (($condorStatusArray[$i]['Arch']."/".$condorStatusArray[$i]['OpSys']) == $value)
		{
			$iter = $key;
			break;
		}
		else
		{
			$iter = count($tempArch);
		}
	}

	$tempArray[$iter]['Arch'] = $condorStatusArray[$i]['Arch']."/".$condorStatusArray[$i]['OpSys'];
	$tempArch[$iter] = $tempArray[$iter]['Arch'];

	$tempArray[$iter]['Total']++;

	if ($condorStatusArray[$i]['State'] == "Claimed")
		$tempArray[$iter]['Claimed']++;
	elseif ($condorStatusArray[$i]['State'] == "Unclaimed")
		$tempArray[$iter]['Unclaimed']++;
}

$condorStatusTotal = new CondorManager($tempArray, 5, $_SESSION['current_page']['page_number_total']);
$condorStatusTotal->drawCondorStatusTotalTable();

echo "<div style='height:30px;width:30px'></div>";
echo "<div style='position:absolute;left:15px;bottom:10px'>";
	$condorStatusTotal->drawPageNavigation("ajax/status_ajax_status_total.php","#output_box_condor_status_total","page_number_total");
echo "</div>";

echo "<div id='status_total_selector'></div>";
include "../lib/error_tracking.php";
?>

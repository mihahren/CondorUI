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
condor_generic('condor_q -xml -attributes ClusterID,Webuser',$codnorOutput);
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

//for zanka, ki grupira vse vnose istega uporabnika
if (!empty($condorArray))
{
	$tempArray[0]['Webuser'] = $condorArray[0]['Webuser'];
	$tempArray[0]['Total'] = 0;
	$tempArray[0]['Total_cluster'] = 1;
	$prevID = $condorArray[0]['ClusterID'];
	$tempNames[0] = $tempArray[0]['Webuser'];
	$iter = 0;
}

for ($i=0;$i<count($condorArray);$i++)
{
	foreach ($tempNames as $key => $value)
	{
		if (($condorArray[$i]['Webuser']) == $value)
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
		$tempArray[$iter]['Webuser'] = $condorArray[$i]['Webuser'];
		$tempNames[$iter] = $tempArray[$iter]['Webuser'];

		$tempArray[$iter]['Total_cluster']++;
	
		$prevID = $condorArray[$i]['ClusterID'];
	}
}


//izpise seznam
$condorStatusQ = new CondorManager($tempArray, 5, $_SESSION['current_page']['page_number_status_q']);
$condorStatusQ->drawCondorStatusQTable();

echo "<div style='height:30px;width:30px'></div>";
echo "<div style='position:absolute;left:15px;bottom:10px'>";
	$condorStatusQ->drawPageNavigation("ajax/status_ajax_q.php","#output_box_condor_q","page_number_status_q");
echo "</div>";

echo "<div id='status_q_selector'></div>";
include "../lib/error_tracking.php";
?>

<?php
error_reporting(0);
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
condor_generic('condor_status -xml -attributes Name,OpSys,Arch,State,Activity,LoadAvg,Memory,SlotID,Machine',$codnorOutput);
$stringOutput = convertString($codnorOutput);

$xml = simplexml_load_string($stringOutput);

$condorStatusArray = array();
$iter = 0;

foreach ($xml->c as $c)
{
	foreach ($c->a as $a)
	{
		$condorStatusArray[$iter][(string)$a['n']] = (string)($a->children());
	}

	$iter++;
}

// sortiraj po racunalnikih, slotih
foreach ($condorStatusArray as $key => $row) {
    $computer[$key]  = $row['Machine'];
    $slot[$key] = $row['SlotID'];
}
array_multisort($computer, SORT_ASC, $slot, SORT_ASC, $condorStatusArray);

$condorStatus = new CondorManager($condorStatusArray, 10, $_SESSION['current_page']['page_number_status']);
$condorStatus->drawCondorStatusTable();

echo "<div style='height:30px;width:30px'></div>";
echo "<div style='position:absolute;left:15px;bottom:10px;width:100%;'>";
	$condorStatus->drawPageNavigation("ajax/status_ajax_status.php","#output_box_condor_status","page_number_status");
echo "</div>";
echo "<div class='countdown_number'></div>";

echo "<div id='status_selector'></div>";
include "../lib/error_tracking.php";
?>

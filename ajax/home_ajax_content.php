<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po menujih
	if(isset($_POST['menu']))
		$_SESSION['menu'] = $_POST['menu'];
	
	//spremenljivke za navigiranje po straneh
	if (isset($_POST['page_number']))
		$_SESSION['current_page'] = $_POST['page_number'];
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "computer_status":

	condor_generic('condor_status -xml -attributes Name,State,Activity',$codnorOutput);
	$stringOutput = convertString($codnorOutput);

	$xml = simplexml_load_string($stringOutput);

	$computerStatusArray = array();
	$iter = 0;

	foreach ($xml->c as $c)
	{
		foreach ($c->a as $a)
		{
			$computerStatusArray[$iter][(string)$a['n']] = (string)($a->Children());
		}
	
		$iter++;
	}
	
	$computerStatus = new CondorManager($computerStatusArray, 15, $_SESSION['current_page']);
	$computerStatus->drawComputerStatusTable("ajax/home_ajax_content.php", "computer_status", "#tab_computer_status");
	$computerStatus->drawPageNavigation("ajax/home_ajax_content.php", "computer_status","#tab_computer_status", "page_number");
	
	break;
	
case "last_submits":
default:

	// ustvarjen array za prikaz v tabeli
	condor_generic('condor_q -xml -attributes ClusterId,JobStartDate,Cmd,JobStatus submitter '.$_SESSION['username'],$condorOutput);
	$stringOutput = convertString($condorOutput);

	$xml = simplexml_load_string($stringOutput);
	
	$lastSubmitsArray = array();
	$iter = 0;
	
	foreach ($xml->c as $c)
	{
		foreach ($c->a as $a)
		{
			switch ((string)$a['n'])
			{			
			case "Cmd":
				$a_path = pathinfo((string)($a->children()));
				$lastSubmitsArray[$iter][(string)$a['n']] = $a_path['basename'];
				break;
				
			default:
				$lastSubmitsArray[$iter][(string)$a['n']] = (string)($a->children());
				break;	
			}
		}
		
		$iter++;
	}
	
	//skrajsan array za tabelo, samo unikatni cluster vnosi
	$lastSubmitsCorrection = array();
	$lastClusterID = $lastSubmitsArray[0]["ClusterId"];
	$lastSubmitsCorrection[0] = $lastSubmitsArray[0];
	$iter = 1;
	
	for ($i=1;$i<count($lastSubmitsArray);$i++)
	{
		if($lastSubmitsArray[$i]["ClusterId"] != $lastClusterID)
		{
			$lastSubmitsCorrection[$iter] = $lastSubmitsArray[$i];
			$iter++;
			$lastClusterID = $lastSubmitsArray[$i]["ClusterId"];
		}
	}

	$lastSubmits = new CondorManager($lastSubmitsCorrection, 15, $_SESSION['current_page']);
	$lastSubmits->drawLastSubmitTable("ajax/home_ajax_content.php", "last_submits", "#tab_last_submits");
	$lastSubmits->drawPageNavigation("ajax/home_ajax_content.php", "last_submits","#tab_last_submits", "page_number");

	break;
}
include "../lib/error_tracking.php";
?>

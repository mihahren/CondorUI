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

	//spremenljivka za navigiranje po display file-u
	if(isset($_POST['directory']))
		$_SESSION['directory'] = $_POST['directory'];
		
	//spremenljivke za navigiranje po straneh
	if (isset($_POST['page_number']))
		$_SESSION['current_page'] = $_POST['page_number'];
	
	//spremenljivke za navigiranje po straneh za total
	if (isset($_POST['page_number_total']))
		$_SESSION['current_page_total'] = $_POST['page_number_total'];
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "queue":

	condor_generic('condor_q -xml -attributes ClusterID,ProcID,Owner,JobStartDate,CommittedTime,JobStatus,JobPrio,Size,ExecutableSize,CMD',$codnorOutput);
	$stringOutput = convertString($codnorOutput);

	$xml = simplexml_load_string($stringOutput);

	$condorQArray = array();
	$iter = 0;

	foreach ($xml->c as $c)
	{
		foreach ($c->a as $a)
		{
			switch ((string)$a['n'])
			{			
			case "CMD":
				$a_path = pathinfo((string)($a->children()));
				$condorQArray[$iter][(string)$a['n']] = $a_path['basename'];
				break;
				
			default:
				$condorQArray[$iter][(string)$a['n']] = (string)($a->Children());
				break;
			}
		}
	
		$iter++;
	}
	
	$condorQ = new CondorManager($condorQArray, 15, $_SESSION['current_page']);
	$condorQ->drawCondorQTable("ajax/advanced_ajax_content.php", "queue", "#output_box", $_SESSION['username'], $_SESSION['isadmin']);
	$condorQ->drawPageNavigation("ajax/advanced_ajax_content.php", "queue","#output_box","page_number");

	echo "<div id='queue_selector'></div>";
	break;

case "status":
	
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

	$condorStatus = new CondorManager($condorStatusArray, 15, $_SESSION['current_page']);
	$condorStatus->drawCondorStatusTable("ajax/advanced_ajax_content.php", "status", "#output_box");
	$condorStatus->drawPageNavigation("ajax/advanced_ajax_content.php", "status","#output_box","page_number");
	
	//druga tabela s skupnim stevilom racunalnikov
	condor_generic('condor_status -xml -attributes OpSys,Arch,State',$codnorOutputTotal);
	$stringOutputTotal = convertString($codnorOutputTotal);

	$xml = simplexml_load_string($stringOutputTotal);

	$condorStatusTotalArray = array();
	$iter = 0;

	foreach ($xml->c as $c)
	{
		foreach ($c->a as $a)
		{
			$condorStatusTotalArray[$iter][(string)$a['n']] = (string)($a->Children());
		}
	
		$iter++;
	}
	
	//for zanka, ki grupira vse elemente z isto arhitekturo/operacijskim istemom
	$tempArray[0]['Arch'] = $condorStatusTotalArray[0]['Arch']."/".$condorStatusTotalArray[0]['OpSys'];
	$tempArch[0] = $tempArray[0]['Arch'];
	$tempArray[0]['Total'] = 0;
	$tempArray[0]['Claimed'] = 0;
	$tempArray[0]['Unclaimed'] = 0;
	$iter = 0;

	for ($i=0;$i<count($condorStatusArray);$i++)
	{
		foreach ($tempArch as $key => $value)
		{
			if (($condorStatusTotalArray[$i]['Arch']."/".$condorStatusTotalArray[$i]['OpSys']) == $value)
			{
				$iter = $key;
				break;
			}
			else
			{
				$iter = count($tempArch);
			}
		}

		$tempArray[$iter]['Arch'] = $condorStatusTotalArray[$i]['Arch']."/".$condorStatusTotalArray[$i]['OpSys'];
		$tempArch[$iter] = $tempArray[$iter]['Arch'];

		$tempArray[$iter]['Total']++;

		if ($condorStatusTotalArray[$i]['State'] == "Claimed")
			$tempArray[$iter]['Claimed']++;
		elseif ($condorStatusTotalArray[$i]['State'] == "Unclaimed")
			$tempArray[$iter]['Unclaimed']++;
	}
		
	$condorStatusTotal = new CondorManager($tempArray, 15, $_SESSION['current_page_total']);
	$condorStatusTotal->drawCondorStatusTotalTable("ajax/advanced_ajax_content.php", "status", "#output_box");
	$condorStatusTotal->drawPageNavigation("ajax/advanced_ajax_content.php", "status","#output_box","page_number_total");

	echo "<div id='status_selector'></div>";
	break;

case "submit":

	$displayFiles = new FileManager("../files/");
	$displayFiles->displayFolders($_SESSION['directory']);

	echo "<div class='btn-group'>
		<button id='advanced_file_button' class='btn btn-primary'>Upload Files</button>
		<button class='btn disabled'>Samo upload</button>
	</div>";

	echo "<div id='submit_selector'></div>";
	break;

default:

	echo "<div class='hero-unit'>Izberi eno izmed moznosti na levi.</div>";
	break;
}
include "../lib/error_tracking.php";
?>

<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$_SESSION['cp_menu'] = "condor";

if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
{

	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		// navigacija po tabih
		if (isset($_POST['cm_menu']))
				$_SESSION['cm_menu'] = $_POST['cm_menu'];
		
		//za navigiranje po straneh
		if (isset($_POST['page_number_condor_all_q']))
			$_SESSION['current_page']['page_number_condor_all_q'] = $_POST['page_number_condor_all_q'];
		
		if (isset($_POST['page_number_condor_user_q']))
			$_SESSION['current_page']['page_number_condor_user_q'] = $_POST['page_number_condor_user_q'];
		
		if (isset($_POST['page_number_condor_all_q_cluster']))
			$_SESSION['current_page']['page_number_condor_all_q_cluster'] = $_POST['page_number_condor_all_q_cluster'];
	}

	//switch stavek za izbiro izracuna
	switch ($_SESSION['cm_menu'])
	{
	case "all_q_cluster":

		//poracunan all Queue s clustri
		condor_generic('condor_q -xml -attributes ClusterID,ProcID,Owner,JobStartDate,CommittedTime,JobStatus,JobPrio,Size,CoreSize,CMD',$condorAllQClusterOutput);
		$stringAllQClusterOutput = convertString($condorAllQClusterOutput);

		$xmlAllQCluster = simplexml_load_string($stringAllQClusterOutput);

		$condorAllQClusterArray = array();
		$iter = 0;

		foreach ($xmlAllQCluster->c as $c)
		{
			foreach ($c->a as $a)
			{
				switch ((string)$a['n'])
				{			
				case "CMD":
					$a_path = pathinfo((string)($a->children()));
					$condorAllQClusterArray[$iter][(string)$a['n']] = $a_path['basename'];
					break;
			
				default:
					$condorAllQClusterArray[$iter][(string)$a['n']] = (string)($a->children());
					break;	
				}
			}
	
			$iter++;
		}

		//skrajsan array za tabelo, samo unikatni cluster vnosi
		if (!empty($condorAllQClusterArray))
		{
			$condorAllQClusterCorrection = array();
			$lastClusterID = $condorAllQClusterArray[0]["ClusterID"];
			$condorAllQClusterCorrection[0] = $condorAllQClusterArray[0];
			$iter = 1;

			for ($i=1;$i<count($condorAllQClusterArray);$i++)
			{
				if($condorAllQClusterArray[$i]["ClusterID"] != $lastClusterID)
				{
					$condorAllQClusterCorrection[$iter] = $condorAllQClusterArray[$i];
					$iter++;
					$lastClusterID = $condorAllQClusterArray[$i]["ClusterID"];
				}
			}
		}
		else
		{
			$condorAllQClusterCorrection = array();
		}
	
		break;
	
	case "user_q":

		//poracunan samo user Queue
		condor_generic('condor_q -xml -attributes ClusterID,ProcID,Owner,JobStartDate,CommittedTime,JobStatus,JobPrio,Size,CoreSize,CMD submitter '.$_SESSION['username'],$condorUserQOutput);
		$stringUserQOutput = convertString($condorUserQOutput);

		$xmlUserQ = simplexml_load_string($stringUserQOutput);

		$condorUserQArray = array();
		$iter = 0;

		foreach ($xmlUserQ->c as $c)
		{
			foreach ($c->a as $a)
			{
				switch ((string)$a['n'])
				{			
				case "CMD":
					$a_path = pathinfo((string)($a->children()));
					$condorUserQArray[$iter][(string)$a['n']] = $a_path['basename'];
					break;
			
				default:
					$condorUserQArray[$iter][(string)$a['n']] = (string)($a->children());
					break;	
				}
			}
	
			$iter++;
		}
	
		break;
	
	case "all_q":
	default:

		//poracunan celoten Queue
		condor_generic('condor_q -xml -attributes ClusterID,ProcID,Owner,JobStartDate,CommittedTime,JobStatus,JobPrio,Size,CoreSize,CMD',$codnorAllQOutput);
		$stringAllQOutput = convertString($codnorAllQOutput);

		$xmlAllQ = simplexml_load_string($stringAllQOutput);

		$condorAllQArray = array();
		$iter = 0;

		foreach ($xmlAllQ->c as $c)
		{
			foreach ($c->a as $a)
			{
				switch ((string)$a['n'])
				{			
				case "CMD":
					$a_path = pathinfo((string)($a->children()));
					$condorAllQArray[$iter][(string)$a['n']] = $a_path['basename'];
					break;
			
				default:
					$condorAllQArray[$iter][(string)$a['n']] = (string)($a->Children());
					break;
				}
			}

			$iter++;
		}
	
		break;
	}

	//switch stavek za izbiro active bara
	$active_control = array();

	switch ($_SESSION['cm_menu'])
	{
	case "all_q_cluster";
	
		$active_control['all_q_cluster'] = "active";
		break;
	
	case "user_q";

		$active_control['user_q'] = "active";
		break;

	case "all_q";
	default:

		$active_control['all_q'] = "active";
		break;
	}

	//tabbatle izgled
	echo "<div id='condor_manager_tab' class='tabbable' >
		<ul class='nav nav-tabs' style='margin-bottom:-1px'>
			<li id='button_all_q' class='".$active_control['all_q']."'><a href='#tab_all_q' data-toggle='tab'>Celotna vrsta</a></li>
			<li id='button_all_q_cluster' class='".$active_control['all_q_cluster']."'><a href='#tab_all_q_cluster' data-toggle='tab'>Celotna vrsta (samo gruče)</a></li>
			<li id='button_user_q' class='".$active_control['user_q']."'><a href='#tab_user_q' data-toggle='tab'>Uporabniška vrsta</a></li>
		</ul>
		<div id='condor_manager_box' class='tab-content'>
			<div class='tab-pane ".$active_control['all_q']."' id='tab_all_q'>";

				$condorAllQ = new CondorManager($condorAllQArray, 15, $_SESSION['current_page']['page_number_condor_all_q']);
				$condorAllQ->drawCondorQTable("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", $_SESSION['username'], $_SESSION['isadmin']);
				$condorAllQ->drawPageNavigation("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", "page_number_condor_all_q");

			echo "</div>
			<div class='tab-pane ".$active_control['all_q_cluster']."' id='tab_all_q_cluster'>";

				$condorAllQCluster = new CondorManager($condorAllQClusterCorrection, 15, $_SESSION['current_page']['page_number_condor_all_q_cluster']);
				$condorAllQCluster->drawCondorQClusterTable("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", $_SESSION['username'], $_SESSION['isadmin']);
				$condorAllQCluster->drawPageNavigation("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", "page_number_condor_all_q_cluster");

			echo "</div>
			<div class='tab-pane ".$active_control['user_q']."' id='tab_user_q'>";

				$condorUserQ = new CondorManager($condorUserQArray, 15, $_SESSION['current_page']['page_number_condor_user_q']);
				$condorUserQ->drawCondorQTable("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", $_SESSION['username'], $_SESSION['isadmin']);
				$condorUserQ->drawPageNavigation("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", "page_number_condor_user_q");

			echo "</div>
		</div>
	</div>";

}

echo "<div id='control_panel_ajax_condor_manager'></div>";
include "../lib/error_tracking.php";
?>

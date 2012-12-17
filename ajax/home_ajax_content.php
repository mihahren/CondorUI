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
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "computer_status":

	condor_generic('condor_status -xml -attributes Name,State,Activity',$output);
	$stringOutput = convertString($output);
	$computerStatus = computerStatus($stringOutput);
	
	if (empty($computerStatus))
	{
		echo "<div style='width:100%; text-align:center'><strong>Vas condor queue je prazen!</strong></div>";
	}	
	else
	{
		echo "<table class='table table-condensed'>
			<thead>
				<tr>
					<th>Name</th>
					<th>State</th>
					<th>Activity</th>
				</tr>
			</thead>
			<tbody>";		

				foreach ($computerStatus as $key => $value)
				{
					echo "<tr>
						<td>".$value['Name']."</td>
						<td>".$value['State']."</td>
						<td>".$value['Activity']."</td>
					</tr>";
				}
								
			echo "</tbody>
		</table>";
	}
	break;

case "last_submits":
default:
		
	condor_generic('condor_q -xml -attributes ClusterId,JobStartDate,Cmd,JobStatus submitter '.$_SESSION['username'],$output);
	$stringOutput = convertString($output);
	$lastSubmits = lastSubmits($stringOutput);
	
	if (empty($lastSubmits))
	{
		echo "<div style='width:100%; text-align:center'><strong>Vas condor queue je prazen!</strong></div>";
	}	
	else
	{
		echo "<table class='table table-condensed'>
			<thead>
				<tr>
					<th>ID</th>
					<th>Submitted</th>
					<th>Name</th>
					<th>State</th>
					<th>Del</th>
				</tr>
			</thead>
			<tbody>";		

				foreach ($lastSubmits as $key => $value)
				{
					if ($value['ClusterId'] != $temp_cluster)
					{
						echo "<tr>
							<td>".$value['ClusterId']."</td>
							<td>".date("d/m - H:i",$value['JobStartDate'])."</td>
							<td>".$value['Cmd']."</td>
							<td>".$value['JobStatus']."</td>
							<td style='text-align: center;'><a class='mouse_hover' onclick=\"homeAjaxDelete('#last_submits','#tab_last_submits','".$value['ClusterId']."')\"><i class='icon-trash'></i></a></td>
						</tr>";
						
						$temp_cluster = $value['ClusterId'];
					}
				}
								
			echo "</tbody>
		</table>";
	}
	break;
}
include "../lib/error_tracking.php";
?>

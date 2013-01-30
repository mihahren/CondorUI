<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$_SESSION['cp_menu'] = "ida";

if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
{
	$fileManager = new FileManager("../files/");

	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['ida_acc_name']))
	{	
		$fileManager->copyIdaFiles($_POST['ida_acc_name']);
		
		$fileManager->createIdaSubmitFile($_POST['ida_acc_name'], $_POST['ida_end_time'], $_POST['ida_pga'], $_POST['ida_per'], $_POST['ida_xdamp'], $_SESSION['username'], $out1);
		$_SESSION['custom_error']['ida_curves'][0] = $out1;
		
		$fileManager->submitFile("idacurves/ida.sub", $_SESSION['username'], $out2);
		$_SESSION['custom_error']['ida_curves'][1] = $out2;
		
		if (isset($_POST['ida_acc_name']))
			$_SESSION['ida_acc_name'] = $_POST['ida_acc_name'];
		
		if (isset($_POST['ida_end_time']))
			$_SESSION['ida_end_time'] = $_POST['ida_end_time'];
			
		if (isset($_POST['ida_pga']))
			$_SESSION['ida_pga'] = $_POST['ida_pga'];
			
		if (isset($_POST['ida_per']))
			$_SESSION['ida_per'] = $_POST['ida_per'];
			
		if (isset($_POST['ida_xdamp']))
			$_SESSION['ida_xdamp'] = $_POST['ida_xdamp'];
	}

	//skenira mapo z akcelelogrami in obdela podatke
	$scanDir = $fileManager->localScanDir("acc-performance-test/acc");
	$nameScan = array();
	
	foreach ($scanDir as $key => $value)
	{ 
		$fullScanName = pathinfo($value);
		$nameScan[$key] = $fullScanName['filename'];
	}
	
	$uniqueNameScan = array_unique($nameScan);
	
?>
	<!-- Tableo za vnose argumentov -->
	<div class='generic_box'><div class='row-fluid'><div class='span12'>
		<form id='ida_form' method='post'><table id='ida_table' class='table table-condensed'><thead>
			<tr>
				<th class='span4' style='text-align:center;'>Akcelelogram</th>
				<th class='span2' style='text-align:center;'>arg1</th>
				<th class='span2' style='text-align:center;'>arg2</th>
				<th class='span2' style='text-align:center;'>arg3</th>
				<th class='span2' style='text-align:center;'>arg4</th>
			</tr></thead>
			<tr id='ida_default_row'>
				<td><select class='input-block-level' name='ida_acc_name[]'>
<?php
					foreach ($uniqueNameScan as $value)
					{
						echo "<option value='".$value."'>".$value."</option>";
					}
?>			
				</select></td>
				<td><input class='input-block-level' name='ida_end_time[]' type='text' maxlength='100' placeholder='ida_end_time' value='43.1900' /></td>
				<td><input class='input-block-level' name='ida_pga[]' type='text' maxlength='100' placeholder='ida_pga' value='0.5262' /></td>
				<td><input class='input-block-level' name='ida_per[]' type='text' maxlength='100' placeholder='ida_per' value='0.1' /></td>
				<td><input class='input-block-level' name='ida_xdamp[]' type='text' maxlength='100' placeholder='ida_xdamp' value='0.01' /></td>
			</tr>
			<tbody id='ida_result_row'>
			</tbody>
		</table></form>
		<img src='img/plus_sign.png' id='ida_plus_sign' style='margin-right:10px' />
		<img src='img/minus_sign.png' id='ida_minus_sign' style='margin-right:10px' />
		<button id='ida_submit_button' class='btn btn-inverse' name='ida_submit'>Submit</button>
	</div></div></div>
	
<?php
echo "<pre>";
print_r($uniqueNameScan);
print_r($_SESSION['ida_acc_name']);
print_r($_SESSION['ida_end_time']);
print_r($_SESSION['ida_pga']);
print_r($_SESSION['ida_per']);
print_r($_SESSION['ida_xdamp']);
echo "</pre>";
}

echo "<div id='control_panel_ajax_ida'></div>";
include "../lib/stats_tracking.php";
include "../lib/error_tracking.php";
?>

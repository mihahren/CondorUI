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
		$fileManager->copyIdaFiles($_POST['ida_acc_name'], "idacurves");
		
		$fileManager->createIdaSubmitFile($_POST['ida_acc_name'], $_POST['ida_end_time'], $_POST['ida_pga'], $_POST['ida_per'], $_POST['ida_xdamp'], $_SESSION['username'], $out1);
		$_SESSION['custom_error']['ida_curves'][0] = $out1;
		
		$fileManager->submitFile("idacurves/ida.sub", $_SESSION['username'], $out2);
		$_SESSION['custom_error']['ida_curves'][1] = $out2;
	}

	//skenira mapo z akcelelogrami in obdela podatke
	$scanDir = $fileManager->localScanDir("acceleration");
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
		<div class="btn-group" style="margin-bottom:15px;">
			<button id='ida_submit_button' class='btn btn-inverse' name='ida_submit'>Predloži</button>
			<button class="btn disabled">Dodajanje novih vnosov:</button>
			<button id='ida_plus_sign' class="btn disabled">+</button>
			<button id='ida_minus_sign' class="btn disabled">-</button>
		</div>
		<form id='ida_form' method='post'><table id='ida_table' class='table table-condensed'><thead>
			<tr>
				<th class='span4' style='text-align:center;'>Akcelelogram</th>
				<th class='span2' style='text-align:center;'>končni čas</th>
				<th class='span2' style='text-align:center;'>PGA</th>
				<th class='span2' style='text-align:center;'>Per</th>
				<th class='span2' style='text-align:center;'>xDamp</th>
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
	</div></div></div>
	

	
<?php
}

echo "<div id='control_panel_ajax_ida'></div>";
include "../lib/stats_tracking.php";
include "../lib/error_tracking.php";
?>

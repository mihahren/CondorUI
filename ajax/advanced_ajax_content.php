<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$displayFiles = new FileManager("../files/");

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po menujih
	$_SESSION['menu'] = $_POST['menu'];
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "queue":
	
	//sprozi globalni in user specific condor_q request
	exec('condor_q -global', $allOutput);

	exec('condor_q www-data -format %4d. ClusterId -format %-3d ProcId 2>&1', $idOutput);
	
	//shrani prvo vrstico ID condor_q requesta ter naredi iterator zanj
	$idOutputExplode = splitString($idOutput[0]);
	$iter = 0;
	
	//izpisi vse, preveri kje se ID ujema pri obeh condor_q requestih - tam doda gumb za brisanje
?>
	<form method='post' id='delete_submited_form' enctype='multipart/form-data'>
		<table id='delete_submited_table'>
			<tr><td colspan='2' style='text-align:center;'>Submited files</td></tr>
<?php

			foreach ($allOutput as $value)
			{
				echo "<tr>";
					if($value != NULL)
					{
						echo "<td style='border:0px;'><pre style='display:inline;'>".$value."</pre></td>";
						$allOutputExplode = explode(" ", ltrim($value, " "));
				
						echo "<td style='text-align:center;border-top:0px;border-bottom:0px;'>";
						if($allOutputExplode[0] === $idOutputExplode[$iter])
						{
							echo "<input type='checkbox' class='submit_delete_checkbox' name='delete_submited_file[]' value='".$allOutputExplode[0]."' />";
							$iter++;
						}
						echo "</td>";
					}
				echo "</tr>";
			}
?>
			<tr>
				<td style='text-align:right;'>Select All:</td>
				<td style='text-align:center;'><input type='checkbox' name='select_all_submited' value='false' class='select_all_submited'/></td>
			</tr>
		</table>
	</form>
	<div style='clear:both;'></div>
	<div class="button_wrapper" id="delete_submited_button"><span class="button_text">Submit</span></div>
	<div id='queue_selector'></div>
<?php
	break;

case "status":

	condor_status($out);
	print_cmd($out);
	echo "<div id='status_selector'></div>";
	break;

case "submit":
?>
	<form method="post" id="file_form" enctype="multipart/form-data">
<?php
		$displayFiles->displayTable("uploads/");
		$displayFiles->displayTable("results/");
?>
		<div style="clear: both;"></div>
		<input type="file" name="file[]" id="advanced_file_upload" multiple/ style="visibility:hidden;float:right;">
	</form>
	
	<div id="advanced_input_wrapper">
		<div class="button_wrapper" id="advanced_file_button">
			<span class="button_text">Upload Files</span>
		</div>
		<div class="button_wrapper" id="advanced_submit_button">
			<span class="button_text">Submit</span>
		</div>
	</div>
	<div id='submit_selector'></div>
<?php
	break;
}
include "../lib/error_function.php";
?>

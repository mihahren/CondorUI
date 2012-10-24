<?php
include_once "functions.php";
include_once "access_control.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po menujih
	$_SESSION['menu'] = $_POST['menu'];
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "queue":
	
	include "file_manager.php";
	
	//sprozi globalni in user specific condor_q request
	exec('condor_q -global', $allOutput);

	exec('condor_q www-data -format %4d. ClusterId -format %-3d ProcId 2>&1', $idOutput);
	
	//shrani prvo vrstico ID condor_q requesta ter naredi iterator zanj
	$idOutputExplode = splitString($idOutput[0]);
	$iter = 0;
	
	//izpisi vse, preveri kje se ID ujema pri obeh condor_q requestih - tam doda gumb za brisanje
	echo "<form method='post' id='delete_submited_form' enctype='multipart/form-data'><table id='delete_submited_table'>";
	
	echo "<tr><td colspan='2' style='text-align:center;'>Submited files</td></tr>";
	
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
	echo "<tr><td style='text-align:right;'>Select All:</td><td style='text-align:center;'><input type='checkbox' name='select_all_submited' value='false' class='select_all_submited'/></td></tr>";

	echo "</table></form><div style='clear:both;'></div>";
?>	
	<div class="button_wrapper" id="delete_submited_button">
		<span class="button_text">Submit</span>
	</div>
<?php
	echo "<div id='queue_selector'></div>";
	break;

case "status":

	condor_status($out);
	print_cmd($out);
	echo "<div id='status_selector'></div>";
	break;

case "submit":
	
	include "file_manager.php";

	$scanUploadDir = scandir($uploadDir);
	$scanResultDir = scandir($resultDir);
?>
	<form method="post" id="file_form" enctype="multipart/form-data">
	
	<!-- pregleda in izpise vse uploadane datoteke, ki ustrezajo dolocenemu uporabniku -->
	<table id="uploads_table" style="margin-right:10px;">
		<tr><td colspan="4" style="text-align:center;">Uploads</td></tr>
		<tr>
			<td>filename</td>
			<td>filetype</td>
			<td>submit</td>
			<td>delete</td>
		</tr>
<?php
		//izpise .condor in .submit file
		for ($i=0; $i<count($scanUploadDir); $i++)
		{
			$fullFileName = pathinfo($scanUploadDir[$i]);
			
			if($fullFileName['basename'] != "." && $fullFileName['basename'] != ".." && ($fullFileName['extension'] == "submit" || $fullFileName['extension'] == "condor"))
			{
?>
				<tr>
					<td><a href="<?php echo $uploadDir."/".$fullFileName['basename']; ?>"><?php echo $fullFileName['basename']; ?></a></td>
					<td style="text-align:center;"><?php echo $fullFileName['extension']; ?></td>
					<td style="text-align:center;"><input type="checkbox" class="upload_submit_checkbox" name="submit_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
					<td style="text-align:center;"><input type="checkbox" class="upload_delete_checkbox" name="delete_upload_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
				</tr>
<?php
			}
		}

		//izpise vse ostale uploadane file
		for ($i=0; $i<count($scanUploadDir); $i++)
		{
			$fullFileName = pathinfo($scanUploadDir[$i]);
			
			if($fullFileName['basename'] != "." && $fullFileName['basename'] != ".." && $fullFileName['extension'] != "submit" && $fullFileName['extension'] != "condor")
			{
?>
				<tr>
					<td><a href="<?php echo $uploadDir."/".$fullFileName['basename']; ?>"><?php echo $fullFileName['basename']; ?></a></td>
					<td style="text-align:center;"><?php echo $fullFileName['extension']; ?></td>
					<td style="text-align:center;"></td>
					<td style="text-align:center;"><input type="checkbox" class="upload_delete_checkbox" name="delete_upload_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
				</tr>
<?php
			}
		}	
?>			
		<tr><td colspan="2" style="text-align:right;">Select All:</td>
			<td style="text-align:center;"><input type="checkbox" name="select_all_submits" value="false" class="select_all_submits" /></td>
			<td style="text-align:center;"><input type="checkbox" name="select_all_uploads" value="false" class="select_all_uploads" /></td></tr>
	</table>
	
	<!-- pregleda in izpise vse result datoteke, ki ustrezajo dolocenemu uporabniku -->
	<table id="results_table" style="margin-bottom: 0px;">
		<tr><td colspan="3" style="text-align:center;">Results</td></tr>
		<tr>
			<td>filename</td>
			<td>filetype</td>
			<td>delete</td>
		</tr>
<?php
		for ($i=0; $i<count($scanResultDir); $i++)
		{
			$fullFileName = pathinfo($scanResultDir[$i]);
			
			if($fullFileName['basename'] != "." && $fullFileName['basename'] != "..")
			{
?>
				<tr>
					<td><a href="<?php echo $resultDir."/".$fullFileName['basename']; ?>"><?php echo $fullFileName['basename']; ?></a></td>
					<td style="text-align:center;"><?php echo $fullFileName['extension']; ?></td>
					<td style="text-align:center;"><input type="checkbox" class="result_delete_checkbox" name="delete_result_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
				</tr>
<?php
			}
		}
?>			
		<tr><td colspan="2" style="text-align:right;">Select All:</td><td style="text-align:center;"><input type="checkbox" name="select_all_results" value="false" class="select_all_results"/></td></tr>
	</table>
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
<?php
	echo "<div id='submit_selector'></div>";
	break;
}
include "error_function.php";
?>

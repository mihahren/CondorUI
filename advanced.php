<?php
include_once "functions.php";
include_once "access_control.php";

if ($_SESSION['access'] == "access")
{
?>
<div id="input_box">
	<div class="button_wrapper" id="queue_button">
		<img src="images/menu_button.png" />
		<span class="button_text">Condor Queue</span>
	</div>
	<div class="button_wrapper" id="status_button">
		<img src="images/menu_button.png" />
		<span class="button_text">Condor Status</span>
	</div>
	<div class="button_wrapper" id="submit_button">
		<img src="images/menu_button.png" />
		<span class="button_text">Submit...</span>
	</div>
</div>
<div id="output_box">
<?php

	//switch stavek za izbiro podmenuja v advanced naèinu
	switch($_SESSION['menu_2'])
	{
	case "queue":

		condor_q($out);
		print_cmd($out);
		echo date(DATE_COOKIE);
		echo "<div id='queue_selector'></div>";
		break;

	case "status":

		condor_status($out);
		print_cmd($out);
		echo date(DATE_COOKIE);
		echo "<div id='status_selector'></div>";
		break;

	case "submit":
		
		//pregleda, ce obstajajo vsi potrebni direktoriji za uporabnika - ce ne, jih zgenerira - ter jim doloci spremenljivke
		if (!is_dir($uploadDir = "uploads/".$_SESSION['login_id']))
		{
			mkdir($uploadDir);
		}
		
		if (!is_dir($resultDir = "results/".$_SESSION['login_id']))
		{
			mkdir($resultDir);
		}

		if($_SERVER['REQUEST_METHOD'] == "POST")
		{	
			//preveri, ali so bili uploadani kaksni file-i in jih prenese v ustrezno mapo
			if (!empty($_FILES['file']['tmp_name'][0]))
			{
				for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
				{
					if (file_exists($uploadDir."/".$_FILES['file']['name'][$i]))
					{
						$_SESSION['custom_error'][2] = 'Datoteka ze obstaja.';
					}
					else
					{
						move_uploaded_file($_FILES['file']['tmp_name'][$i],$uploadDir."/".$_FILES['file']['name'][$i]);
					}
				}
			}
			
			//preveri, ce je potrebno kaksno upload datoteko zbrisati
			if (!empty($_POST['delete_upload_file']))
			{
				for ($i=0; $i<(count($_POST['delete_upload_file'])); $i++)
				{
					unlink($uploadDir."/".$_POST['delete_upload_file'][$i]);	
				}
			}
			
			//preveri, ce je potrebno kaksno result datoteko zbrisati
			if (!empty($_POST['delete_result_file']))
			{
				for ($i=0; $i<(count($_POST['delete_result_file'])); $i++)
				{
					unlink($resultDir."/".$_POST['delete_result_file'][$i]);	
				}
			}
			
			//preveri, ce je potrebno kaksen file submitat
			if (!empty($_POST['submit_file']))
			{
				if(!file_exists($uploadDir."/".$_POST['submit_file']))
				{
					$_SESSION['custom_error'][3] = 'Datoteka vec ne obstaja!';
				}
				else
				{
					condor_submit($uploadDir."/".$_POST['submit_file'], $out);
					$_SESSION['custom_error'][4] = $out;
				}
			}
			
			unset($_FILES['file']);
			unset($_POST['submit_file']);
			unset($_POST['delete_upload_file']);
			unset($_POST['delete_result_file']);
		}

		$scanUploadDir = scandir($uploadDir);
		$scanResultDir = scandir($resultDir);
?>
		<form method="post" id="file_form" enctype="multipart/form-data">
		
		<!-- pregleda in izpise vse uploadane datoteke, ki ustrezajo dolocenemu uporabniku -->
		<table id="uploads_table">
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
						<td><a href="<?php echo $uploadDir."/".$fullFileName['basename']; ?>"><?php echo $fullFileName['filename']; ?></a></td>
						<td style="text-align:center;"><?php echo $fullFileName['extension']; ?></td>
						<td style="text-align:center;"><input type="radio" name="submit_file" value="<?php echo $fullFileName['basename']; ?>" /></td>
						<td style="text-align:center;"><input type="checkbox" name="delete_upload_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
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
						<td><a href="<?php echo $uploadDir."/".$fullFileName['basename']; ?>"><?php echo $fullFileName['filename']; ?></a></td>
						<td style="text-align:center;"><?php echo $fullFileName['extension']; ?></td>
						<td style="text-align:center;"></td>
						<td style="text-align:center;"><input type="checkbox" name="delete_upload_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
					</tr>
<?php
				}
			}	
?>			
			<tr><td colspan="3" style="text-align:right;">Select All:</td><td style="text-align:center;"><input type="checkbox" name="select_all_uploads" value="false" /></td></tr>
		</table>
		
		<!-- pregleda in izpise vse result datoteke, ki ustrezajo dolocenemu uporabniku -->
		<table id="results_table">
			<tr><td colspan="3" style="text-align:center;">Results</td></tr>
			<tr>
				<td>filename</td>
				<td>filetype</td>
				<td>delete</td>
			</tr>
<?php		
			//izpise .condor in .submit file
			for ($i=0; $i<count($scanResultDir); $i++)
			{
				$fullFileName = pathinfo($scanResultDir[$i]);
				
				if($fullFileName['basename'] != "." && $fullFileName['basename'] != "..")
				{
?>
					<tr>
						<td><a href="<?php echo $resultDir."/".$fullFileName['basename']; ?>"><?php echo $fullFileName['filename']; ?></a></td>
						<td style="text-align:center;"><?php echo $fullFileName['extension']; ?></td>
						<td style="text-align:center;"><input type="checkbox" name="delete_result_file[]" value="<?php echo $fullFileName['basename']; ?>" /></td>
					</tr>
<?php
				}
			}
?>			
			<tr><td colspan="2" style="text-align:right;">Select All:</td><td style="text-align:center;"><input type="checkbox" name="select_all_results" value="false" /></td></tr>
		</table></br>
		<input type="file" name="file[]" id="file" multiple/>
		</form>
		<button id="confirm_submit">Submit</button><br />
<?php
		echo date(DATE_COOKIE);
		echo "<div id='submit_selector'></div>";
		break;
	}
?>
</div>
<?php
}
else
{
echo "Prislo je do napake!";
}
?>
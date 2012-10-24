<?php
include_once "functions.php";
include_once "access_control.php";

//pregleda, ce obstajajo vsi potrebni direktoriji za uporabnika - ce ne, jih zgenerira - ter jim doloci spremenljivke
if (!is_dir($uploadDir = "uploads/".$_SESSION['login_id']))
{
	mkdir($uploadDir, 0777, true);
}

if (!is_dir($resultDir = "results/".$_SESSION['login_id']))
{
	mkdir($resultDir, 0777, true);
}

if($_SERVER['REQUEST_METHOD'] == "POST")
{	
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
	
	//preveri, ce je potrebno kaksno submitano datoteko odstranit iz condor queue
	if (!empty($_POST['delete_submited_file']))
	{
		for ($i=0; $i<(count($_POST['delete_submited_file'])); $i++)
		{
			condor_remove($_POST['delete_submited_file'][$i], $removeOut);
			$_SESSION['custom_error'][0][$i] = $removeOut;
		}
	}
	
	//preveri, ali so bili uploadani kaksni file-i in jih prenese v ustrezno mapo
	if (!empty($_FILES['file']['tmp_name'][0]))
	{
		for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
		{
			if (file_exists($uploadDir."/".$_FILES['file']['name'][$i]))
			{
				$_SESSION['custom_error'][1][$i] = 'Datoteka '.$_FILES['file']['name'][$i].' ze obstaja.';
			}
			else
			{
				move_uploaded_file($_FILES['file']['tmp_name'][$i],$uploadDir."/".$_FILES['file']['name'][$i]);
			}
		}
	}
	
	//preveri, ce je potrebno ustvariti kaksen submit file
	if (!empty($_POST['create_submit_file']))
	{
		for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
		{
			if (file_exists($uploadDir."/".$_FILES['file']['name'][$i].".submit"))
			{
				$_SESSION['custom_error'][2][$i] = "Datoteka ".$_FILES['file']['name'][$i].".submit ze obstaja.";
			}
			else
			{
				$file = fopen($uploadDir."/".$_FILES['file']['name'][$i].".submit","x+");
				
					//string za vpisat v submit datoteko
					$string="Universe=vanilla
					Executable=".$uploadDir."/".$_FILES['file']['name'][$i]."
					Output=".$resultDir."/".$_FILES['file']['name'][$i].".output
					Error=".$resultDir."/".$_FILES['file']['name'][$i].".error
					Log=".$resultDir."/".$_FILES['file']['name'][$i].".log

					should_transfer_files = YES
					when_to_transfer_output = ON_EXIT

					queue";
					
					$trimmedString=str_replace("\t","",$string);
						
					fwrite($file,$trimmedString);
				
				fclose($file);
				
				//nemudoma pripravi uploadane file za submitanje
				$_SESSION['submit_file'][$i] = $_FILES['file']['name'][$i].".submit";
			}
		}
	}
	
	//preveri, ce je potrebno kaksen file submitat
	if (!empty($_POST['submit_file']))
	{
		$_SESSION['submit_file'] = $_POST['submit_file'];
	}
	
	if (!empty($_SESSION['submit_file']))
	{
		foreach ($_SESSION['submit_file'] as $key => $value)
		{
			if(!file_exists($uploadDir."/".$value))
			{
				$_SESSION['custom_error'][3][$key] = "Datoteka ".$value." vec ne obstaja!";
			}
			else
			{
				condor_submit($uploadDir."/".$value, $submitOut);
				$_SESSION['custom_error'][4][$key] = $submitOut;
			}
		}
	}

	unset($_FILES['file']);
	unset($_POST['submit_file']);
	unset($_SESSION['submit_file']);
	unset($_POST['delete_upload_file']);
	unset($_POST['delete_result_file']);
	unset($_POST['create_submit_file']);
}
?>

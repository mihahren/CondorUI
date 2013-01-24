<?php
include_once "functions.php";
include_once "access_control.php";
include_once "classes.php";

//pregleda, ce obstajajo vsi potrebni direktoriji za uporabnika - ce ne, jih zgenerira - ter jim doloci spremenljivke
$fileManager = new FileManager("../files/");

if($_SERVER['REQUEST_METHOD'] == "POST")
{	
	//preveri, ce je potrebno kaksno datoteko zbrisati
	if (!empty($_POST['delete_file']))
	{
		if(is_array($_POST['delete_file']))
		{
			for ($i=0; $i<(count($_POST['delete_file'])); $i++)
			{
				$fileManager->removeFile($_POST['delete_file'][$i]);
				$_SESSION['custom_error']['file_delete'] = "Uspesno izbrisano.";
			}
		}
		else
		{
			$fileManager->removeFile($_POST['delete_file']);
			$_SESSION['custom_error']['file_delete'] = "Uspesno izbrisano.";
		}
	}
	
	//preveri, ce je potrebno kaksno submitano datoteko odstranit iz condor queue
	if (!empty($_POST['delete_submited_file']))
	{
		if(is_array($_POST['delete_submited_file']))
		{
			for ($i=0; $i<(count($_POST['delete_submited_file'])); $i++)
			{
				condor_remove($_POST['delete_submited_file'][$i], $removeOut);
				$_SESSION['custom_error']['submit_delete'][$i] = $removeOut;
			}
		}
		else
		{
			condor_remove($_POST['delete_submited_file'], $removeOut);
			$_SESSION['custom_error']['submit_delete'] = $removeOut;
		}
	}
	
	//preveri, ali so bili uploadani kaksni file-i in jih prenese v ustrezno mapo
	if (!empty($_FILES['file']['tmp_name'][0]))
	{
		for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
		{
			$fileManager->uploadFile($_FILES['file']['tmp_name'][$i], $_FILES['file']['name'][$i], $_SESSION['username'], $out);
			$_SESSION['custom_error']['uploads'][$i] = $out;
		}
	}
	
	//preveri, ce je potrebno ustvariti kaksen submit file
	if (!empty($_POST['create_submit_file']))
	{
		for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
		{
			$fileManager->createSubmitFile($_FILES['file']['name'][$i], $_SESSION['username'], $out);
			$_SESSION['custom_error']['create_submit'][$i] = $out;
				
			//nemudoma pripravi uploadane file za submitanje
			$_SESSION['submit_file'][$i] = "uploads/".$_FILES['file']['name'][$i].".submit";
		}
	}
	
	//preveri, ce je potrebno kaksen file submitat
	if (!empty($_POST['submit_file']))
	{
		$_SESSION['submit_file'] = $_POST['submit_file'];
	}
	
	if (!empty($_SESSION['submit_file']))
	{	
		$fileManager->submitFile($_SESSION['submit_file'], $_SESSION['username'], $out);
		
		foreach ($out as $key => $value)
		{
			$_SESSION['custom_error']['submits'][$key] = $value;
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

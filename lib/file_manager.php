<?php
include_once "functions.php";
include_once "access_control.php";
include_once "classes.php";

//pregleda, ce obstajajo vsi potrebni direktoriji za uporabnika - ce ne, jih zgenerira - ter jim doloci spremenljivke
$fileManager = new FileManager("../files/");

if($_SERVER['REQUEST_METHOD'] == "POST")
{	
	//preveri, ali so bili uploadani kaksni file-i in jih prenese ter razpakira v ustrezne mape
	if ($_POST['upload_file'] == "true" && isset($_FILES['file']) && isset($_POST['upload_path']))
	{
		//a gre za array ali samo en file
		if (is_array($_FILES['file']['name']))
		{
			for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
			{
				$fileManager->uploadFile($_FILES['file']['tmp_name'][$i], $_FILES['file']['name'][$i], $_POST['upload_path'], $upload_out);
				$_SESSION['custom_error']['uploads'][$i] = $upload_out;
			}
		}
		else
		{
			$fileManager->uploadFile($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_POST['upload_path'], $upload_out);
			$_SESSION['custom_error']['uploads'] = $upload_out;		
		}
	}
	
	//preveri, ali je kaksen file potrebno razpakirati
	if ($_POST['unzip_file'] == "true" && isset($_POST['file_zip']))
	{
		//a gre za array ali samo en file
		if (is_array($_POST['file_zip']))
		{
			for ($i=0; $i<count($_POST['file_zip']); $i++)
			{
				$fullFileName = pathinfo($_POST['file_zip'][$i]);
				$zipArray = array("zip");
	
				//preveri, ce je file .zip
				if (in_array($fullFileName['extension'], $zipArray))
				{
					$unzip_success = $fileManager->unzipFile($_POST['file_zip'][$i], $unzip_out);
					$_SESSION['custom_error']['unzip'][$i] = $unzip_out;
				}
			}
		}
		else
		{
			$fullFileName = pathinfo($_POST['file_zip']);
			$zipArray = array("zip");

			//preveri, ce je file .zip
			if (in_array($fullFileName['extension'], $zipArray))
			{
				$unzip_success = $fileManager->unzipFile($_POST['file_zip'], $unzip_out);
				$_SESSION['custom_error']['unzip'] = $unzip_out;
			}
		}
	}

	//preveri, ce je potrebno kaksno datoteko zbrisati
	if (isset($_POST['delete_file']))
	{
		//a gre za array ali samo en file
		if(is_array($_POST['delete_file']))
		{
			for ($i=0; $i<(count($_POST['delete_file'])); $i++)
			{
				$fullFileName = pathinfo($_POST['delete_file'][$i]);
				$fileManager->removeFile($_POST['delete_file'][$i]);
				$_SESSION['custom_error']['file_delete'] = $fullFileName['basename']." uspesno izbrisan.";
			}
		}
		else
		{
			$fullFileName = pathinfo($_POST['delete_file']);
			$fileManager->removeFile($_POST['delete_file']);
			$_SESSION['custom_error']['file_delete'] = $fullFileName['basename']." uspesno izbrisan.";
		}
	}
	
	//preveri, ce je potrebno kaksen file submitat
	if (isset($_POST['submit_file']))
	{	
		//a gre za array ali samo en file
		if(is_array($_POST['submit_file']))
		{
			for ($i=0; $i<(count($_POST['submit_file'])); $i++)
			{
				$fileManager->submitFile($_POST['submit_file'][$i], $_SESSION['username'], $submitOut);
		
				foreach ($submitOut as $key => $value)
				{
					$_SESSION['custom_error']['submits'][$i][$key] = $value;
				}
			}
		}
		else
		{
			$fileManager->submitFile($_POST['submit_file'], $_SESSION['username'], $submitOut);
		
			foreach ($submitOut as $key => $value)
			{
				$_SESSION['custom_error']['submits'][$key] = $value;
			}
		}
	}
	
	//preveri, ce je potrebno kaksno submitano datoteko odstranit iz condor queue
	if (!empty($_POST['delete_submited_file']))
	{
		//a gre za array ali samo en file
		if(is_array($_POST['delete_submited_file']))
		{
			for ($i=0; $i<(count($_POST['delete_submited_file'])); $i++)
			{
				condor_generic('condor_rm '.$_POST['delete_submited_file'][$i], $removeOut);
				$_SESSION['custom_error']['submit_delete'][$i] = $removeOut;
			}
		}
		else
		{
			condor_generic('condor_rm '.$_POST['delete_submited_file'], $removeOut);
			$_SESSION['custom_error']['submit_delete'] = $removeOut;
		}
	}
	
	//ustvari nove mape za file manager
	if (isset($_POST['new_folder_desktop']))
	{
		$fileManager->makeOutsideDir($_POST['new_folder_path'].$_POST['new_folder_name'], $newFolderOut);
		$_SESSION['custom_error']['new_folder'] = $newFolderOut;
	}
				
	if (isset($_POST['new_folder_mobile']))
	{
		$fileManager->makeOutsideDir($_POST['new_folder_path'].$_POST['new_folder_name'], $newFolderOut);
		$_SESSION['custom_error']['new_folder'] = $newFolderOut;
	}
}

if ($_SERVER['REQUEST_METHOD'] != "POST")
{
	unset($_POST);
	unset($_FILES);
}
?>

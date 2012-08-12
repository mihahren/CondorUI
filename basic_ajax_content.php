<?php
include_once "functions.php";
include_once "access_control.php";

//pregleda, ce obstajajo vsi potrebni direktoriji za uporabnika - ce ne, jih zgenerira - ter jim doloci spremenljivke
if (!is_dir($uploadDir = "uploads/".$_SESSION['login_id']))
{
	mkdir($uploadDir);
}

if (!is_dir($resultDir = "results/".$_SESSION['login_id']))
{
	mkdir($resultDir);
}

//preveri, ce je potrebno ustvariti kaksen submit file
if (!empty($_FILES['file']['tmp_name'][0]))
{
	for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
	{
		if (file_exists($uploadDir."/".$_FILES['file']['name'][$i].".submit"))
		{
			$_SESSION['custom_error'][$i] = "Datoteka ".$_FILES['file']['name']." ze obstaja.";
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
		}
	}
}
?>
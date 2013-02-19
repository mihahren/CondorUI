<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$_SESSION['cp_menu'] = "ida-zip";

if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
{
	$fileManager = new FileManager("../files/");

	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['file']))
	{
		for ($i=0; $i<count($_FILES['file']['name']); $i++)
		{
			$fullFileName = pathinfo($_FILES['file']['name'][$i]);
			$zipArray = array("zip");

			//preveri, ce je file .zip
			if (in_array($fullFileName['extension'], $zipArray))
			{
				//razpakira
				$unzip_success = $fileManager->unzipFile($_POST['upload_path'].$_FILES['file']['name'][$i], $unzip_out);
				$_SESSION['custom_error']['unzip'][$i] = $unzip_out;
				
				if ($unzip_success)
				{
					$scanDir = $fileManager->scanDir($_POST['upload_path'].$fullFileName['filename']);
			
					//gre cez vse razpakirane datoteke
					foreach ($scanDir as $key1 => $value)
					{
						$argFileName = pathinfo($_POST['upload_path'].$fullFileName['filename']."/".$value);
						$accArray = array("arg");
		
						//preveri, ce je file arg
						if (in_array($argFileName['extension'], $accArray))
						{
							$tempAccArray = $fileManager->readArgFile($argFileName['filename'], $fullFileName['filename']);
							
							$tempAccFileName[0] = $tempAccArray[0][2];
							$iter = 1;
							foreach ($tempAccArray as $value)
							{
								if (!in_array($value[2], $tempAccFileName))
								{
									$tempAccFileName[$iter] = $value[2];
									$iter++;
								}
							}
							
							$fileManager->copyIdaFiles($tempAccFileName, $fullFileName['filename']);
							
							$fileManager->createIdaZipSubmitFile($tempAccArray, $fullFileName['filename'], $_SESSION['username'], $out1);
							$_SESSION['custom_error']['ida_curves'][0] = $out1;
		
							$fileManager->submitFile($fullFileName['filename']."/ida.sub", $_SESSION['username'], $out2);
							$_SESSION['custom_error']['ida_curves'][0] = $out2;
							
							break;
						}
					}
				}
			}
		}
	}
?>

	<div class='generic_box'>
		<p>
			Samodejno prenese in razpakira zip datoteko v mapo z enakim imenom ter predloži ustvarjeno submit datoteko. Potrebno je poskrbeti, da:
			<ul>
				<li>Mapa z istim imenom, kot je zip datoteka, še ne obstaja.</li>
				<li>Submit datoteka se ustvari samodejno.</li>
				<li>Veljavne končnice za submit datoteko so .sub, .submit in .condor.</li>
				<li>Obvezne datoteke so pospeški (.acc, .AEi), in argumenti (.arg).</li>
				<li>Vse prej opisane datoteke morajo imeti isto ime.</li>
				<li>Program samodejno uporabi samo prve najdene datoteke.</li>
				<li>Po končanem prenašanju se lahko submit datoteke tudi ročno predloži v izračun znotraj menija "Upravljanje z datotekami". Datoteke se nahajajo v ustreznih mapah.</li>
			</ul>
		</p>
		<div class="btn-group" style="margin-top:5px">
			<button id="ctr_pnl_ida_zip_file_button" class="btn btn-inverse">Naloži ZIP datoteke</button>
			<button class="btn disabled">Ustvarjena bo mapa z imenom ZIP datoteke</button>
		</div>
		<form method="post" id="ctr_pnl_ida_zip_form" enctype="multipart/form-data" style="display:none">
			<input type="hidden" name="upload_file" value="true" />
			<input type="hidden" name="upload_path" value="" />
			<input type="file" name="file[]" id="ctr_pnl_ida_zip_upload" multiple/><br />
		</form>
	</div>

<?php
}

echo "<div id='control_panel_ajax_ida_zip'></div>";
include "../lib/stats_tracking.php";
include "../lib/error_tracking.php";
?>

<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$fileManager = new FileManager("../files/");

$_SESSION['cp_menu'] = "zip";

if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
{
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['file']))
	{
		for ($i=0; $i<count($_FILES['file']['name']); $i++)
		{
			$fullFileName = pathinfo($_FILES['file']['name'][$i]);
			$zipArray = array("zip");

			//preveri, ce je file .zip
			if (in_array($fullFileName['extension'], $zipArray))
			{
				$unzip_success = $fileManager->unzipFile($_POST['upload_path'].$_FILES['file']['name'][$i], $unzip_out);
				$_SESSION['custom_error']['unzip'][$i] = $unzip_out;
			
				if ($unzip_success)
				{
					$scanDir = $fileManager->scanDir($_POST['upload_path'].$fullFileName['filename']);
			
					//gre cez vse razpakirane datoteke
					foreach ($scanDir as $key1 => $value1)
					{
						$fullFileName1 = pathinfo($_POST['upload_path'].$fullFileName['filename']."/".$value1);
						$zipArray = array("submit","sub","condor");
		
						//preveri, ce je file .sub, .submit ali .condor
						if (in_array($fullFileName1['extension'], $zipArray))
						{
							$fileManager->submitFile($_POST['upload_path'].$fullFileName['filename']."/".$value1, $_SESSION['username'], $out);
	
							foreach ($out as $key2 => $value2)
							{
								$_SESSION['custom_error']['submits'][$i][$key2] = $value2;
							}
					
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
			Samodejno prenese in razpakira zip datoteko v mapo z enakim imenom ter predloži najdeno submit datoteko. Potrebno je poskrbeti, da:
			<ul>
				<li>Mapa z istim imenom, kot je zip datoteka, še ne obstaja.</li>
				<li>V zip datoteki se nahaja vsaj ena submit datoteka in ima relativno postavljene poti do ostalih datotek (Executable, Output, Error, Log).</li>
				<li>Submit datoteka se nahaja v korenu zip datoteke. Sicer je program ne bo samodejno predložil.</li>
				<li>Veljavne končnice za submit datoteko so .sub, .submit in .condor.</li>
				<li>Program samodejno predloži samo prvo najdeno submit datoteko.</li>
				<li>Po končanem prenašanju se lahko submit datoteke tudi ročno predloži v izračun znotraj menija "Upravljanje z datotekami". Datoteke se nahajajo v ustreznih mapah.</li>
			</ul>
		</p>
		<div class="btn-group" style="margin-top:5px">
			<button id="ctr_pnl_zip_file_button" class="btn btn-inverse">Naloži ZIP datoteke</button>
			<button class="btn disabled">Ustvarjena bo mapa z imenom ZIP datoteke</button>
		</div>
		<form method="post" id="ctr_pnl_zip_form" enctype="multipart/form-data" style="display:none">
			<input type="hidden" name="upload_file" value="true" />
			<input type="hidden" name="upload_path" value="" />
			<input type="file" name="file[]" id="ctr_pnl_zip_upload" multiple/><br />
		</form>
	</div>

<?php
}

echo "<div id='control_panel_ajax_zip'></div>";
include "../lib/stats_tracking.php";
include "../lib/error_tracking.php";
?>

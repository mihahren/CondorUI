<?php
error_reporting(0);
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$_SESSION['cp_menu'] = "file";

if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
{

	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		if (isset($_POST['directory']))
			$_SESSION['directory'] = $_POST['directory'];
	}

	echo "<div class='generic_box'>";
		$fileManager = new FileManager("../files/");
		$fileManager->displayFolders($_SESSION['directory'],'ajax/control_panel_ajax_file_manager.php','#output_box_control_panel');
?>
		<div class="btn-group">
			<button id="ctr_pnl_file_button" class="btn btn-inverse">Naloži datoteke</button>
			<button class="btn disabled">v trenutno odprto mapo</button>
		</div>
		
		<!-- vidno v desktop -->
		<form method="post" id="new_folder_form_desktop" class="pull-right visible-desktop" enctype="multipart/form-data">
			<div class="input-append">
				<input type="text" name="new_folder_name" value="mapa" />
				<input type="hidden" name="new_folder_path" value="<?php echo $_SESSION['directory'] ?>" />
				<input type="hidden" name="new_folder_desktop" value="true" />
				<button id="new_folder_button_desktop" type="button" class="btn btn-inverse">Ustvari mapo</button>
			</div>
		</form>
		<!-- vidno v desktop tablet/mobile -->
		<form method="post" id="new_folder_form_mobile" class="hidden-desktop" enctype="multipart/form-data" style="margin-top:20px">
			<div class="input-append">
				<input type="text" name="new_folder_name" value="mapa" />
				<input type="hidden" name="new_folder_path" value="<?php echo $_SESSION['directory'] ?>" />
				<input type="hidden" name="new_folder_mobile" value="true" />
				<button id="new_folder_button_mobile" type="button" class="btn btn-inverse">Ustvari mapo</button>
			</div>
		</form>
		
		<form method="post" id="ctr_pnl_file_form" enctype="multipart/form-data" style="display:none">
			<input type="hidden" name="upload_file" value="true" />
			<input type="hidden" name="upload_path" value="<?php echo $_SESSION['directory'] ?>" />
			<input type="hidden" name="unzip_file" value="false" />
			<input type="file" name="file[]" id="ctr_pnl_file_upload" multiple/><br />
		</form>

<?php
}

echo "</div><div id='control_panel_ajax_file_manager'></div>";
include "../lib/stats_tracking.php";
include "../lib/error_tracking.php";
?>

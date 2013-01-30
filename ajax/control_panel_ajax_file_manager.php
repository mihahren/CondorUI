<?php
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
			<button class="btn disabled">Naloži datoteke v trenutno mapo.</button>
		</div>
		<form method="post" id="ctr_pnl_file_form" enctype="multipart/form-data" style="display:none">
			<input type="hidden" name="upload_file" value="true" />
			<input type="hidden" name="upload_path" value="<?php echo $_SESSION['directory'] ?>" />
			<input type="hidden" name="unzip_file" value="false" />
			<input type="file" name="file[]" id="ctr_pnl_file_upload" multiple/><br />
		</form>
	</div>

<?php
}

echo "<div id='control_panel_ajax_file_manager'></div>";
include "../lib/stats_tracking.php";
include "../lib/error_tracking.php";
?>

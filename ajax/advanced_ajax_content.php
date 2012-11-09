<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

$displayFiles = new FileManager("../files/");

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po menujih
	$_SESSION['menu'] = $_POST['menu'];

	//spremenljivka za navigiranje po display file-u
	$_SESSION['directory'] = $_POST['directory'];
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "queue":

	echo "<form method='post' id='delete_submited_form' enctype='multipart/form-data'>";

	condor_qAdvanced("www-data");
?>
	</form>
	<div style='clear:both;'></div>
	<div class='button_wrapper' id='delete_submited_button'><span class='button_text'>Submit</span></div>
	<div id='queue_selector'></div>
<?php
	break;

case "status":

	condor_status($out);
	print_cmd($out);
	echo "<div id='status_selector'></div>";
	break;

case "submit":

	echo "<form method='post' id='file_form' enctype='multipart/form-data'>";

		$displayFiles->displayFolders($_SESSION['directory']);
?>
		<div style='clear:both;'></div>
		<input type='file' name='file[]' id='advanced_file_upload' multiple/ style='visibility:hidden;float:right;'>
	</form>
	
	<div id='advanced_input_wrapper'>
		<div class='button_wrapper' id='advanced_file_button'>
			<span class='button_text'>Upload Files</span>
		</div>
		<div class='button_wrapper' id='advanced_submit_button'>
			<span class='button_text'>Submit</span>
		</div>
	</div>
	<div id='submit_selector'></div>
<?php
	break;
}
include "../lib/error_function.php";
?>

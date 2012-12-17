<?php
include_once "../lib/functions.php";
include_once "../lib/classes.php";
include_once "../lib/access_control.php";
include_once "../lib/file_manager.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	//spremenljivke za navigiranje po menujih
	if(isset($_POST['menu']))
		$_SESSION['menu'] = $_POST['menu'];

	//spremenljivka za navigiranje po display file-u
	if(isset($_POST['directory']))
		$_SESSION['directory'] = $_POST['directory'];
}

//switch stavek za izbiro podmenuja v advanced naèinu
switch($_SESSION['menu'])
{
case "queue":

	condor_qAdvanced($_SESSION['username']);
	echo "<div id='queue_selector'></div>";
	break;

case "status":

	condor_status($out);
	print_cmd($out);
	echo "<div id='status_selector'></div>";
	break;

case "submit":

	$displayFiles = new FileManager("../files/");
	$displayFiles->displayFolders($_SESSION['directory']);
?>
	<div class="btn-group">
		<button id="advanced_file_button" class="btn btn-primary">Upload Files</button>
		<button class="btn disabled">Samo upload</button>
	</div>
<?php
	echo "<div id='submit_selector'></div>";
	break;

default:

	echo "<div class='hero-unit'>Izberi eno izmed moznosti na levi.</div>";
	break;
}
include "../lib/error_tracking.php";
?>

<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

$userManager = new UserManager();	// zacne nov ali nadaljuje obstojec session

if (($_SERVER['REQUEST_METHOD'] == "POST") && isset($_POST['submit_entry_user_options'])) // preveri, ce zelimo spremeniti nastavitve
{
	if (!isset($_POST['errorstatus'])) // preveri, ali errorstatus ni nastavljen
	{
		$_POST['errorstatus'] = 0;
	}
	
	$userManager->changeErrorStatus($_POST['errorstatus'], $_SESSION['login_id'], $out);
	$_SESSION['errorstatus'] = $_POST['errorstatus'];
	$_SESSION['custom_error']['errorstatus'] = $out;
}
?>

<form class="generic_box" style="padding-top:5px" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<h1>Nastavitve</h1>
	<div class="separator"></div>
	<label class='checkbox'><input type='checkbox' name='errorstatus' value='1' <?php if($_SESSION['errorstatus'] == 1){echo "checked";} ?>/>Prikaži sporočila</label>
	<div class="separator"></div>
	<button class="btn btn-inverse" type="submit" name="submit_entry_user_options">Shrani nastavitve</button>
</form>

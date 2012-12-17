<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

$userManager = new UserManager();	//zacne nov ali nadaljuje obstojec session

if (($_SERVER['REQUEST_METHOD'] == "POST") && isset($_POST['submit_entry_register']))
{
	$register_user = $userManager->inputNewUser($_POST['new_username'], $_POST['new_password'], $_POST['new_email'], $_POST['new_isadmin'], $_POST['new_registertime'], $_POST['new_activetime']);

	$_SESSION['custom_error']['register_user'] = $register_user;
}
?>

<form class="generic_box" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<h1>Dodaj uporabnika</h1>
	<div class="separator"></div>
	<input class="input-block-level" name="new_username" type="text" maxlength="100" placeholder="Username" />
	<input class="input-block-level" name="new_password" type="password" maxlength="150" placeholder="Password" />
	<input class="input-block-level" name="new_email" type="email" maxlength="100" placeholder="Email" />
	<select class="input-block-level" name="new_activetime"> 
		<option value="<?php echo (1 * 24 * 60 * 60); ?>" selected="selected">1 day trial</option>
		<option value="<?php echo (15 * 24 * 60 * 60); ?>">15 day trial</option>
<?php 
		if ($_SESSION['access'] == "admin")
		{
			echo "<option value='0'>neomejeno</option>";
		}
?>
	</select>
<?php 
	if ($_SESSION['access'] == "admin")
	{
		echo "<label class='checkbox'><input type='checkbox' name='new_isadmin' value='1' />admin</label>";
	}
	else
	{
		echo "<input name='new_isadmin' type='hidden' value='0' />";
	}
?>
	<input name="new_registertime" type="hidden" value="<?php echo time(); ?>" />
	<input class="btn btn-primary" type="submit" name="submit_entry_register" value="Dodaj uporabnika" />
</form>


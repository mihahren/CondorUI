<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

$userManager = new UserManager();	//zacne nov ali nadaljuje obstojec session

if (($_SERVER['REQUEST_METHOD'] == "POST") && isset($_POST['submit_entry_register']))
{
	$new_username = "";
	$new_email = "";

	if ($userManager->checkUsernameExistance($_POST['new_username'])) // preveri, ce username ze obstaja
	{
		$new_username = $_POST['new_username'];
	}

	if ($userManager->checkEmailExistance($_POST['new_email'])) // preveri, ce email ze obstaja
	{
		$new_email = $_POST['new_email'];
	}

	$register_user = $userManager->inputNewUser($new_username, $_POST['new_password'], $new_email, $_POST['new_isadmin'], $_POST['new_registertime'], $_POST['new_activetime']);

	foreach ($register_user as $key => $value)
	{
		$_SESSION['custom_error']['register_user'][$key] = $value;
	}
}
?>

<div id='register_form' class="generic_box">
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<table style='border-collapse:collapse;'>
			<tr>
				<td colspan='2' style='text-align:center;'>Dodaj uporabnika</div></td>
			</tr>
			<tr>
				<td colspan='2'><div style='padding-bottom:5px; margin-bottom:5px; border-bottom:1px solid #8895a6;'></div></td>
			</tr>
			<tr>
				<td align="right">Username:</td>
				<td><input name="new_username" type="text" maxlength="100" size="25" /></td>
			</tr>
			<tr>
				<td align="right">Password:</td>
				<td><input name="new_password" type="password" maxlength="150" size="25" /></td>
			</tr>
			<tr>
				<td align="right">Email:</td>
				<td><input name="new_email" type="text" maxlength="100" size="25" /></td>
			</tr>
			<tr>
				<td align="right">Trial:</td>
				<td>
					<select name="new_activetime"> 
						<option value="<?php echo (1 * 24 * 60 * 60); ?>" selected="selected">1 day trial</option>
						<option value="<?php echo (15 * 24 * 60 * 60); ?>">15 day trial</option>
<?php 
						if ($_SESSION['access'] == "admin")
						{
							echo "<option value='0'>neomejeno</option>";
						}
?>
					</select>
				</td>
			</tr>
<?php 
			if ($_SESSION['access'] == "admin")
			{
?>
			<tr>
				<td align="right">Je admin:</td>
				<td><input type="checkbox" name="new_isadmin" value="1"></td>
			</tr>
<?php 
			}
			else
			{
				echo "<input name='new_isadmin' type='hidden' value='0' />";
			}
?>
		</table>
		<input name="new_registertime" type="hidden" value="<?php echo time(); ?>" />
		<div style='padding-bottom:5px; margin-bottom:10px; border-bottom:1px solid #8895a6;'></div>
		<input name="submit_entry_register" type="submit" value="Dodaj uporabnika" />
	</form>
</div>

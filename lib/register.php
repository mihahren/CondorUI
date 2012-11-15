<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

if (($_SERVER['REQUEST_METHOD'] == "POST") && (isset($_POST['new_username'])))
{
	if(!$userManager->inputNewUser($_POST['new_username'], $_POST['new_password'], $_POST['new_email'], $_POST['new_isadmin'], $_POST['new_registertime'], $_POST['new_activetime']))
	{
		$_SESSION['custom_error']['registration'] = "Prislo je do napake. Preveri vpisane podatke";
	}
	else
	{
		$_SESSION['custom_error']['registration'] = "Registracija uspela!";
	}
}
?>

<div class="generic_box">
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<table>
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
		<input name="submit_entry" type="submit" value="sumbit entry" />
	</form>
</div>

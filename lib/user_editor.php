<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

$userManager = new UserManager();	// zacne nov ali nadaljuje obstojec session

if (($_SERVER['REQUEST_METHOD'] == "POST") && isset($_POST['submit_entry_edit_user'])) // preveri, ce hocemo spreminjati podatke
{
	$new_username = "";
	$new_password = "";
	$new_email = "";

	if (!empty($_POST['edit_username'])) // preveri, cezelimo spremeniti username
	{
		$new_username = $_POST['edit_username'];
	}

	if (!empty($_POST['edit_password'])) // preveri, ce zelimo spremeniti password
	{
		$new_password = $_POST['edit_password'];
	}

	if (!empty($_POST['edit_email'])) // preveri, ce zelimo spremeniti email
	{
		$new_email = $_POST['edit_email'];
	}


	if (isset($_POST['select_user'])) // doloci, ali gre za admin uporabnika
	{
		$admin_user_edit = $userManager->editUserAdmin($_POST['select_user'], $new_username, $new_password, $new_email);

		foreach ($admin_user_edit as $key => $value)
		{
			$_SESSION['custom_error']['user_editing'][$key] = $value;
		}
	}
	else // ali pa navadnega
	{
		$user_edit = $userManager->editUser($_POST['current_username'], $_POST['current_password'], $new_username, $new_password, $new_email);

		foreach ($user_edit as $key => $value)
		{
			$_SESSION['custom_error']['user_editing'][$key] = $value;
		}	
	}
}

if (($_SERVER['REQUEST_METHOD'] == "POST") && isset($_POST['delete_entry_edit_user'])) // preveri, ce hocemo brisati uporabnika
{
	if ($_POST['select_user'] != $_SESSION['username']) // preveri, ce gre za trenutno logiranega uporabnika
	{
		if ($userManager->deleteUser($_POST['select_user']))
		{
			$_SESSION['custom_error']['user_delete'] = "Uspesno izbrisano.";
		}
		else
		{
			$_SESSION['custom_error']['user_delete'] = "Brisanje ni uspelo.";
		}
	}
	else
	{
		$_SESSION['custom_error']['user_delete'] = "Ne mores zbrisati samega sebe.";
	}
}
?>

<div id='edit_user_form' class="generic_box">
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<table style='border-collapse:collapse; width:364px'>
			<tr>
				<td colspan='2' style='text-align:center;'>Spremeni podatke uporabnika</div></td>
			</tr>
			<tr>
				<td colspan='2'><div style='padding-bottom:5px; margin-bottom:5px; border-bottom:1px solid #8895a6;'></div></td>
			</tr>
<?php
			if ($_SESSION['access'] == 'access') // ce je navadni uporabnik
			{
?>
				<tr>
					<td align='right'>Current Username:</td>
					<td><input name='current_username' type='text' maxlength='100' size='25' /></td>
				</tr>
				<tr>
					<td align='right'>Current Password:</td>
					<td><input name='current_password' type='password' maxlength='150' size='25' /></td>
				</tr>
<?php
			}
			elseif ($_SESSION['access'] == 'admin') // ce je admin uporabnik
			{
				$username_array = $userManager->getUserArray("SELECT username FROM users");
?>
				<tr>
					<td align='right'>Username:</td>
					<td>
						<select name='select_user'>
<?php
							foreach ($username_array as $value)
							{
								$userid_array = $userManager->getUserArray("SELECT userid FROM users WHERE username = '".$value."'");
								$isadmin_array = $userManager->getUserArray("SELECT isadmin FROM users WHERE username = '".$value."'");

								if ($isadmin_array[0] == 1) // preveri, ce je admin, in izpise ime rdece
									$admin_color = "style='color:red'";
								else
									$admin_color = "";

								echo "<option ".$admin_color." value='".$value."'>".$value." (".$userid_array[0].")</option>";
							}
?>						
						</select>
						<input name='delete_entry_edit_user' type='submit' value='Brisi uporabnika' />
					</td>
				</tr>
<?php
			}
?>
			<tr>
				<td colspan='2'><div style='padding-bottom:5px; margin-bottom:5px; border-bottom:1px solid #8895a6;'></div></td>
			</tr>
			<tr>
				<td align="right">New Username:</td>
				<td><input name="edit_username" type="text" maxlength="100" size="25" /></td>
			</tr>
			<tr>
				<td align="right" >New Password:</td>
				<td><input name="edit_password" type="password" maxlength="150" size="25" /></td>
			</tr>
			<tr>
				<td align="right">New Email:</td>
				<td><input name="edit_email" type="text" maxlength="100" size="25" /></td>
			</tr>
		</table>
		<div style='padding-bottom:5px; margin-bottom:10px; border-bottom:1px solid #8895a6;'></div>
		<input name="submit_entry_edit_user" type="submit" value="Spremeni podatke" />
	</form>
</div>

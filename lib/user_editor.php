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


	if (isset($_POST['select_user'])) // doloci, ali gre za admin uporabnika - podatki o uporabniku
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

<form class="generic_box" style="padding-top:5px" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
	<h1>Spremeni podatke uporabnika</h1>
	<div class="separator"></div>
<?php
	if ($_SESSION['access'] == 'access') // ce je navadni uporabnik
	{
?>
	<input class="input-block-level" name='current_username' type='text' maxlength='100' placeholder="Current Username"/>
	<input class="input-block-level" name='current_password' type='password' maxlength='150' placeholder="Current Password"/>
<?php
	}
	elseif ($_SESSION['access'] == 'admin') // ce je admin uporabnik
	{
		$username_array = $userManager->getUserArray("SELECT username FROM users");
?>
		<div class="row-fluid">
			<div class="span9">
				<select class="span12" name='select_user'>
<?php
					foreach ($username_array as $value)
					{
						$userid_array = $userManager->getUserArray("SELECT userid FROM users WHERE username = '".$value."'");
						$isadmin_array = $userManager->getUserArray("SELECT isadmin FROM users WHERE username = '".$value."'");

						if ($isadmin_array[0] == 1) // preveri, ce je admin, in izpise ime rdece
							$admin_color = "style='color:red;'";
						else
							$admin_color = "";

						echo "<option ".$admin_color." value='".$value."'>".$value." (".$userid_array[0].")</option>";
					}
?>					
				</select>
			</div>
			<div class="span3">
				<input class="btn btn-warning span12" name='delete_entry_edit_user' type='submit' value='Brisi' style="margin-bottom:10px" />
			</div>
		</div>
<?php
	}
?>
	<div class="separator"></div>
	<input class="input-block-level" name="edit_username" type="text" maxlength="100" placeholder="New Username" />
	<input class="input-block-level" name="edit_password" type="password" maxlength="150" placeholder="New Password" />
	<input class="input-block-level" name="edit_email" type="email" maxlength="100" placeholder="New Email" />
	<button class="btn btn-primary" type="submit" name="submit_entry_edit_user">Spremeni podatke</button>
</form>

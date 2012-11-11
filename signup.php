<?php
include_once "lib/functions.php";
include_once "lib/access_control.php";
include_once "lib/classes.php";

if (($_SERVER['REQUEST_METHOD'] == "POST") && !($_POST['new_username']=='' || $_POST['new_password']=='' || $_POST['new_email']==''))
{
	$userManager->inputNewUser($_POST['new_username'], $_POST['new_password'], $_POST['new_email'], $_POST['new_isadmin'], $_POST['new_registertime'], $_POST['new_activetime']);
}

switch ($_SESSION['access'])
{
case "access":

	header('Location: index.php');
	break;	

case "no_access":

	$_SESSION['custom_error']['index_login'] = "Napacni podatki ali pa je vas trial cas potekel!";
	break;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Signup</title>
		<link rel="stylesheet" type="text/css" href="css/global_css.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="js/global_jquery.js"></script>
	</head>
	<body>
		<!-- header, ki vsebuje glavo z login, logout menujem ter odsek za prikazovanje sporocil -->
		<?php include_once "lib/header.php";?>

		<!-- content panel, ki prikazuje glavni del aplikacije -->
		<div id="content_panel">
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
						<td align="right">Je Admin:</td>
						<td><input type="checkbox" name="new_isadmin" value="1"></td>
					</tr>
<?php 
					}
					else
					{
						echo "<input name='new_isadmin' type='hidden' value='0' />";
					}
?>
				<table>
				<input name="new_registertime" type="hidden" value="<?php echo time(); ?>" />
				<input name="submit_entry" type="submit" value="sumbit entry" />
			</form>
		</div>
		
		<!-- footer, ki vsebuje small print in error funkcijo -->
		<?php include_once "lib/footer.php";?>
	</body>
</html>

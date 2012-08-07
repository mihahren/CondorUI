<?php
include_once "functions.php";
include_once "access_control.php";

//default vrednosti
$_SESSION['menu_1'] = "advanced";
$_SESSION['menu_2'] = "status";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>index</title>
		<link rel="stylesheet" type="text/css" href="global_css.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
		<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script>
		<script type="text/javascript" src="global_jquery.js"></script>
	</head>
	<body>
		<!-- main panel, ki vsebuje glavo z login, logout menujem ter odsek za prikazovanje sporocil -->
		<div id="main_panel">
<?php
			if ($_SESSION['access'] == "access")
			{
?>
				<div id="logout_box" class="login_section">
					<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="logout_form">
						<input type="hidden" name="logout" value="logout" />
						<div id="logout_text">Welcome, <?php echo $_SESSION['username'];?></br>
						<span id="logout_button">Logout</span></div>
					</form>
				</div>
<?php
			}
			else
			{
?>			
				<div id="login_box" class="login_section">
					<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="login_form">
						<input id="username_input" type="text" name="username" size="8" value="username" onFocus="this.value=''"/>
						<input id="password_input" type="password" name="password" SIZE="8" value="password" onFocus="this.value=''"/><br />
						<span id="login_button">Login</span>
						<a href="razno\signup.php" id="signup_button"><span>Register</span></a>
					</form>
				</div>
<?php
			}
?>
			<div id="error_prompt"></div>
		</div>
		<!-- content panel, ki prikazuje glavni del aplikacije -->
		<div id="content_panel">
			<?php include "content_control.php";?>
		</div>
	</body>
</html>


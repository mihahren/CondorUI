<?php
include "php\access_control.php";
//$content = "default";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>index</title>
		<link rel="stylesheet" type="text/css" href="global_css.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
		<script type="text/javascript" src="global_jquery.js"></script>
	</head>
	<body>
		<div id="main_panel">
			<?php
			if ($access == "access")
			{
			?>
				<div id="logout_box" class="login_section">
					<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" id="logout_form">
						<input type="hidden" name="logout" value="logout" />
						<div id="logout_text">Welcome, <?php echo $username;?></br>
						<span id="logout_button">Logout</span></div>
					</form>
				</div>
			<?php
			}
			else
			{
			?>			
				<div id="login_box" class="login_section">
					<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" id="login_form">
						<input type="text" name="username" size="8" value="username" onFocus="this.value=''"/>
						<input type="password" name="password" SIZE="8" value="password" onFocus="this.value=''"/><br />
						<span id="login_button">Login</span>
						<a href="php\signup.php" id="signup_button"><span>Register</span></a>
					</form>
				</div>
			<?php
			}
			
			if ($access == "no_access")
			{
			?>
				<div id="login_prompt" style="background-color:#F05456;">Napacni podatki!</div>
			<?php
			}
			?>
		</div>
		<div id="content_panel">
		<?php
			switch ($access)
			{
			case "login":
				echo "Prosim vpisi svoje uporabnisko ime in geslo";
				break;
			case "no_access":
				echo "Prislo je do napake!";
				break;
			case "access":
				include "php\main_content.php";
				break;
			}
		?>
		</div>
	</body>
</html>


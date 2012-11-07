<div id="header">
	<div id="menu_wrapper" style="position: absolute;">
		<a href="index.php"><div class="header_button_wrapper" id="home_button">
			<span class="header_button_text">HOME</span>
		</div></a>
		<a href="basic.php"><div class="header_button_wrapper" id="basic_button">
			<span class="header_button_text">BASIC</span>
		</div></a>
		<a href="advanced.php"><div class="header_button_wrapper" id="advanced_button">
			<span class="header_button_text">ADVANCED</span>
		</div></a>
	</div>
<?php
	if ($_SESSION['access'] == "access")
	{
?>
		<div id="logout_box" class="login_section">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="logout_form" enctype="multipart/form-data">
				<input type="hidden" name="logout" value="logout" />
				<div id="logout_text">
					Welcome, <?php echo $_SESSION['username'];?><br />
					<span id="logout_button">Logout</span>
				</div>
			</form>
		</div>
<?php
	}
	else
	{
?>			
		<div id="login_box" class="login_section">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="login_form" enctype="multipart/form-data">
				<div id="input_section">
					<input id="username_input" type="text" name="username" size="8" value="username" onFocus="this.value=''" style="float: left;"/>
					<input id="password_input" type="password" name="password" SIZE="8" value="password" onFocus="this.value=''" style="float: left;"/>
				</div>
				<div id="login_text">
					<span id="login_button">Login</span>
					<a href="razno\signup.php" id="signup_button"><span>Register</span></a>
				</div>
			</form>
		</div>
<?php
	}
?>
	<div id="error_prompt"></div>
</div>

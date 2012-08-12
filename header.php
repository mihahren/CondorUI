<div id="header">
	<div id="menu_wrapper">
		<div class="menu_button_wrapper" id="basic_button">
			<a href="index.php">
			<img src="images/menu_button.png" />
			<span class="menu_button_text">HOME</span></a>
		</div>
		<div class="menu_button_wrapper" id="basic_button">
			<a href="basic.php">
			<img src="images/menu_button.png" />
			<span class="menu_button_text">BASIC</span></a>
		</div>
		<div class="menu_button_wrapper" id="advanced_button">
			<a href="advanced.php">
			<img src="images/menu_button.png" />
			<span class="menu_button_text">ADVANCED</span></a>
			
		</div>
	</div>
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
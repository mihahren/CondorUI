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
<?php
		// samo izpise, ce ima dostop admin
		if ($_SESSION['access'] == "admin")
		{
?>
			<a href="admin.php"><div class="header_button_wrapper" id="admin_button">
				<span class="header_button_text">ADMIN</span>
			</div></a>
<?php
		}
	echo "</div>";
	
	// logout obrazec
	if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
	{
?>
		<div id="logout_box" class="login_section">
			<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" id="logout_form" enctype="multipart/form-data">
				<input type="hidden" name="logout" value="logout" />
				<div id="logout_text">
					Welcome, <a id="profile_button" href="profile.php"><?php echo $_SESSION['username']; ?></a><br/>
					<span id="logout_button">Logout</span>
<?php 
						if (is_int($_SESSION['daysleft']))
						{
							echo "(".(int)($_SESSION['daysleft']/(24 * 60 * 60))." dni)";
						}
						else
						{
							echo "(".$_SESSION['daysleft'].")";						
						}
?>					
				</div>
			</form>
		</div>
<?php
	}
	// login obrazec
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
					<a href="signup.php" id="signup_button"><span>Register</span></a>
				</div>
			</form>
		</div>
<?php
	}
?>
	<div id="error_prompt"></div>
</div>

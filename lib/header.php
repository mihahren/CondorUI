<?php include_once "functions.php"; ?>

<div id="header">
	<div class="navbar navbar-static-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<div class="nav-collapse collapse">		
					<!-- navigacijski gumbi -->
					<ul class="nav">
<?php
						echo "<li ".echoActiveClassIfRequestMatches('index')."><a id='collapse_link_index' href='index.php'>CondorUI</a></li>";
						echo "<li ".echoActiveClassIfRequestMatches('tour')."><a id='collapse_link_tour' href='tour.php'>Predstavitev</a></li>";
						echo "<li ".echoActiveClassIfRequestMatches('links')."><a id='collapse_link_links' href='links.php'>Povezave</a></li>";
						echo "<li ".echoActiveClassIfRequestMatches('status')."><a id='collapse_link_status' href='status.php'>Stanje</a></li>";
						
						// izpise samo, ce ima dostop admin ali access
						if ($_SESSION['access'] == "admin" || $_SESSION['access'] == "access")
						{
							echo "<li ".echoActiveClassIfRequestMatches('control_panel')."><a id='collapse_link_control_panel' href='control_panel.php'>Nadzorna plošča</a></li>";
						}
						
						// izpise samo, ce ima dostop admin
						if ($_SESSION['access'] == "admin")
						{
							echo "<li ".echoActiveClassIfRequestMatches('admin')."><a id='collapse_link_admin' href='admin.php'>Admin</a></li>";
						}
						
					echo "</ul>";

					// logout obrazec
					if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
					{
?>
					<form id="logout_form" class="navbar-form pull-right" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
						<!-- vidno v desktop nacinu -->
						<p class="navbar-text pull-right visible-desktop">
							<a id="logout_button" class="navbar-link">ODJAVI</a>
						</p>
						<ul class="nav pull-right visible-desktop"><li class="divider-vertical"></li></ul>
						<p class="navbar-text pull-right visible-desktop">
							PRIJAVLJENI: <a id="profile_button" class="navbar-link" href="profile.php"> <?php echo $_SESSION['username']; ?></a> (<?php echo round($_SESSION['daysleft']/86400, 1); ?>)
						</p>
						<!-- vidno v tablet/mobile nacinu -->
						<div style="margin-right:-15px;margin-left:-15px;">
						   	<p class="navbar-text pull-right hidden-desktop" style="font-size:120%;">
								<a id="logout_button" class="navbar-link">ODJAVI</a>
							</p>
							<p class="navbar-text pull-left hidden-desktop" style="font-size:120%;">
								PRIJAVLJENI: <a id="profile_button" class="navbar-link" href="profile.php"> <?php echo $_SESSION['username']; ?></a> (<?php echo round($_SESSION['daysleft']/86400, 1); ?>)
							</p>
						</div>
						<input type="hidden" name="logout" value="logout" />
					</form>
<?php
					}
					// login obrazec
					else
					{
?>
					<!-- vidno v desktop nacinu -->
					<p class="navbar-text pull-right visible-desktop">
						<a class="register_button" href="signup.php">Registriraj</a>
					</p>
					<ul class="nav pull-right visible-desktop"><li class="divider-vertical"></li></ul>
					<form id="login_form_desktop" class="navbar-form pull-right visible-desktop" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
						<div class="input-prepend input-append">
							<input type="text" name="username" placeholder="Username" style="width:100px">
							<input type="password" name="password" placeholder="Password" style="width:100px">
							<button type="submit" class="btn">Prijavi</button>
						</div>
					</form>
					<!-- vidno v tablet/mobile nacinu -->
					<form id="login_form_mobile" class="navbar-form hidden-desktop" style="padding-top:5px;padding-bottom:10px;padding-left:0px" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
						<input type="text" name="username" placeholder="Username">
						<input type="password" name="password" placeholder="Password">
						<button type="submit" class="btn" style="width:220px">Prijavi</button>
					</form>
					<div class="btn hidden-desktop" style="margin-bottom:20px;margin-left:0%;width:197px">
						<a style="color:black;" href="signup.php">Registriraj</a>
					</div>
<?php
					}
?>
					<div id='error_prompt_desktop' style="display:none;z-index:5;position:relative"></div>
					<div id='logo'><img src="img/large_logo.png"></img></div>
				</div>
			</div>
		</div>
	</div>
	<div style="padding:10px;"></div>
	<div class="container"><div  style="margin-top:-20px;display:none;z-index:5;position:relative" id="error_prompt_mobile"></div></div>
</div>

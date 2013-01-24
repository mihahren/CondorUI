<?php include_once "functions.php"; ?>

<div id="header" class="container">
	<div class="navbar">
		<div class="navbar-inner"> 
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="index.php">CondorUI</a>
			<div class="nav-collapse collapse">		
				<ul class="nav">
<?php
					echo "<li ".echoActiveClassIfRequestMatches('index')."><a href='index.php'>Domov</a></li>";
					echo "<li ".echoActiveClassIfRequestMatches('advanced')."><a href='advanced.php'>Napredno</a></li>";
					echo "<li ".echoActiveClassIfRequestMatches('basic')."><a href='basic.php'>Akcelelogrami</a></li>";

					// izpise samo, ce ima dostop admin
					if ($_SESSION['access'] == "admin")
					{
						echo "<li ".echoActiveClassIfRequestMatches('admin')."><a href='admin.php'>Admin</a></li>";
					}
					
					echo "<li class='divider-vertical visible-desktop'></li>

				</ul>";	
				
				$alert_index = " active";
				//spremenljivka za ohranjanje statusa toggle opozorila gumba
				if($_SESSION['alert_popup'] == "default")
					$alert_index = "";
				else
					$alert_index = " active";
					
				echo "<button id='alert_button' class='btn".$alert_index."' data-toggle='button'>Opozorila</button>";
			
				// logout obrazec
				if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
				{
?>
				<form id="logout_form" class="navbar-form pull-right" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
					<!-- vidno v desktop nacinu -->
				    <p class="navbar-text pull-right visible-desktop">
				    	<a id="logout_button" class="navbar-link">odjavi</a>
				    </p>
				    <ul class="nav pull-right visible-desktop"><li class="divider-vertical"></li></ul>
				    <p class="navbar-text pull-right visible-desktop">
				    	Prijavljeni ste kot <a id="profile_button" class="navbar-link" href="profile.php"> <?php echo $_SESSION['username']; ?></a>
				    </p>
				    <!-- vidno v tablet/mobile nacinu -->
				   	<p class="navbar-text pull-right hidden-desktop">
				    	<a id="logout_button" class="navbar-link">odjavi</a>
				    </p>
				    <p class="navbar-text pull-left hidden-desktop">
				    	Prijavljeni ste kot <a id="profile_button" class="navbar-link" href="profile.php"> <?php echo $_SESSION['username']; ?></a>
				    </p>
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
			    	<a href="signup.php">Register</a>
			    </p>
				<ul class="nav pull-right visible-desktop"><li class="divider-vertical"></li></ul>
				<form id="login_form" class="navbar-form pull-right visible-desktop" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
					<div class="input-prepend input-append">
						<input type="text" name="username" placeholder="Username" style="width:100px">
						<input type="password" name="password" placeholder="Password" style="width:100px">
						<button type="submit" class="btn">Prijavi</button>
					</div>
				</form>
				<!-- vidno v tablet/mobile nacinu -->
				<form id="login_form" class="navbar-form hidden-desktop" style="padding:0px" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
					<input type="text" name="username" placeholder="Username">
					<input type="password" name="password" placeholder="Password">
					<button type="submit" class="btn" style="width:220px">Prijavi</button>
				</form>
				<div class="btn hidden-desktop" style="margin-bottom:20px;width:197px">
					<a style="color:black" href="signup.php">Register</a>
				</div>
<?php
				}
?>
			</div>
		</div>
	<div id="error_prompt_mobile"></div>
	<div style="position:relative"><div id='error_prompt_desktop'></div></div>
</div>

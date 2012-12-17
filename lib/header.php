<?php include_once "functions.php"; ?>

<div id="header" class="container">
	<div class="navbar">
		<div class="navbar-inner">
			<a class="brand" href="index.php">CondorUI</a>
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
?>
			</ul>
<?php
			// logout obrazec
			if ($_SESSION['access'] == "access" || $_SESSION['access'] == "admin")
			{
?>
			<form id="logout_form" class="navbar-form pull-right" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
		        <p class="navbar-text pull-right">
		        	<span id="logout_button">odjavi</span>
		        </p>
				<ul class="nav pull-right"><li class="divider-vertical"></li></ul>
		        <p class="navbar-text pull-right">
		        	Prijavljeni ste kot <a id="profile_button" class="navbar-link" href="profile.php"><?php echo $_SESSION['username']; ?></a>
		        </p>
				<input type="hidden" name="logout" value="logout" />
			</form>
<?php
			}
			// login obrazec
			else
			{
?>
	        <p class="navbar-text pull-right">
	        	<a href="signup.php">Register</a>
	        </p>
			<ul class="nav pull-right"><li class="divider-vertical"></li></ul>
			<form id="login_form" class="navbar-form pull-right" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
				<div class="input-prepend input-append">
					<input type="text" name="username" placeholder="Username" class="span2">
					<input type="password" name="password" placeholder="Password" class="span2">
					<button type="submit" class="btn">Prijavi</button>
				</div>
			</form>
<?php
			}
?>
		</div>
	</div>
	<div id="error_prompt"></div>
</div>

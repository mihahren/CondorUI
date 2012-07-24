<?php
include_once 'functions.php';

if (!isset($_POST['submit_entry'])) //check if anything was already submitted
{
?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Signup</title>
		</head>
		<body>
			<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
				<table>
				<tr>
					<td align="right">Username:</td>
					<td><input name="new_username" type="text" maxlength="100" size="25" /></td>
				</tr>
				<tr>
					<td align="right">Email:</td>
					<td><input name="new_email" type="text" maxlength="100" size="25" /></td>
				</tr>
				<tr>
					<td align="right">Password:</td>
					<td><input name="new_password" type="password" maxlength="150" size="25" /></td>
				</tr>
				<table>
				<input name="submit_entry" type="submit" value="sumbit entry" />
			</form>
		</body>
	</html>

<?php
	exit;
}
else //check if the entry was valid and put it into the database
{
	$database_link = dbConnect('condor_users');
	
	if ($_POST['new_username']=='' || $_POST['new_email']=='' || $_POST['new_password']=='') //check if any fields are blank
	{
		error('Error 1.\\nOne or more required fields were left blank.\\nPlease fill them in and try again.');
	}
	
	//Check for existing user with the new id
	$query = "SELECT COUNT(*) FROM users WHERE username = '$_POST[new_username]'";
	
	$result = mysql_query($query);
	
	if (!$result)
	{
		error('Error 2.\\nA database error occurred in processing your submission.\\nIf this error persists, please contact miha.hren88@gmail.com.');
	}
	
	if (mysql_result($result,0,0)>0)
	{
		error('Error 3.\\nA user already exists with your chosen username.\\nPlease try another.');
	}
	
	//insert into database
	$query = "INSERT INTO users VALUES (NULL,'$_POST[new_username]','$_POST[new_email]',PASSWORD('$_POST[new_password]'))";
	
	$result = mysql_query($query, $database_link);

	if (!$result)
	{
	error('Error 4.\\nA database error occurred in processing your submission.\\nIf this error persists, please contact miha.hren88@gmail.com.');
	}
	
	//Email the new password to the person.
	$message = "Good Day!
 
	Your personal account for the Project Web Site
	has been created! To log in, proceed to the
	following address:
 
	http://93.103.42.102/
 
	Your personal login ID and password are as follows:
 
	Username: $_POST[new_username]
	Password: $_POST[new_password]
 
	You aren't stuck with this password! You can change it at any time after you have logged in.
 
	If you have any problems, feel free to contact me at <miha.hren88@gmail.com>.
 
	-Miha Hren
	Your Site Webmaster";
 
	mail($_POST['new_email'],"Your Password for Your Website", $message, "From:Miha Hren <miha.hren88@gmail.com>");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Registration complete</title>
	</head>
	<body>
		Registration complete! Check your email to confirm!</br>
		Click <a href="..\index.php">here</a> to login!
	</body>
</html>
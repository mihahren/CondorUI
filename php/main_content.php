<?php
include_once "functions.php";
include_once "access_control.php";

if($_SERVER['REQUEST_METHOD'] == "POST")
{
	$_SESSION["podatek"] = $_POST["podatek"];
}

//glavni switch stavek za izbiro menuja
switch($_SESSION["podatek"])
{
case queue:

	condor_q($out);
	print_cmd($out);
	break;

case status:

	condor_status($out);
	print_cmd($out);
	break;

case submit:

	$database_link = dbConnect('condor_users');
	$login_id = $_SESSION['login_id'];

	if($_SERVER['REQUEST_METHOD'] == "POST")
	{	
		if (!empty($_FILES["file"]["tmp_name"]))	//preveri, ali je bil uploadan file in ga prenese v ustrezno mapo ter doda v bazo podatkov
		{
			if (!is_dir("../upload/".$login_id))
			{
				mkdir("../upload/".$login_id);
			}
			
			if (file_exists("../upload/".$login_id."/".$_FILES["file"]["name"]))
			{
				$_SESSION['custom_error'] = 'Error 10! File already exists.';
			}
			else
			{
				move_uploaded_file($_FILES["file"]["tmp_name"],"../upload/".$login_id."/".$_FILES["file"]["name"]);
				echo "File ".$_FILES["file"]["name"]."successfully uploaded.";
				
				$query="INSERT INTO files VALUES (NULL, $login_id, '".$_FILES["file"]["name"]."', '".$_FILES["file"]["type"]."', '".$_FILES["file"]["size"]."')";
				$result = mysql_query($query, $database_link);
			}
		}
		
		if (!empty($_POST["delete_file"]))	//preveri, ce je potrebno kaksno datoteko zbrisati
		{
			for ($i=0; $i<(count($_POST["delete_file"])); $i++)
			{
				$query = "SELECT * FROM files WHERE fileid=".$_POST["delete_file"][$i];
				$result = mysql_query($query, $database_link);
				
				unlink("../upload/".$login_id."/".mysql_result($result,0,'filename'));
				
				$query = "DELETE FROM files WHERE fileid=".$_POST["delete_file"][$i];
				$result = mysql_query($query, $database_link);		
			}
		}
		
		if (!empty($_POST["submit_file"]))	//preveri, ce je potrebno kaksen file submitat
		{
			$query = "SELECT * FROM files WHERE fileid=".$_POST["submit_file"];
			$result = mysql_query($query, $database_link);
			
			if(mysql_num_rows($result) == 0)
			{
				$_SESSION['custom_error'] = 'Error 11! This file does not exist anymore.';
			}
			else
			{
				condor_submit("../upload/".$login_id."/".mysql_result($result,0,'filename'), $out);
				$_SESSION['custom_error'] = $out;
			}
		}
		
		unset($_FILES["file"]);
		unset($_POST["submit_file"]);
		unset($_POST["delete_file"]);
	}

	//pregleda in izpise vse datoteke, ki ustrezajo dolocenemu uporabniku
	$query="SELECT * FROM files WHERE userid=$login_id";
	$result = mysql_query($query, $database_link);

	if (!$result)
	{
		$_SESSION['custom_error'] = 'Error 12.\\nA database error occurred while checking file details.\\nIf this error persists, please contact miha.hren88@gmail.com.';
	}
?>
	<form method="post" action="php/main_content.php" id="file_form" enctype="multipart/form-data">
	<table style=>
		<tr>
			<td>filename</td>
			<td>filetype</td>
			<td>filesize</td>
			<td>submit</td>
			<td>delete</td>
		</tr>
<?php
		for ($i=0; $i<(mysql_num_rows($result)); $i++)
		{
?>
			<tr>
				<td><?php echo mysql_result($result,$i,'filename'); ?></td>
				<td><?php echo mysql_result($result,$i,'filetype'); ?></td>
				<td><?php echo mysql_result($result,$i,'filesize'); ?></td>
				<td><input type="radio" name="submit_file" value="<?php echo mysql_result($result,$i,'fileid'); ?>" /></td>
				<td><input type="checkbox" name="delete_file[]" value="<?php echo mysql_result($result,$i,'fileid'); ?>" /></td>
			</tr>
<?php
		}
?>
	</table>
	<input type="file" name="file" id="file" />
	</form>
	<button id="confirm_submit">Submit</button><br />
<?php
	mysql_close($database_link);
	break;
}

//obdelava errorjev
if (!empty($_SESSION['custom_error']))
{
	echo "<div id='custom_error'>";
	print_cmd($_SESSION['custom_error']);
	echo "</div>";
}

//reset zacasnih pomoznih globalnih spremenljivk
if($_SERVER['REQUEST_METHOD'] != "POST")
{
	unset($_SESSION['custom_error']);
	unset($_SESSION["podatek"]);
}
?>
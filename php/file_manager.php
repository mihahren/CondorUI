<?php
include_once "functions.php";
include_once "access_control.php";

$database_link = dbConnect('condor_users');

$query="SELECT * FROM files WHERE userid=$login_id";

$result = mysql_query($query, $database_link);

if (!$result)
{
	error('Error 10.\\nA database error occurred while checking file details.\\nIf this error persists, please contact miha.hren88@gmail.com.');
}
?>
<table style=>
	<tr>
		<td>filename</td>
		<td>filetype</td>
		<td>filesize</td>
	</tr>
<?php
	for ($i=0; $i<(mysql_num_rows($result)); $i++)
	{
?>
		<tr>
			<td><?php echo mysql_result($result,$i,'filename'); ?></td>
			<td><?php echo mysql_result($result,$i,'filetype'); ?></td>
			<td><?php echo mysql_result($result,$i,'filesize'); ?></td>
		</tr>
<?php
	}
?>
</table>
<?php

/*if ((($_FILES["file"]["type"] == "image/gif")|| ($_FILES["file"]["type"] == "image/jpeg")|| ($_FILES["file"]["type"] == "image/pjpeg"))&& ($_FILES["file"]["size"] < 20000))
{
	if ($_FILES["file"]["error"] > 0)
	{
		echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
	}
	else
	{
		echo "Upload: " . $_FILES["file"]["name"] . "<br />";
		echo "Type: " . $_FILES["file"]["type"] . "<br />";
		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
		echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

		if (file_exists("upload/" . $_FILES["file"]["name"]))
		{
			echo $_FILES["file"]["name"] . " already exists. ";
		}
		else
		{
			move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $_FILES["file"]["name"]);
			echo "File ".$_FILES["file"]["name"]."successfully uploaded.";
		}
    }
}
else
{
	echo "Invalid file";
}*/

?>
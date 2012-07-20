<?php
include "php/functions.php";

$database_link = dbConnect('condor_users');

echo count($_POST['delete_file'])."<br />";

for ($i=0; $i<(count($_POST['delete_file'])); $i++)
{
	echo $_POST['delete_file'][$i]."<br />";
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>" id="file_form" enctype="multipart/form-data">
<table style=>
	<tr>
		<td>filename</td>
		<td>filetype</td>
		<td>filesize</td>
		<td>submit</td>
		<td>delete</td>
	</tr>
<?php
	for ($i=0; $i<10; $i++)
	{
?>
		<tr>
			<td><?php echo "filename-".$i; ?></td>
			<td><?php echo "filetype-".$i; ?></td>
			<td><?php echo "filesize-".$i; ?></td>
			<td><input type="radio" name="submit_file" value="<?php echo $i; ?>" /></td>
			<td><input type="checkbox" name="delete_file[]" value="<?php echo $i; ?>" /></td>
		</tr>
<?php
	}
?>
</table>
<input type="file" name="file" id="file" />
<input type="submit" value="submit" />
</form>
<?php

?>
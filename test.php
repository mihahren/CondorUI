<?php
include "functions.php";

$test = scandir('../downloads');
?>
<form method="post" id="file_form" enctype="multipart/form-data">
<table id="file_table">
	<tr>
		<td>filename</td>
		<td>filetype</td>
		<td>submit</td>
		<td>delete</td>
	</tr>
<?php
	for ($i=0; $i<count($test); $i++)
	{
		if($test[$i] != "." && $test[$i] != "..")
		{
		list($fileName,$fileType)=explodeFileName($test[$i]);
?>
			<tr>
				<td><?php echo $fileName; ?></td>
				<td><?php echo $fileType; ?></td>
				<td><input type="radio" name="submit_file" value="<?php echo $fileName; ?>" /></td>
				<td><input type="checkbox" name="delete_file[]" value="<?php echo $fileName; ?>" /></td>
			</tr>
<?php
		}
	}
?>
</table>
<input type="file" name="file[]" id="file" multiple/>
</form>
<button id="confirm_submit">Submit</button><br />
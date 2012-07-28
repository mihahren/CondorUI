<?php
include_once "functions.php";
include_once "access_control.php";
?>
<div id="input_box">
	<div class="button_wrapper" id="queue_button">
		<img src="images/menu_button.png" />
		<span class="button_text">Condor Queue</span>
	</div>
	<div class="button_wrapper" id="status_button">
		<img src="images/menu_button.png" />
		<span class="button_text">Condor Status</span>
	</div>
	<div class="button_wrapper" id="submit_button">
		<img src="images/menu_button.png" />
		<span class="button_text">Submit...</span>
	</div>
</div>
<div id="output_box">
<?php

	//switch stavek za izbiro podmenuja v advanced naèinu
	switch($_SESSION['menu_2'])
	{
	case "queue":

		condor_q($out);
		print_cmd($out);
		echo date(DATE_COOKIE);
		break;

	case "status":

		condor_status($out);
		print_cmd($out);
		echo date(DATE_COOKIE);
		break;

	case "submit":

		$database_link = dbConnect('condor_users');
		$login_id = $_SESSION['login_id'];

		if($_SERVER['REQUEST_METHOD'] == "POST")
		{	
			//preveri, ali je bil uploadan file in ga prenese v ustrezno mapo ter doda v bazo podatkov
			if (!empty($_FILES['file']['tmp_name'][0]))
			{
				if (!is_dir("upload/".$login_id))
				{
					mkdir("upload/".$login_id);
				}
				for ($i=0; $i<count($_FILES['file']['tmp_name']); $i++)
				{
					if (file_exists("upload/".$login_id."/".$_FILES['file']['name'][$i]))
					{
						$_SESSION['custom_error'][2] = 'Datoteka ze obstaja.';
					}
					else
					{
						move_uploaded_file($_FILES['file']['tmp_name'][$i],"upload/".$login_id."/".$_FILES['file']['name'][$i]);
						
						$query="INSERT INTO files VALUES (NULL, $login_id, '".$_FILES['file']['name'][$i]."', '".$_FILES['file']['type'][$i]."', '".$_FILES['file']['size'][$i]."')";
						$result = mysql_query($query, $database_link);
					}
				}
			}
			
			//preveri, ce je potrebno kaksno datoteko zbrisati
			if (!empty($_POST['delete_file']))
			{
				for ($i=0; $i<(count($_POST['delete_file'])); $i++)
				{
					$query = "SELECT * FROM files WHERE fileid=".$_POST['delete_file'][$i];
					$result = mysql_query($query, $database_link);
					
					unlink("upload/".$login_id."/".mysql_result($result,0,'filename'));
					
					$query = "DELETE FROM files WHERE fileid=".$_POST['delete_file'][$i];
					$result = mysql_query($query, $database_link);		
				}
			}
			
			//preveri, ce je potrebno kaksen file submitat
			if (!empty($_POST['submit_file']))
			{
				$query = "SELECT * FROM files WHERE fileid=".$_POST['submit_file'];
				$result = mysql_query($query, $database_link);
				
				if(mysql_num_rows($result) == 0)
				{
					$_SESSION['custom_error'][3] = 'Datoteka vec ne obstaja!';
				}
				else
				{
					condor_submit("upload/".$login_id."/".mysql_result($result,0,'filename'), $out);
					$_SESSION['custom_error'][4] = $out;
				}
			}
			
			unset($_FILES['file']);
			unset($_POST['submit_file']);
			unset($_POST['delete_file']);
		}

		//pregleda in izpise vse datoteke, ki ustrezajo dolocenemu uporabniku
		$query="SELECT * FROM files WHERE userid=$login_id";
		$result = mysql_query($query, $database_link);

		if (!$result)
		{
			$_SESSION['custom_error'][5] = 'Napaka na podatkovni bazi.';
		}
?>
		<form method="post" id="file_form" enctype="multipart/form-data">
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
		<input type="file" name="file[]" id="file" multiple/>
		</form>
		<button id="confirm_submit">Submit</button><br />
<?php
		mysql_close($database_link);
		echo date(DATE_COOKIE);
		break;
	}
?>
</div>
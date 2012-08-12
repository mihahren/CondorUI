<div id="footer">
<?php
	//obdelava errorjev
	if (!empty($_SESSION['custom_error']))
	{
		echo "<div id='custom_error'>";
		for ($i=0; $i<=10; $i++)
		{
			if (!empty($_SESSION['custom_error'][$i]))
			{
				if (is_array($_SESSION['custom_error'][$i]))
				{
					echo "<p>";
					for ($j=1; $j<(count($_SESSION['custom_error'][$i])); $j++)
					{
						echo $_SESSION['custom_error'][$i][$j]."<br />";
					}
					echo "</p>";
				}
				else
				{
					echo "<p>".$_SESSION['custom_error'][$i]."</p>";
				}
			}
		}
		echo "</div>";
	}

	//reset pomoznih globalnih spremenljivk
	if($_SERVER['REQUEST_METHOD'] != "POST")
	{
		unset($_SESSION['custom_error']);
	}
?>
</div>
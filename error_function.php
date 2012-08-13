<?php
include_once "functions.php";
include_once "access_control.php";

//obdelava errorjev
if (!empty($_SESSION['custom_error']))
{
	echo "<div id='custom_error'>";
	foreach ($_SESSION['custom_error'] as $value1)
	{
		if (is_array($value1))
		{
			foreach ($value1 as $value2)
			{
				if (is_array($value2))
				{
					foreach ($value2 as $value3)
					{
						if (!empty($value3))
							echo $value3."<br />";
					}
				}
				else
				{
					if (!empty($value2))
						echo $value2."<br />";
				}
			}
		}
		else
		{
			if (!empty($value1))
				echo $value1."<br />";
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
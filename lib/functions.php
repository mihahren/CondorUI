<?php

//definirane condor funkcije
function condor_status(&$output)
{
	exec('condor_status 2>&1', $output);
}

function condor_q(&$output)
{
	exec('condor_q 2>&1', $output);
}

function condor_submit($input, &$output)
{
	exec('condor_submit '.$input.' 2>&1', $output);
}

function condor_remove($input, &$output)
{
	exec('condor_rm '.$input.' 2>&1', $output);
}

//sprinta izpise cmd konzole vrstico za vrstico
function print_cmd($input)
{
	if (is_array($input))
	{
		echo "<table>";
			foreach ($input as $value)
			{
				echo "<tr><td><pre style='display:inline;'>".$value."</pre></td></tr>";
			}
		echo "</table>";
	}
	else
	{
		echo $input;
	}
}

//razbije celotno ime datoteke v stringu na ime in tip
function explodeFileName($string)
{
	$tempStrArray = explode(".",$string);
	
	for ($i=0; $i<(count($tempStrArray)-1); $i++)
	{
		$fileName = $fileName.$tempStrArray[$i];
	}
	
	$fileType = $tempStrArray[(count($tempStrArray)-1)];
	
	if ($fileName == "")
	{
		$fileName = $fileType;
		$fileType="";
	}
	
	return array($fileName, $fileType);
}

//vzame string, zbrise whitespace ter doda posamezne besede v array
function splitString($string, $char = " ")
{
	$strArray = str_split($string);
	$whitespace = false;
	$word = false;
	$iter = 0;
	$newArray = array();
	
	for ($i=0; $i<count($strArray); $i++)
	{
		if ($strArray[$i] == $char)
		{
			$whitespace = true;
		}
		else
		{
			$newArray[$iter] .= $strArray[$i];
			$word = true;
			$whitespace = false;
		}
		
		if ($whitespace && $word)
		{
			$word = false;
			$iter++;
		}
	}
	
	return $newArray;
}

//spremeni 3 ali manj stopenjski array v 1 stopnjo, zbrise prazne vnose
function flattenArray($inputArray)
{
	$flattenedArray = array();
	$iter = 0;
	
	foreach ($inputArray as $value1)
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
							$flattenedArray[$iter] = $value3;
							$iter++;
					}
				}
				else
				{
					if (!empty($value2))
						$flattenedArray[$iter] = $value2;
						$iter++;
				}
			}
		}
		else
		{
			if (!empty($value1))
				$flattenedArray[$iter] = $value1;
				$iter++;
		}
	}
	
	return $flattenedArray;
}
?>

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

function condor_generic($input, &$output)
{
	exec($input.' 2>&1', $output);
}

//sprinta izpise cmd konzole vrstico za vrstico
function print_cmd($input)
{
	if (is_array($input))
	{
		echo "<pre><table id='default_condor_table'>";
			foreach ($input as $value)
			{
				echo "<tr><td>".$value."</td></tr>";
			}
		echo "</table></pre>";
	}
	else
	{
		echo $input;
	}
}

//convertira array string v enotni string
function convertString($input)
{
	$output = "";

	if (is_array($input))
	{
		foreach ($input as $value)
		{
			$output .= $value;
		}
	}
	else
	{
		$output = $input;
	}
	
	return $output;
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

//spremeni vecstopenjski array v 1 stopnjo, zbrise prazne vnose
function flattenArray($inputArray)
{
	$flattenedArray = array();
	$iter = 0;
	
	foreach ($inputArray as $value)
	{
		if (is_array($value))
		{
			$tempArray = flattenArray($value);
			
			foreach ($tempArray as $tempValue)
			{
				$flattenedArray[$iter] = $tempValue;
				$iter++;
			}
		}
		else
		{
			if (!empty($value))
			{
				$flattenedArray[$iter] = $value;
				$iter++;
			}
		}
	}
	
	return $flattenedArray;
}

//pokaze active class
function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");

    if ($current_file_name == $requestUri)
	{
        return "class='active'";
	}
}
?>

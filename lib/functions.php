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
	exec('condor_submit -n miha '.$input.' 2>&1', $output);
}

function condor_remove($input, &$output)
{
	exec('condor_rm '.$input.' 2>&1', $output);
}

// izpis condor queue funkcije z moznostjo izbrisa vnosov, ki pripadajo vnesenemu uporabniku
function condor_qAdvanced($usernameString)
{
	//sprozi globalni in user specific condor_q request
	exec('condor_q -global', $allOutput);

	exec('condor_q '.$usernameString.' -format %4d. ClusterId -format %-3d ProcId 2>&1', $idOutput);
	
	//shrani prvo vrstico ID condor_q requesta ter naredi iterator zanj
	$idOutputExplode = splitString($idOutput[0], " ");
	$iter = 0;
	
	//izpisi vse, preveri kje se ID ujema pri obeh condor_q requestih - tam doda gumb za brisanje
	echo "<table id='delete_submited_table'>
		<tr>
			<td style='text-align:center;'><pre style='display:inline;'>SUBMITED FILES</pre></td>
			<td style='text-align:center;'><pre style='display:inline;'>DEL</pre></td>
		</tr>";
		if (empty($allOutput))
		{
			echo "<tr><td><pre style='display:inline;'>All queues are empty</pre></td><td></td></tr>";
		}

		foreach ($allOutput as $value)
		{
			echo "<tr>";
				if($value != NULL)
				{
					echo "<td style='border:0px;'><pre style='display:inline;'>".$value."</pre></td>";
					$allOutputExplode = explode(" ", ltrim($value, " "));
	
					echo "<td style='text-align:center;border-top:0px;border-bottom:0px;'>";
					if($allOutputExplode[0] === $idOutputExplode[$iter])
					{
						echo "<input type='checkbox' class='submit_delete_checkbox' name='delete_submited_file[]' value='".$allOutputExplode[0]."' />";
						$iter++;
					}
					echo "</td>";
				}
			echo "</tr>";
		}

		echo "<tr>
			<td style='text-align:right;'><pre style='display:inline;'>Select All:</pre></td>
			<td style='text-align:center;'><input type='checkbox' name='select_all_submited' value='false' class='select_all_submited'/></td>
		</tr>
	</table>";
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
?>

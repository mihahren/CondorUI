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

// funkcija za risanje debelih crt
function imagelinethick($image, $x1, $y1, $x2, $y2, $color, $thick = 1)
{
    /* this way it works well only for orthogonal lines
    imagesetthickness($image, $thick);
    return imageline($image, $x1, $y1, $x2, $y2, $color);
    */
    if ($thick == 1) {
        return imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    $t = $thick / 2 - 0.5;
    if ($x1 == $x2 || $y1 == $y2) {
        return imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color);
    }
    $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
    $a = $t / sqrt(1 + pow($k, 2));
    $points = array(
        round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
        round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
        round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
        round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
    );
    imagefilledpolygon($image, $points, 4, $color);
    return imagepolygon($image, $points, 4, $color);
}

// izrise line graph na podlagi array-a
function drawLineGraph($array, $file_name, $naslov="test graph", $margin=25, $space_width=20, $line_spacing=20, $horizontal_lines=10, $axis_frequency=1, $aa_level=1)
{
	//dinamicne vrednosti
	$margin = $margin*$aa_level;
	$space_width = $space_width*$aa_level;
	$line_spacing = $line_spacing*$aa_level;
	$font_size = 11*$aa_level;

	// izracunane dimenzije
	$num_items = count($array);
	$max_value = max($array);
	$image_width = 2*$margin + $num_items*$space_width;
	$image_height = 2.5*$margin + $horizontal_lines*$line_spacing;
	$aaimage_width = $image_width/$aa_level;
	$aaimage_height = $image_height/$aa_level;
	$ratio = ($horizontal_lines*$line_spacing)/$max_value;
	$array_keys = array_keys($array);
	$iter = 1;
	
	// image canvas
	$image = imagecreatetruecolor($image_width,$image_height);
	$aaimage = imagecreatetruecolor($aaimage_width,$aaimage_height);

	// barve
	$background_color = imagecolorallocate($image,255,255,255);
	$graph_color = imagecolorallocate($image,200,35,35);
	$line_color = imagecolorallocate($image,220,220,220);
	$string_color = imagecolorallocate($image,0,0,0);
	
	// narisi background
	imagefilledrectangle($image,0,0,$image_width,$image_height,$background_color);

	// napisi naslov
	imagettftext($image,$font_size*1.5,0,$margin,$font_size*1.8,$string_color,$_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf",$naslov);

	// narisi horizontalne crte
	for($i=0;$i<=$horizontal_lines;$i++)
	{
		$x1 = $margin;
		$y1 = $y2 = $image_height - $margin - $i*$line_spacing;
		$x2 = $image_width - $margin;
		imagelinethick($image,$x1,$y1,$x2,$y2,$line_color,2*$aa_level);
		$value = intval($i*$line_spacing/$ratio);
		imagettftext($image, $font_size, 0, $x1-$font_size/3-strlen((string)$value)*$font_size, $y1+$font_size/2, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $value);	// y-os text
	}

	// narisi graf
	for($i=0;$i<($num_items);$i++)
	{
		$current_value = $array[$array_keys[$i]]*$ratio;
		$next_value = $array[$array_keys[$i+1]]*$ratio;
		$x1 = $margin + $space_width/2 + $i*$space_width;
		$y1 = $image_height - $margin - $current_value;
		$x2 = $x1 + $space_width;
		$y2 = $image_height - $margin - $next_value;
		if ($i<($num_items-1))
			imagelinethick($image,$x1,$y1,$x2,$y2,$graph_color,3*$aa_level);
		if ($iter>=$axis_frequency)
		{
			imagettftext($image, $font_size, 0, $x1-$font_size*strlen($array_keys[$i])/6, $image_height-$font_size/2, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $array_keys[$i]);	// x-os text
			$iter = 1;
		}
		else
		{
			$iter++;
		}
	}

	// izrisi sliko
	imagecopyresampled($aaimage,$image,0,0,0,0,$aaimage_width,$aaimage_height,$image_width,$image_height); 
    imagepng($aaimage, "images/".$file_name);
	imagedestroy($image);
	imagedestroy($aaimage);
}

// izrise bar graph na podlagi array-a
function drawBarGraph($array, $file_name, $naslov="test graph", $margin=30, $bar_width=25, $space_width=15, $line_spacing=15, $horizontal_lines=10, $aa_level=1)
{	
	//dinamicne vrednosti
	$margin = $margin*$aa_level;
	$bar_width = $bar_width*$aa_level;
	$space_width = $space_width*$aa_level;
	$line_spacing = $line_spacing*$aa_level;
	$font_size = 11*$aa_level;

	// izracunane dimenzije
	$num_items = count($array);
	$max_value = max($array);
	$image_width = 1.5*$margin + $space_width + $num_items*($space_width+$bar_width);
	$image_height = 2.5*$margin + $horizontal_lines*$line_spacing;
	$aaimage_width = $image_width/$aa_level;
	$aaimage_height = $image_height/$aa_level;
	$ratio = ($horizontal_lines*$line_spacing)/$max_value;
	
	// image canvas
	$image = imagecreatetruecolor($image_width,$image_height);
	$aaimage = imagecreatetruecolor($aaimage_width,$aaimage_height);

	// barve
	$background_color = imagecolorallocate($image,255,255,255);
	$bar_color = imagecolorallocate($image,200,35,35);
	$line_color = imagecolorallocate($image,220,220,220);
	$string_color = imagecolorallocate($image,0,0,0);
	
	// narisi background
	imagefilledrectangle($image,0,0,$image_width,$image_height,$background_color);

	// napisi naslov
	imagettftext($image, $font_size*1.5, 0, $margin, $font_size*1.8, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $naslov);

	// narisi horizontalne crte
	for($i=0;$i<=$horizontal_lines;$i++)
	{
		$x1 = $margin;
		$y1 = $y2 = $image_height - $margin - $i*$line_spacing;
		$x2 = $image_width - $margin/2;
		imagelinethick($image,$x1,$y1,$x2,$y2,$line_color,2*$aa_level);
		$value = intval($i*$line_spacing/$ratio);
		imagettftext($image, $font_size, 0, $x1-$font_size/3-strlen((string)$value)*$font_size, $y1+$font_size/2, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $value);	// y-os text
	}

	// narisi bare
	for($i=0;$i<$num_items;$i++)
	{
		list($key,$value)=each($array);
		$temp_value = $value*$ratio;
		$x1 = $margin + $space_width + $i*($bar_width+$space_width);
		$y1 = $image_height - $margin;
		$x2 = $x1 + $bar_width;
		$y2 = $y1 - $temp_value;
		imagefilledrectangle($image,$x1,$y1,$x2,$y2,$bar_color);
		imagettftext($image, $font_size, 0, $x1+$bar_width*0.05, $y2-5*$aa_level, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $value);	// vrednosti
		imagettftext($image, $font_size, 0, $x1+$bar_width/2-strlen($key)*$font_size/3, $image_height-$font_size/2, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $key);	// x-os text
	}

	// izrisi sliko
	imagecopyresampled($aaimage,$image,0,0,0,0,$aaimage_width,$aaimage_height,$image_width,$image_height); 
    imagepng($aaimage, "images/".$file_name);
	imagedestroy($image);
	imagedestroy($aaimage);
}

// izrise pie chart na podlagi array-a
function drawPieChart($array, $file_name, $naslov="test graph", $image_width=450, $image_height=250, $aa_level=2)
{
	// izracunane dimenzije
	$num_items = count($array);
	$max_value = max($array);
	$ratio = 360/(array_sum($array));
	$aaimage_width = $image_width;
	$aaimage_height = $image_height;
	$image_width = $image_width*$aa_level;
	$image_height = $image_height*$aa_level;
	$font_size = 10*$aa_level;
	$radius = $image_height-$font_size*6;
	$start = -90;
	$end = -90;
	
	// image canvas
	$image = imagecreatetruecolor($image_width,$image_height);
	$aaimage = imagecreatetruecolor($aaimage_width,$aaimage_height);

	// barve
	$background_color = imagecolorallocate($image,255,255,255);
	$string_color = imagecolorallocate($image,0,0,0);
	
	// narisi background
	imagefilledrectangle($image, 0, 0, $image_width, $image_height,$background_color);

	// napisi naslov
	imagettftext($image, $font_size*1.5, 0, $font_size*1.5, $font_size*3, $string_color,$_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $naslov);

	// narisi graf in legendo
	for($i=0;$i<$num_items;$i++)
	{
		list($key,$value)=each($array);

		if ($value > 0)
		{
			// graf
			$end = $start + $value*$ratio;
			$arc_color = imagecolorallocate($image,rand(0,255),rand(0,255),$i*255/$num_items);
			imagefilledarc($image, $font_size*1.5+$radius/2, $font_size*5+$radius/2, $radius, $radius, $start, $end, $arc_color, IMG_ARC_PIE);
			$start = $end;

			// legenda
			$x_leg = $font_size*4+$radius;
			$y_leg = $font_size*5+$i*(30*$aa_level);
			imagefilledrectangle($image, $x_leg, $y_leg, $x_leg+20*$aa_level, $y_leg+20*$aa_level, $arc_color);
			imagettftext($image, $font_size, 0, $x_leg+25*$aa_level, $y_leg+10*$aa_level+$font_size/2, $string_color, $_SERVER["DOCUMENT_ROOT"]."/CondorUI/fonts/arial.ttf", $key." [".round(100*$value/array_sum($array), 2)."%]");
		}
	}

	// izrisi sliko
	imagecopyresampled($aaimage,$image,0,0,0,0,$aaimage_width,$aaimage_height,$image_width,$image_height); 
    imagepng($aaimage, "images/".$file_name);
	imagedestroy($image);
	imagedestroy($aaimage);
}
?>

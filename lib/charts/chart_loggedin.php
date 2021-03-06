<?php
include_once "../classes.php";
include_once "pChart2.1.3/class/pDraw.class.php";
include_once "pChart2.1.3/class/pImage.class.php";
include_once "pChart2.1.3/class/pData.class.php";

$stats_viewer = new StatsTracker();

// array za graf "Stevilo uporabnikov zadnjih 24 ur"
$array_loggedin = array();
$array_loggedin_abscissa = array();
$current_time_24h = time();
$precision = 1800;

for($i=0;$i<(86400/$precision);$i++)
{
$min_time = $current_time_24h - 86400 + $i*$precision;
$max_time = $min_time + $precision;
$users_loggedin = $stats_viewer->getStatsRows("SELECT COUNT(DISTINCT userid) FROM stats WHERE date_visited>=".$min_time." AND date_visited<=".$max_time);
$this_time = intval(($min_time+$max_time)/2);
if($users_loggedin <= 0){$users_loggedin = 0;}else{$users_loggedin = $users_loggedin-1;}
$array_loggedin[$i] = intval($users_loggedin);
$array_loggedin_abscissa[$i] = date('H:i',$this_time);
}

//ustvari data class
$usersData = new pData();
$usersData->addPoints($array_loggedin,"array_loggedin");
$usersData->addPoints($array_loggedin_abscissa,"array_loggedin_abscissa");
$usersData->setPalette("array_loggedin",array("R" => 0,"G" => 64, "B" =>200, "Alpha" => 100));
$usersData->setAbscissa("array_loggedin_abscissa");
$usersData->setSerieDescription("array_loggedin_abscissa","Cas");
$usersData->setSerieOnAxis("array_loggedin", 0);

//ustvari image class
$myImage = new pImage(1500, 600, $usersData);
$myImage->setGraphArea(30,40, 1470,570);
$myImage->setFontProperties(array("FontName" => "lib/charts/pChart2.1.3/fonts/GeosansLight.ttf", "FontSize" => 11));
$myImage->drawFilledRectangle(0,0,1500,600,array("R"=>245,"G"=>245,"B"=>245,"Alpha"=>100));
//$myImage->drawGradientArea(0,0,700,250,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myImage->drawRectangle(0,0,699,249,array("R"=>200,"G"=>200,"B"=>200));
//$myImage->drawLegend(320,100,array("R" => 220, "G" => 220, "B" => 220,"FontR" => 0, "FontG" => 64, "FontB" => 255,"BorderR" => 80, "BorderG" => 80, "BorderB" => 80,"FontSize" => 12, "Family" => LEGEND_FAMILY_CIRCLE));
$myImage->drawText(30,30, "Stevilo prijavljenih uporabnikov",array("R" => 0, "G" => 0, "B" => 0, "FontSize" => 20));
$myImage->drawScale(array("GridR"=>180,"GridG"=>180,"GridB"=>180,"CycleBackground"=>TRUE,"LabelSkip"=>1,"DrawSubTicks"=>TRUE));
$myImage->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myImage->drawSplineChart();
$myImage->setShadow(FALSE);

//shrani sliko
$myImage->Render("lib/charts/chart_loggedin.png");
?>

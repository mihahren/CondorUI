<?php
include_once "../classes.php";
include_once "pChart2.1.3/class/pDraw.class.php";
include_once "pChart2.1.3/class/pImage.class.php";
include_once "pChart2.1.3/class/pData.class.php";

$stats_viewer = new StatsTracker();

// array za graf "Stevilo uporabnikov zadnjih 24 ur"
$array_last_24h = array();
$array_last_24h_abscissa = array();
$current_time_24h = time();
$precision = 1800;

for($i=0;$i<(86400/$precision);$i++)
{
$min_time = $current_time_24h - 86400 + $i*$precision;
$max_time = $min_time + $precision;
$users_24h = $stats_viewer->getStatsRows("SELECT COUNT(DISTINCT ip) FROM stats WHERE date_visited>=".$min_time." AND date_visited<=".$max_time);
$this_time = intval(($min_time+$max_time)/2);
$array_last_24h[$i] = intval($users_24h);
$array_last_24h_abscissa[$i] = date('H:i',$this_time);
}

//ustvari data class
$usersData = new pData();
$usersData->addPoints($array_last_24h,"array_last_24h");
$usersData->addPoints($array_last_24h_abscissa,"array_last_24h_abscissa");
$usersData->setPalette("array_last_24h",array("R" => 0, "G" => 64, "B" => 200, "Alpha" => 100));
$usersData->setAbscissa("array_last_24h_abscissa");
$usersData->setSerieDescription("array_last_24h_abscissa","Cas");
$usersData->setSerieOnAxis("array_last_24h", 0);

//ustvari image class
$myImage = new pImage(735, 266, $usersData);
$myImage->setGraphArea(30,40, 705,236);
$myImage->setFontProperties(array("FontName" => "lib/charts/pChart2.1.3/fonts/GeosansLight.ttf", "FontSize" => 11));
$myImage->drawFilledRectangle(0,0,735,266,array("R"=>245,"G"=>245,"B"=>245,"Alpha"=>100));
//$myImage->drawGradientArea(0,0,700,250,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myImage->drawRectangle(0,0,699,249,array("R"=>200,"G"=>200,"B"=>200));
//$myImage->drawLegend(320,100,array("R" => 220, "G" => 220, "B" => 220,"FontR" => 0, "FontG" => 64, "FontB" => 255,"BorderR" => 80, "BorderG" => 80, "BorderB" => 80,"FontSize" => 12, "Family" => LEGEND_FAMILY_CIRCLE));
$myImage->drawText(30,30, "Stevilo uporabnikov v zadnjih 24 urah",array("R" => 0, "G" => 0, "B" => 0, "FontSize" => 20));
$myImage->drawScale(array("GridR"=>180,"GridG"=>180,"GridB"=>180,"CycleBackground"=>TRUE,"LabelSkip"=>4,"DrawSubTicks"=>TRUE));
$myImage->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myImage->drawSplineChart();
$myImage->setShadow(FALSE);

//shrani sliko
$myImage->Render("lib/charts/chart_24h.png");
?>



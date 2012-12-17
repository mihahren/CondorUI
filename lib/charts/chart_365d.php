<?php
include_once "../classes.php";
include_once "pChart2.1.3/class/pDraw.class.php";
include_once "pChart2.1.3/class/pImage.class.php";
include_once "pChart2.1.3/class/pData.class.php";

$stats_viewer = new StatsTracker();

// array za graf "Stevilo uporabnikov v zadnjem letu"
$array_last_365d = array();
$current_time_365d = time();
$fixed_time_365d = time();

for($i=0;$i<12;$i++)
{
	while(date('M',$current_time_365d) == date('M',$fixed_time_365d))
	{
		$current_time_365d -= 86400;
		if (date('M',$current_time_365d) != date('M',$fixed_time_365d))
		{
			$users_365d = $stats_viewer->getStatsRows("SELECT COUNT(DISTINCT ip) FROM stats WHERE date_visited>=".$current_time_365d." AND date_visited<=".$fixed_time_365d);
			$array_last_365d[$i] = intval($users_365d);
			$array_last_365d_abscissa[$i] = date('M',$fixed_time_365d);
		}
	}

	$fixed_time_365d = $current_time_365d;
}

$array_last_365d = array_reverse($array_last_365d);
$array_last_365d_abscissa = array_reverse($array_last_365d_abscissa);

//ustvari data class
$usersData = new pData();
$usersData->addPoints($array_last_365d,"array_last_365d");
$usersData->addPoints($array_last_365d_abscissa,"array_last_365d_abscissa");
$usersData->setPalette("array_last_365d",array("R" => 0, "G" => 64, "B" => 200, "Alpha" => 100));
$usersData->setAbscissa("array_last_365d_abscissa");
$usersData->setSerieDescription("array_last_365d_abscissa","Meseci");
$usersData->setSerieOnAxis("array_last_365d", 0);

//ustvari image class
$myImage = new pImage(735, 241, $usersData);
$myImage->setGraphArea(30,40, 705,220);
$myImage->setFontProperties(array("FontName" => "lib/charts/pChart2.1.3/fonts/GeosansLight.ttf", "FontSize" => 11));
$myImage->drawFilledRectangle(0,0,735,241,array("R"=>245,"G"=>245,"B"=>245,"Alpha"=>100));
//$myImage->drawGradientArea(0,0,700,250,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myImage->drawRectangle(0,0,699,249,array("R"=>200,"G"=>200,"B"=>200));
//$myImage->drawLegend(320,100,array("R" => 220, "G" => 220, "B" => 220,"FontR" => 0, "FontG" => 64, "FontB" => 255,"BorderR" => 80, "BorderG" => 80, "BorderB" => 80,"FontSize" => 12, "Family" => LEGEND_FAMILY_CIRCLE));
$myImage->drawText(30,30, "Stevilo uporabnikov v zadnjem letu",array("R" => 0, "G" => 0, "B" => 0, "FontSize" => 20));
$myImage->drawScale(array("GridR"=>180,"GridG"=>180,"GridB"=>180,"CycleBackground"=>TRUE,"LabelSkip"=>0,"DrawSubTicks"=>TRUE));
$myImage->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myImage->drawBarChart();
$myImage->setShadow(FALSE);

//shrani sliko
$myImage->Render("lib/charts/chart_365d.png");
?>



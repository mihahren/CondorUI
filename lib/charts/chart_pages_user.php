<?php
include_once "../classes.php";
include_once "pChart2.1.3/class/pData.class.php";
include_once "pChart2.1.3/class/pDraw.class.php";
include_once "pChart2.1.3/class/pPie.class.php";
include_once "pChart2.1.3/class/pImage.class.php";

$stats_viewer = new StatsTracker();

// array za graf "Najbolj obiskane strani uporabnika"
$page_index_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/index.php' AND userid=".$_SESSION['login_id']);
$page_tour_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/tour.php' AND userid=".$_SESSION['login_id']);
$page_links_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/links.php' AND userid=".$_SESSION['login_id']);
$page_status_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/status.php' AND userid=".$_SESSION['login_id']);
$page_control_panel_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/control_panel.php' AND userid=".$_SESSION['login_id']);
$page_admin_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/admin.php' AND userid=".$_SESSION['login_id']);
$page_profile_user = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/profile.php' AND userid=".$_SESSION['login_id']);

$array_page_user = array($page_index_user, $page_tour_user, $page_links_user, $page_status_user, $page_control_panel_user, $page_admin_user, $page_profile_user);
$array_page_user_abscissa = array('Domov','Predstavitev','Povezave','Status','Nadzorna plosca','Admin','Profil');
//ustvari data class
$usersData = new pData();
$usersData->addPoints($array_page_user,"array_page_user");
$usersData->addPoints($array_page_user_abscissa,"array_page_user_abscissa");
$usersData->setPalette("array_page_user",array("R" => 0, "G" => 64, "B" => 200, "Alpha" => 100));
$usersData->setAbscissa("array_page_user_abscissa");
$usersData->setSerieDescription("array_page_user_abscissa","Strani");
$usersData->setSerieOnAxis("array_page_user", 0);

//ustvari image class
$myImage = new pImage(530, 260, $usersData);
$myImage->setGraphArea(30,40, 500,236);
$myImage->setFontProperties(array("FontName" => "lib/charts/pChart2.1.3/fonts/GeosansLight.ttf", "FontSize" => 11));
$myImage->drawFilledRectangle(0,0,530,260,array("R"=>245,"G"=>245,"B"=>245,"Alpha"=>100));
//$myImage->drawGradientArea(0,0,700,250,DIRECTION_VERTICAL,array("StartR"=>220,"StartG"=>220,"StartB"=>220,"EndR"=>255,"EndG"=>255,"EndB"=>255,"Alpha"=>100));
//$myImage->drawRectangle(0,0,699,249,array("R"=>200,"G"=>200,"B"=>200));
//$myImage->drawLegend(320,100,array("R" => 220, "G" => 220, "B" => 220,"FontR" => 0, "FontG" => 64, "FontB" => 255,"BorderR" => 80, "BorderG" => 80, "BorderB" => 80,"FontSize" => 12, "Family" => LEGEND_FAMILY_CIRCLE));
$myImage->drawText(30,30, "Najbolj obiskane strani",array("R" => 0, "G" => 0, "B" => 0, "FontSize" => 20));
$myImage->drawText(30,60, "uporabnika",array("R" => 0, "G" => 0, "B" => 0, "FontSize" => 20));
//$myImage->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

//ustvari pie class
$PieChart = new pPie($myImage,$usersData);
$PieChart->draw2DPie(350,135,array("Border"=>TRUE,"Radius"=>100, "WriteValues"=>PIE_VALUE_PERCENTAGE, "ValuePosition"=>PIE_VALUE_OUTSIDE, "ValueR"=>0, "ValueG"=>0, "ValueB"=>0));
$PieChart->drawPieLegend(60,90,array("Alpha"=>10, "FontSize"=>12, "R" => 150, "G" => 150, "B" => 105));
//$myImage->setShadow(FALSE);

//shrani sliko
$myImage->Render("lib/charts/chart_pages_user.png");
?>



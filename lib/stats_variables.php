<?php
include_once "functions.php";
include_once "classes.php";

$stats_viewer = new StatsTracker();

// array za graf "Najbolj obiskane strani"
$page_index = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/index.php'");
$page_basic = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/basic.php'");
$page_advanced = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/advanced.php'");
$page_profile = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/profile.php'");
$page_admin = $stats_viewer->getStatsRows("SELECT COUNT(*) FROM stats WHERE page='/CondorUI/admin.php'");

$array_page = array(
	'index' => $page_index,
	'basic' => $page_basic,
	'advanced' => $page_advanced,
	'profile' => $page_profile,
	'admin' => $page_admin,
);

// array za graf "Stevilo uporabnikov zadnjih 24 ur"
$array_last = array();
$current_time_24h = time();
$precision = 3600;

for($i=0;$i<(86400/$precision);$i++)
{
$min_time = $current_time_24h - 86400 + $i*$precision;
$max_time = $min_time + $precision;
$users_24h = $stats_viewer->getStatsRows("SELECT COUNT(DISTINCT ip) FROM stats WHERE date_visited>=".$min_time." AND date_visited<=".$max_time);
$this_time = intval(($min_time+$max_time)/2);
$array_last_24h[date('H:i',$this_time)] = intval($users_24h);
}

// array za graf "Stevilo uporabnikov v zadnjem letu"
$array_last_year = array();
$current_time_year = time();
$fixed_time_year = time();
$precision = 3600;

for($i=0;$i<12;$i++)
{
	while(date('M',$current_time_year) == date('M',$fixed_time_year))
	{
		$current_time_year -= 86400;
		if (date('M',$current_time_year) != date('M',$fixed_time_year))
		{
			$users_year = $stats_viewer->getStatsRows("SELECT COUNT(DISTINCT ip) FROM stats WHERE date_visited>=".$current_time_year." AND date_visited<=".$fixed_time_year);
			$array_last_year[date('M',$fixed_time_year)] = intval($users_year);
		}
	}

	$fixed_time_year = $current_time_year;
}

$array_last_year = array_reverse($array_last_year);
?>

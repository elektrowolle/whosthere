<?php
include "conf.php";
$today_arrivals  = null;
$former_arrivals = null;

//RESET
if ($_GET['mode'] = "resetCookie") {
	$_COOKIE = null;
}

//TIMING
$today->setTime(0,0,0);
$historicDate->sub($config['historic_arrivals_interval']);

//USER DATA
function isCheckedIn() {
	return !empty($_COOKIE['lastCheckin']) && $_COOKIE['lastCheckin'] > $today;
}

function locationIsNeeded() {
	return !isCheckedIn();
}

function isArrived() {

}

function isKioskMode() {
	return !empty($_GET['kioskMode']) && $_GET['kioskMode'] == 'true';
}

$tpl->assign(
	'user_name',
	!empty($_COOKIE['name']) 
		? $_COOKIE['name']
		: $lang['_name_request']);

//Arrivals
$todayArrivalQuery = $db
	->{'\'whosthere.sqlite.visitorLog\''}()
	->where('time > ' . $today->getTimestamp());

$today_arrivals = queryToArray($todayArrivalQuery);



$formerArrivalsQuery = $db
	->{'\'whosthere.sqlite.visitorLog\''}()
	->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());

$former_arrivals = queryToArray($formerArrivalsQuery);

$tpl->assign('today_arrivals' , $today_arrivals);
$tpl->assign('former_arrivals', $former_arrivals);
$tpl->assign('initial_time'   , time());

$tpl->draw('start');
?>

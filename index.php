<?php
	include "conf.php";
	
	$today           = new DateTime();
	$historicDate    = new DateTime();
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
	function isCheckedIn(){
		return !empty($_COOKIE['lastCheckin']) && $_COOKIE['lastCheckin'] > $today;
	}

	function locationIsNeeded(){
		return !isCheckedIn();
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

	$tpl->assign('today_arrivals',  $today_arrivals);

	$formerArrivalsQuery = $db
		->{'\'whosthere.sqlite.visitorLog\''}()
		->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());

	$former_arrivals = queryToArray($formerArrivalsQuery);

	$tpl->assign('former_arrivals', $former_arrivals);



	$tpl->draw('flightlist');
?>
<?php
	include "conf.php";
	
	$today           = new DateTime();
	$historicDate    = new DateTime();
	$today_arrivals  = null;
	$former_arrivals = null;
	
	$today->setTime(0,0,0);
	$historicDate->sub($config['historic_arrivals_interval']);

	$todayArrivalQuery = $db
		->{'\'whosthere.sqlite.visitorLog\''}()
		->where('time > ' . $today->getTimestamp());
	
	$today_arrivals = queryToArray($todayArrivalQuery);

	$formerArrivalsQuery = $db
		->{'\'whosthere.sqlite.visitorLog\''}()
		->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());

	$former_arrivals = queryToArray($formerArrivalsQuery);

	$tpl->assign($today_arrivals);
	$tpl->assign($former_arrivals);

	$tpl->draw('flightlist');
?>
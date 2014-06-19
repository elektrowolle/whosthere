<?php
	include "conf.php";
	
	$today        = new DateTime();
	$historicDate = new DateTime();
	
	$today->setTime(0,0,0);
	$historicDate->sub($config['historic_arrivals_interval']);

	$today_arrivals  = $db
		->visitorLog();
		//->where('time > ' . $today->getTimestamp());
	
	$former_arrivals = $db
		->visitorLog()
		->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());

	$tpl->draw('flightlist');
?>
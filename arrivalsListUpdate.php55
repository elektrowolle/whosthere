<?php
include_once 'conf.php';

//Arrivals
$filter = "";

if(!empty($_GET['filter']))
	$filter = $_GET['filter'];

$arrivalsQuery = $db
	->{'\'whosthere.sqlite.visitorLog\''}();
if ($filter == 'former') {
	$arrivalsQuery->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());
	
}else {
	$arrivalsQuery->where('time > ' . $today->getTimestamp());
}

	

$arrivals = queryToArray($arrivalsQuery);

setTplMessage("arrivals");
$tpl->assign('arrivals',  $arrivals);



$tpl->draw('arrivalList');

?>
<?php
include_once 'conf.php';
$log      = $db->{'\'whosthere.sqlite.visitorLog\''}();
$name     = $_POST['name'];
$time     = time();
$duration = $config['default_duration'];

try {
	$origin = $_POST['location'];

	if(!empty($origin) && $origin != ""){
		$destination = $config['destination'];
		$mapsURL     = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations='. $destination . '&mode=bicycling';
		$mapsJson    = file_get_contents($mapsURL);
		$result      = json_decode($mapsJson, true);
		$apxDuration = $result['rows'][0]['elements'][0]['duration']['value'];
	}
	
	if ($result['status'] == 'OK' && $apxDuration != '0') {
		$duration = $apxDuration;
	}
} catch (Exception $e) {}

$time += $duration;

$data = array(
	'time'   => $time,
	'name'   => $name,
	'status' => false
	);

$_COOKIE["lastCheckedIn"] = time();

$log->insert($data);
?>

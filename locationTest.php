<?php
	include_once 'conf.php';

	$time     = time();
	$duration = $config['default_duration'];
	
	try {
		$origin = $config['default_position'];

		if(!empty($origin) && $origin != ""){
			$destination = '8+Am+Speicher+XI+Bremen';
			$mapsURL     = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations='. $destination . '&mode=bicycling';
			$mapsJson    = file_get_contents($mapsURL);
			$result      = json_decode($mapsJson, true);
			print_r($mapsJson);
			print_r($mapsURL);
			print_r($result);
			$apxDuration = $result['rows'][0]['elements'][0]['duration']['value'];
		}
		if ($result['status'] == 'OK' && $apxDuration != '0') {
			$duration = $apxDuration;
		}
		

	} catch (Exception $e) {}

?>
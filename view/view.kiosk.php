<?php

include_once 'view._module.php';

/**
* 
*/
class KioskView extends ViewModule {

	public function update() {
		$lang         = $GLOBALS['lang'];
		$db           = $GLOBALS['db'];
		$today        = $GLOBALS['today'];
		$historicDate = $GLOBALS['historicDate'];
		$config       = $GLOBALS['kiosk'];

		foreach ($this->args as $key => $value) {
			$config[$key] = $value;
		}

		//USER DATA

		//Arrivals
		$todayArrivalQuery = $db
			->{'\'whosthere.sqlite.visitorLog\''}()
			->where('time > ' . $today->getTimestamp());

		$today_arrivals = queryToArray($todayArrivalQuery);

		$formerArrivalsQuery = $db
			->{'\'whosthere.sqlite.visitorLog\''}()
			->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());

		$former_arrivals = queryToArray($formerArrivalsQuery);

		$this->setContent('rotation', KioskView::rotationToInt($config['rotation']));
		$this->setContent('width', $config['width']);

		$this->setContent('today_arrivals' , $today_arrivals);
		$this->setContent('former_arrivals', $former_arrivals);

		//$tpl->draw('start');
	}

	static public function rotationToInt($value=''){
		switch (strtoupper($value)) {
			case 'CW':
				return '90'; 
				break;

			case 'CCW':
				return '270'; 
				break;
			
			default:
				return '0';
				break;
		}
	}
}

View::registerView('KioskView', 'kiosk', 'kiosk');
/*
//TIMING

*/

?>
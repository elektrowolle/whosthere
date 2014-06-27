<?php
	
include_once 'inc.api._module.php';

/**
* Just an example
*/
class arrivalsApi extends apiModule	{
	var $today        = '';
    var $db           = '';
    var $historicDate = '';
    var $filter       = '';
    var $arrivalsQuery;

	function __construct($args = array('' => ''))
	{
		parent::__construct($args);
		$this->today                  = $GLOBALS['today'];
	    $this->db                     = $GLOBALS['db'];
	    $this->historicDate           = $GLOBALS['historicDate'];
	    $this->filter                 = '';
		$this->content['lastRequest'] = time();
		$this->template               = 'arrivalList';
		$this->tplMessage             = 'arrivals';


		$this->arrivalsQuery = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
	}

	public function show($value = '') {
		if(is_array($value)) {
			$mode = $value['filter'];
		} else {
			$mode = $value;
		}

		switch ($mode) {
			case 'former':
				$this->filter = $this->filter . ' time < ' . $this->today->getTimestamp() . ' AND time > ' . $this->historicDate->getTimestamp();
				break;

			case 'since':
				if (isset($value['time'])) {
					$this->content["arrivals_since"] = $value['time'];
					$this->filter = $this->filter .  ' time > ' . $value['time'];
				}else
					echo "is not set";
				break;

			default:
				$this->filter = $this->defaultFilter();
				break;
		}
		$this->arrivalsQuery->where($this->filter);
		$this->content['arrivals'] = queryToArray($this->arrivalsQuery);
	}

	public function announce($value = '') {
		$mode = isset($value['mode']) ? $value['mode'] : "announcement";
		switch ($mode) {
			case 'announcement':
				$this->storeNewArrival($value);
				break;

			case 'update':
				$this->updateArrival($value);
				break;

			case 'cancellation':
			default:
				throw new Exception('Mode ' . $mode . ' not yet implemented', 1);
				break;
		}
	}

	public function updateArrival($value) {
		$arrived  = isset($value['arrived']) ? $value['arrived'] == "true" : false;
		$id       = isset($value['id']) ? $value['id'] : -1;
		$time     = time();
		$origin   = isset($value['location']) ? $value['location'] : $GLOBALS['config']['default_position'];
		$log      = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
		$duration = $this->getJourneyDuration($origin);
		
		if ($id == -1) {
			throw new Exception("Invalid ID", 1);
		}
		
		if(!$arrived){
			$originLocation      = explode(",", $origin);
			$destinationLocation = explode(",", $GLOBALS['config']['destination_coordinates']);
			$positionDifference  = 
				abs(floatval($originLocation[0]) - floatval($destinationLocation[0]))
				+ abs(floatval($originLocation[1]) - floatval($destinationLocation[1]));

			if($GLOBALS['config']['debug']) print_r($originLocation);
			if($GLOBALS['config']['debug']) print_r($destinationLocation);
			if($GLOBALS['config']['debug']) print_r($positionDifference);
			$arrived = $positionDifference < $GLOBALS['config']['geoDifferenceForArrival'];
		}

		$data = array(
			'time'   => $time + $duration,
			'status' => $arrived
			);

		$arrivalRow = $log[$id];
		$arrivalRow->update($data);

		$localStorage = array(
			'arrival_id'     => $arrivalRow['id'],
			'lastChecked_in' => $arrivalRow['time'],
			'name'           => $arrivalRow['name'],
			'arrived'        => $arrivalRow['status']);

		$this->setContent('localStorage', $localStorage);

		

		if ($arrived == "1") {
			$this->getArrivalMessage($id);
		}else{
			$this->getUpdateForm($id);
		}

	}

	private function setArrived($id) {
		$log = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
		
		$filter = 'id = ' . $id;
		$data = array('status' => 1);

		$log[$id]->update($data);
		
		setcookie('arrived', true, 24*60*60);
		
	}

	public function getArrival($id) {
		$log = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
		// $log->select('');
		$row = $log[$id];
		$localStorage = array(
			'arrival_id'     => $row['id'],
			'lastChecked_in' => $row['time'],
			'name'           => $row['name'],
			'arrived'        => $row['status']);
		
		$this->setContent('today'       , $GLOBALS['today']->format('U'));
		$this->setContent('arrival'     , $row);
		$this->setContent('localStorage', $localStorage);
	}

	private function getJourneyDuration($origin, $destination = '') {
		try {
			

			if(!empty($origin) && $origin != ""){
				$destination = $destination == '' ? $GLOBALS['config']['destination'] :  $destination;

				$mapsURL     = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations='. $destination . '&mode=bicycling';
				$mapsJson    = file_get_contents($mapsURL);
				$result      = json_decode($mapsJson, true);
				$apxDuration = $result['rows'][0]['elements'][0]['duration']['value'];
			}
			
			if ($result['status'] == 'OK' && $apxDuration != '0') {
				return $apxDuration;
			}
				
		} catch (Exception $e) {}
		return $GLOBALS['config']['default_duration'];
	}

	private function storeNewArrival($value) {
		$log             = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
		$name            = isset($value['name']) ? $value['name'] : null;
		$time            = time();
		$duration        = $GLOBALS['config']['default_duration'];
		$status          = isset($value['status']) ? $value['status'] : 0;
		$showArrivalForm = isset($value['show_arrival_form']) ? $value['show_arrival_form'] == "true" : false;
		$origin          = isset($value['location']) ? $value['location'] : $GLOBALS['config']['default_position'];

		$this->content['success'] = true;

		if($name == null) throw new Exception("Name can't be empty", 1);

		$duration = $this->getJourneyDuration($origin);

		$time += $duration;

		$data = array(
			'time'   => $time,
			'name'   => $name,
			'status' => $status
			);

		if($GLOBALS['config']['debug']) $this->content['data'] = $data;

		$newArrival = $log->insert($data);
		$id = $newArrival['id'];

		if ($showArrivalForm) {
			$this->getUpdateForm($id);
		} else {
			$this->getArrival($id);
		}
	}

	public function getUpdateForm($id='') {
		$this->getArrival($id);
		$this->setContent('arrivalTimeM', floor(intval($this->getContent('arrival')['time'] - time()) / 60));
		$this->template   = "updateForm";
	}

	public function getArrivalMessage($id='') {
		$this->getArrival($id);
		$this->template   = "arrivalMessage";
	}

	public function defaultApi($value=''){

	}

	public function defaultFilter(){
		return 'time > ' . $this->today->getTimestamp();
	}

	public function answer() {
		
		$this->arrivalsQuery->where($this->filter);
		
		if($GLOBALS['config']['debug']) $this->content['filter'] = $this->filter;

		return parent::answer();
	}


}

API::registerAPi('arrivalsApi', 'arrivals');
?>
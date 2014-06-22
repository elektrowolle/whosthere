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

		private function updateArrival($value) {
			$arrived = isset($value['arrived']) ? $value['arrived'] : false;
			$id = isset($value['id']) ? $value['id'] : -1;

			if ($id == -1) {
				throw new Exception("Invalid ID", 1);
			}

			if ($arrived != false) {
				$this->setArrived($id);
			}
		}

		private function setArrived($id)
		{
			$log = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
			
			$filter = 'id = ' . $id;
			$data = array('status' => 1);

			$log->where($filter);
			$log->update($data);
			
			setcookie('arrived', true, 24*60*60);
			
		}

		private function storeNewArrival($value) {
			$log             = $this->db->{'\'whosthere.sqlite.visitorLog\''}();
			$name            = isset($value['name']) ? $value['name'] : null;
			$time            = time();
			$duration        = $GLOBALS['config']['default_duration'];
			$status          = isset($value['status']) ? $value['status'] : 0;
			$showArrivalForm = isset($value['showArrivalForm']) ? $value['showArrivalForm'] : false;

			$this->content['success'] = true;

			if($name == null) throw new Exception("Name can't be empty", 1);

			try {
				$origin = isset($value['location']) ? $value['location'] : $GLOBALS['config']['default_position'];
				

				if(!empty($origin) && $origin != ""){
					$destination = $GLOBALS['config']['destination'];

					$mapsURL     = 'http://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations='. $destination . '&mode=bicycling';
					$mapsJson    = file_get_contents($mapsURL);
					$result      = json_decode($mapsJson, true);
					$apxDuration = $result['rows'][0]['elements'][0]['duration']['value'];
				}
				
				if ($result['status'] == 'OK' && $apxDuration != '0') {
					$duration = $apxDuration;
				}
			} catch (Exception $e) {
				$this->content['success'] = false;
			}

			$time += $duration;

			$data = array(
				'time'   => $time,
				'name'   => $name,
				'status' => $status
				);

			setcookie('lastCheckedIn', time(), time() + true, 24*60*60, '/');
			setcookie('name', $name, true, time() + 24*60*60, '/');

			if($GLOBALS['config']['debug']) $this->content['data'] = $data;

			$this->content['newArrival'] =  $log->insert($data);	

			if ($showArrivalForm != false) {
				$this->template   = "arrivalForm";
			}
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

	$api->registerAPi('arrivalsApi', 'arrivals');
?>
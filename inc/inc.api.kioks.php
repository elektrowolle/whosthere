<?php
	/**
	* 
	*/
	class kioskApi extends apiModule
	{
		var $db;
		var $options;
		
		function __construct($args = array('' => '')) {
  			parent::__construct();

  			$this->db = $GLOBALS['db'];
			$this->options = $this->db->{'\'whosthere.sqlite.options\''}();

		}

		public function defaultApi($value) {

		}

		public function kioskUpdates($value='') {
			$this->content['enabled'] = $this->options['showIP']['value'];
		}

		public function remoteControl($value='') {
			foreach ($variable as $key => $value) {			
				switch ($key) {
					case 'showIp':
						setShowIp($value);
						break;
					
					default:
						throw new Exception("No", 1);
						
						break;
				}
			}
		}

		private function setShowIp($value = 'false') {
			$data = array(
				'id'    => 'showIP',
				'value' =>  $value);

			$this->options->update($data);
			$this->content['enabled'] = $this->options['showIP']['value'];
		}
	}

	API::registerApi('kioskApi', 'kiosk');
?>
<?php
	/**
	* 
	*/
	class kioskApi extends apiModule {
		var $db;
		var $options;
		var $name;
		
		function __construct($args = array('' => '')) {
  			parent::__construct();

  			$this->db = $GLOBALS['db'];
			$this->options = $this->db->{'\'whosthere.sqlite.options\''}();

		}

		public function defaultApi($value) {}

		public function kioskUpdates($args = array()) {
			$this->name = $args['name'];
			$this->options->where('id LIKE "kiosk[' . $this->name . ']%"');
			
			foreach ($this->options as $id => $value) {
				$decodedValue   = json_decode($value['value'], true);
				$unescapedValue = $decodedValue ? $decodedValue : $value['value'];
				$unescapedId    = (substr($id, stripos($id, '_')+1));
				$this->setContent($unescapedId, $unescapedValue);
			}
			$this->setContent('server_time', time());
			
		}

		private function getOption($value='') {
			$id = substr($value, stripos($value, '_') +1 );
			return $this->options[$id];
		}

		public function remoteControl($args='') {
			$cssData         = array();
			$cssData['body'] = array();
			$now             = time();
			$this->name      = $args['name'];

			if ($this->name == '') {
				throw new Exception('name can\'t be empty', 1);
				
			}

			foreach ($args as $key => $value) {			
				switch ($key) {
					case 'css-rotation':
						$cssData['body']['rotation'] = 'rotate(' . rotationToInt($value) . 'deg)';
						break;
					
					case 'css-top':
						$cssData['body']['top'] = $value . 'px';
						break;

					case 'css-left':
						$cssData['body']['left'] = $value . 'px';
						break;

					case 'css-width':
						$cssData['body']['width'] = $value;
						break;

					case 'css-custom':
						$decoded =json_decode($value,true);
						if($value == null)
							continue;
						elseif (!$decoded) 
							throw new Exception('css-custom arguments must be json', 1);
							
						foreach ($value as $identifier => $css) {
							if($identifier == 'body')
								$cssData['body']['custom'] = $css;
							else
								$cssData[$identifier] = $css;
						}
						break;

					case 'name':
						# code...
						break;

					default:
						$this->setOption($key, $value);
						break;
				}
			}
			

			if(count($cssData) > 0){
				$this->setOption('kiosk_css', json_encode($cssData));
			}

			$this->setOption('last_update', $now);


			$this->setContent('css_data'   , $cssData);
			$this->setContent('last_update', $now);
		}



		private function setOption($key, $value) {
			$data = array(
				//'id'    => 'kiosk[' . $this->name . ']_' . $key,
				'value' => $value);

			$this->options->insert_update(
				array('id' => 'kiosk[' . $this->name . ']_' . $key), 
				$data);

			$this->content['request'][$key] 
					= $this->options[$key];
		}
	}

	API::registerApi('kioskApi', 'kiosk');
?>
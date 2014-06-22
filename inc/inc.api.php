<?php
	include_once 'inc.api._module.php';


	/**
	* 
	*/
	class API {

		var $apis;
		var $output; 
		var $tpl;
		
		function __construct($tpl = 'api', $output = 'html') {
			$this->tpl    = $tpl;
			$this->apis   = array();
			$this->output = $output;
		}

		public function askApi($requestedApi, $request, $args = '') {
			$result = '';
			$api;
			if(!is_array($args)){
				$decodedArgs = json_decode($args, true);
				$args        = $decodedArgs != null ? $decodedArgs : $args;
			}
			try {
				if (!isset($this->apis[$requestedApi])) {
					throw new Exception("No such API: " . $requestedApi, 1);
				}
				$api = $this->loadApi($requestedApi);
				$api->get($request, $args);
			} catch (Exception $e) {
				$api = $this->loadApi('error');
				$api->get('', $e);
			}
			$result = $api->answer();
			$this->output($result);
		}

		public function registerAPi($apiClassName='', $name='') {
			$apiClass = new ReflectionClass($apiClassName);
			$this->apis[($name == '') ? $apiClassName : $name] = $apiClass;
		}

		public function loadApi($api) {
			return $this->apis[$api]->newInstance();
		}

		public function output($content)
		{
			switch ($this->output) {
				case 'json':
					$this->jsonOutput($content['content']);
					break;

				case 'html':
				default:
					$this->htmlOutput($content['template'], $content['content'], $content['tplMessage']);
					break;
			}
		}

		public function htmlOutput($emplate, $content, $tplMessage){
			setTplMessage($tplMessage);

			if(is_array($content)){
				foreach ($content as $key => $value) {
					$this->tpl->assign($key,  $value);

				}
			}else{
				$this->tpl->assign('content',  $content);
			}

			$this->tpl->assign('api_values', print_r($content, true));
			
			$this->tpl->draw($emplate);
		}

		public function jsonOutput($content) {
			echo json_encode($content);
		}

		public function getJs($requestedApi) {
			$api = $this->loadApi($requestedApi);
			$this->tpl->assign('api' . $requestedApi . 'js', $requestedApi->getJs());
		}
	}


	
?>
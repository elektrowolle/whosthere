<?php

/**
* 
*/
class View {

	static $views = array();
	var $view;

	function __construct($viewName ='', $args = '', $update = false) {
		$this->view = View::loadView($viewName == '' ? 'default': $viewName, $args, $update);
	}

	public function draw($tpl) {
		$this->view->draw($tpl);
	}

	static public function registerView($viewClassName='', $name='', $template = 'api') {
		$viewClass = new ReflectionClass($viewClassName);
		View::$views[($name == '') ? $viewClassName : $name]['class']    = $viewClass;
		View::$views[($name == '') ? $viewClassName : $name]['template'] = $template;
	}

	static public function loadView($view, $args, $update) {
		return View::$views[$view]['class']->newInstance($args, $update, View::$views[$view]['template']);
	}
}

include_once 'view._views.php';

?>
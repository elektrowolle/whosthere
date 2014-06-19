<?php
	include_once 'conf.php';

	$mode = $_GET['mode'];

	switch ($mode) {
		case 'announce':
			echo announce();
			break;

		case 'update':
			# code...
			break;

		default:
			# code...
			break;
	}

	function announce()
	{
		$db   = $GLOBALS['db'];
		$log  = $db->{'\'whosthere.sqlite.visitorLog\''}();
		$name = $_POST['name'];
		
		$data = array(
			'time'   => time(),
			'name'   => $name,
			'status' => false
			);
		
		return $log->insert($data);

	}
?>
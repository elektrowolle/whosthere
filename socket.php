<?php
	//Websockets
	require_once 'SplClassLoader.php';
	$classLoader = new \SplClassLoader('Wrench', __DIR__ . 'php-websocket/lib');
	$classLoader->register();
?>
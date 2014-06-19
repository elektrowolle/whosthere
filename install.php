<?php
	include_once 'conf.php';

	try {
		$pdo = new PDO($config['db_name']);
		$query = 
		  'CREATE TABLE IF NOT EXISTS "whosthere.sqlite.visitorLog" ( '
		. 'id INTEGER PRIMARY KEY AUTOINCREMENT, '
		. 'name TEXT NOT NULL, '
		. 'time INTEGER NOT NULL) ';

		print($query);
		$pdo->exec($query) or die(print_r($pdo->errorInfo(), true)); ;
	} catch (Exception $e) {
		print_r($e);
	}

	

?>
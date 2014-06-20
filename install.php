<?php
	include_once 'conf.php';

	try {
		$pdo = new PDO($config['db_name']);
		$query = 
		  'CREATE TABLE IF NOT EXISTS "whosthere.sqlite.visitorLog" ( '
		. 'id INTEGER PRIMARY KEY AUTOINCREMENT, '
		. 'name TEXT NOT NULL, '
		. 'time INTEGER NOT NULL, '
		. 'status BOOLEAN NOT NULL); '

		. 'CREATE TABLE IF NOT EXISTS "whosthere.sqlite.options" ( '
		. 'id TEXT PRIMARY KEY, '
		. 'value TEXT NOT NULL); ';

		print($query);
		echo $pdo->exec($query); //or die(print_r($pdo->errorInfo(), true) . "dead");

		$options = $db->{'\'whosthere.sqlite.options\''}();

		$data = array(
				'id'    => 'showIP',
				'value' => 'false');

		echo "\n " . $options->insert($data);

	} catch (Exception $e) {
		echo "\n exception: ";
		print_r($e);
	}

	

?>
<?php
	include_once "util.php";
	//////INDEPENDENCIES
	$libDirectory = 'libs/';
	//Templates
	include $libDirectory . "raintpl/inc/rain.tpl.class.php"; //include Rain TPL
	//raintpl::$base_url  = __DIR__;
	raintpl::$tpl_dir   = "tpl/";             // template directory
	raintpl::$cache_dir = "tmp/";             // cache directory
	$tpl = new rainTPL();

	//////DATABASE / ORM
	$config['db_name'] = 'sqlite:whosthere.sqlite'; 

	include_once $libDirectory . 'notorm/NotORM.php';
	$pdo = new PDO($config['db_name']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
	$db  = new NotORM($pdo);

	//////LANG _DE
	$config['default_language'] = 'de';
	include_once 'lang.php';

	$lang = initLang($config['lang'], $config['default_language']);
	$tpl->assign($lang['de']);

	//////Different Stuff
	$config['historic_arrivals']          = 3;    //in days
	$config['historic_arrivals_interval'] = new DateInterval('P' . $config['historic_arrivals'] . 'D');
	
	$config['socket_address'] = 'socket.php';
	$tpl->assign('socket_address', $config['socket_address']);
?>
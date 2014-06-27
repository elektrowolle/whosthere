<?php
date_default_timezone_set("Europe/Berlin");
include_once "util.php";
//////INDEPENDENCIES
$libDirectory = 'libs/';
//Templates
include $libDirectory . "raintpl/inc/rain.tpl.class.php"; //include Rain TPL


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

$lang = initLang($config['lang'], $config['default_language'])[$config['default_language']];


//////Different Stuff
$config['historic_arrivals']          = 3;    //in days
$config['historic_arrivals_interval'] = new DateInterval('P' . $config['historic_arrivals'] . 'D');

$config['googleApiKey'] = '';

$config['debug'] = false;

$config['default_position']        = '53.074435,8.808602';
$config['default_duration']        = 52380;
$config['destination']             = '8+Am+Speicher+XI+Bremen';
$config['destination_coordinates'] = '53.096725,8.7697675';
$config['geoDifferenceForArrival'] = .002;

$config['webAddress']        = 'http://whosthere.hausnr11.de/';
$config['path']              = '/whosthere/';
$config['apiAddressRestful'] = $config['path'] . 'api/v0';
$config['apiAddress']        = $config['apiAddressRestful']; //*/$config['path'] . 'api.php';


$config['appCheckinUrl'] = $config['webAddress'] . $config['path'] . '?installApp=true&arrived=true';
$config['restFulLinks']  = "true";


//////Kiosk Stuff
$kiosk = array();
$kiosk['width']    = '500';
$kiosk['rotation'] = '0';
$config['kiosk'] = $kiosk;


raintpl::$base_url     = $config['path']  ;
raintpl::$tpl_dir      = "tpl/";             // template directory
raintpl::$cache_dir    = "tmp/";             // cache directory
$tpl = new rainTPL();

//$config['shortAppCheckinUrl'] = "http://goo.gl/4htls6";



///// Assignments


$today           = new DateTime();
$historicDate    = new DateTime();
$today->setTime(0,0,0);
$historicDate->sub($config['historic_arrivals_interval']);

$__debug = 1;
$__att   = $__debug;

$tpl->assign($lang);

$tpl->assign('restful_links'   , $config['restFulLinks']);
$tpl->assign('api_address'     , $config['apiAddress']);
$tpl->assign('web_address'     , $config['webAddress']);
$tpl->assign('app_checkin_url' , $config['appCheckinUrl']);
$tpl->assign('default_position', $config['default_position']);
?>

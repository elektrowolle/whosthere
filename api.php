<?php
include_once 'conf.php';

//Arrivals
$args        = '';
$output      = 'html';
$getMode     = '';
$content     = '';
$api_success = false;

if (!empty($_GET['get'])) 
	$getMode = $_GET['get'];

if(!empty($_GET['output']))
	$output = $_GET['output'];

if(!empty($_GET['args']))
	$args = $_GET['args'];

/////  ARGS
$argsArray = json_decode($args, true);

if($argsArray != null){
	$args = array();
	foreach ($argsArray as $key => $value) {
		$args[$key] = $value;
	}
}else{
	$tmpArgs = array($args => '' );
	$args    = $tmpArgs;
}

/////  MODES
switch ($getMode) {
	case 'arrivals':
		$content = arrivals($args);
		break;

	case 'kiosk':
		$content = kiosk($args);
		break;
	
	default:
		$content = array(
			'content'    => array('content' => "invalid"),
			'template'   => 'api',
			'tplMessage' => '');
		break;
}

function kiosk($args) {
	$db = $GLOBALS['db'];
	$content = array();
	$options = $db->{'\'whosthere.sqlite.options\''}();

	foreach ($args as $key => $value) {
		switch ($key) {
		case 'showIP':
			$content['enabled'] = $options['showIP']['value'];
			break;

		case 'toggleShowIP':
			$content['former_enabled'] = $options['showIP']['value'];
			
			$data = array(
				'id'    => 'showIP',
				'value' =>  $value);

			$options->update($data);
			
			$content['enabled'] = $options['showIP']['value'];
			break;
		
		default:
			# code...
			break;
		}
	}

	$ret = array(
		'content'    => $content,
		'template'   => 'api',
		'tplMessage' => '');
	return $ret;
}

function arrivals($args) {
	$today        = $GLOBALS['today'];
	$db           = $GLOBALS['db'];
	$historicDate = $GLOBALS['historicDate'];
	$filter       = isset($args["filter"]) ? $args["filter"] : "";
	$content      = array();

	$content['lastRequest'] = time();

	$arrivalsQuery = $db->{'\'whosthere.sqlite.visitorLog\''}();

	if ($filter == '') {
		$arrivalsQuery->where('time > ' . $today->getTimestamp());
	}else{
		switch ($filter) {
			case 'former':
				$arrivalsQuery->where('time < ' . $today->getTimestamp() . ' AND time > ' . $historicDate->getTimestamp());
				break;

			case 'since':

				if (isset($args['filterArg'])) {
					$content["arrivals_since"] = $args['filterArg'];
					$arrivalsQuery->where('time > ' . $args['filterArg']);
				}
				break;
		}
	}

	$content['arrivals'] = queryToArray($arrivalsQuery);
	
	$template   = "arrivalList";
	$tplMessage = "arrivals"   ;

	$ret = array(
		'content'    => $content,
		'template'   => $template,
		'tplMessage' => $tplMessage);
	return $ret;
}

$content["api_success"] = $api_success;

/////  OUTPUT
function htmlOutput($content, $tpl){
	setTplMessage($content['tplMessage']);

	if(is_array($content)){
		foreach ($content['content'] as $key => $value) {
			$tpl->assign($key,  $value);

		}
	}else{
		$tpl->assign('content',  $content);
	}

	$tpl->assign('api_values', serialize($content['content']));
	
	$tpl->draw($content['template']);
}


switch ($output) {
	case 'json':
		echo json_encode($content['content']);
		break;
	
	default:
		htmlOutput($content, $tpl);
		break;
}

?>
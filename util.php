<?php
	
	$tplMessage = "";

	function initLang($languageArray, $language) {

		$lang[$language] = $languageArray['default'];

		foreach ($languageArray['default'] as $key => $value) {
			if(isset($languageArray[$language][$value]) && !empty($languageArray[$language][$value])){
				$lang[$value] = $languageArray[$language][$value];
			}
		}

		return $lang;
	}

	function queryToArray($query) {
		$ret = null;
		foreach ($query as $id => $value) {
		$ret[] = array(
			'name'   => $value['name'],
			'time'   => $value['time'],
			'status' => $value['status']);
		}
		
		return $ret;
	}

	function stDate($time, $format) {
		return date($format, $time);
	}

	function setTplMessage($str){
		$GLOBALS['tplMessage'] = $str;
	}

?>
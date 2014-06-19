<?php

	function initLang($languageArray, $language){

		$lang[$language] = $languageArray['default'];

		foreach ($languageArray['default'] as $key => $value) {
			if(isset($languageArray[$language][$value]) && !empty($languageArray[$language][$value])){
				$lang[$value] = $languageArray[$language][$value];
			}
		}
		return $lang;
	}
?>
<?php
	// -- рекурсивна€ обработка элементов массива по trim
	function pre_global_arrays_trim(&$_value) {
		if(is_array($_value)) {
			foreach($_value as $i => &$item) {
				pre_global_arrays_trim($item);
			}
			return true;
		}
		$_value = trim($_value);
		return true;
	}
	
	// -- рекурсивна€ обработка элементов массива по stripslashes
	function pre_global_arrays_stripslashes(&$_value) {
		if(is_array($_value)) {
			foreach($_value as $i => &$item) {
				pre_global_arrays_stripslashes($item);
			}
			return true;
		}
		$_value = stripslashes($_value);
		return true;
	}
	
	pre_global_arrays_trim($_POST);
	pre_global_arrays_trim($_GET);
	
	if(get_magic_quotes_gpc()) {
		pre_global_arrays_stripslashes($_POST);
		pre_global_arrays_stripslashes($_GET);
	}
?>
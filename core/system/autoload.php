<?php
	function __autoload($class) {
		$class = strtolower($class);
		$paths = array();
		$application_path = defined('APPLICATION_ADMIN') ? APPLICATION_ADMIN : APPLICATION;
		
		if(substr($class, -11, 11) == '_controller') {
			$paths = array(
				$application_path.'controllers'.DS,
				CORE.'libs'.DS
			);
		} elseif(substr($class, -8, 8) == '_helpner') {
			$paths = array(
				$application_path.'helpners'.DS,
				CORE.'system'.DS
			);
		} else {
			$paths = array(
				CORE.'models'.DS,
				CORE.'views'.DS,
				$application_path.'models'.DS
			);
		}
		
		foreach($paths as $path) {
			if(file_exists($path.$class.'.php')) {
				require_once($path.$class.'.php');
			}
		}
	}
?>
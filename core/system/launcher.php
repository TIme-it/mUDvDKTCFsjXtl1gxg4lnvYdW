<?php

	function __autoload($class) {
		$classFileName	= $class;
		$libsPaths		= null;

		if( substr($classFileName, -10) == 'Controller' ) {
			$classFileName = strtolower(substr($classFileName, 0, -10)).'_'.strtolower(substr($classFileName, -10)).'.php';

			$libsPaths = array(	CORE.'controllers'.DS,
										APPLICATION.'controllers'.DS);
		} else {
			$classFileName = strtolower($classFileName).'.php';

			$libsPaths = array(	CORE,
										CORE.'models'.DS,
										CORE.'views'.DS,
										APPLICATION.DS.'models'.DS);
		}

		if( $libsPaths === null )
			return false;

		foreach( $libsPaths as $libPath ) {
			if( file_exists($libPath.$classFileName) ) {
				require_once($libPath.$classFileName);
				return true;
			}
		}

		return false;
	}

?>
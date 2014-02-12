<?php
	//error_reporting(E_ALL);
	//ini_set('display_errors', 'On');
	
	require_once('define.php');
	require_once(SYSTEM.'autoload.php');
	require_once(SYSTEM.'preprocessing.php');
	require_once(SYSTEM.'app_model.php');
	// if ($_SERVER['SERVER_NAME']!='www.trios.ru')	{header("HTTP/1.1 301 Moved Permanently"); header("Location: http://www.trios.ru".$_SERVER['REQUEST_URI']); }
	if (OFFICE) {
		error_reporting(E_ALL);
		ini_set('display_errors', 'off');
	}
	
	try {
		$uri = new Uri_Controller();
	} catch(Exception $e) {
		print $e->getMessage();
	};
?>
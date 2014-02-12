<?php
	error_reporting(E_ALL);
	
	require_once('../define.php');
	require_once(SYSTEM.'autoload.php');
	require_once(SYSTEM.'preprocessing.php');
	require_once(SYSTEM.'app_model.php');
	
	try {
		$uri = new Uri_Controller();
	} catch(Exception $e) {
		print $e->getMessage();
	};
?>
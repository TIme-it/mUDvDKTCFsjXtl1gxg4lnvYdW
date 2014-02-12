<?php
	date_default_timezone_set('Europe/Moscow');

	define('DS',          DIRECTORY_SEPARATOR);
	define('INDEX',       dirname(__FILE__).DS);

	define('CORE',        INDEX.'core'.DS);
	define('LIBS',        CORE.'libs'.DS);
	define('APPLICATION', INDEX.'application'.DS);
	define('INCLUDES',    APPLICATION.'includes'.DS);
	define('BACKUPS',     INDEX.'backups'.DS);
	define('CONFIG',      INDEX.'application'.DS);
	define('VIEWS',       APPLICATION.'views'.DS);
	define('VIEWS_ADMIN', INDEX.'admin'.DS.'application'.DS.'views'.DS);
	define('HOST',        'http://'.$_SERVER['HTTP_HOST'].'/');
	define('SYSTEM',      CORE.'system'.DS);
	define('URI_BASE',    '/');
	define('DEBUG',       1);
	define('VERSION',     '2.04b');
	
	
	define('OFFICE',     ($_SERVER['REMOTE_ADDR'] == '195.144.209.133') );
	
	define('ALERT_CHANGE_DATA', 'Данные были успешно изменены');
	define('ALERT_DEL_IMAGE',   'Изображение было успешно удалено');
?>
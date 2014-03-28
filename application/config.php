<?php
	// Настройки подключения к базе данных
	$config['db']['host']	 = 'localhost';
	$config['db']['user']	 = 'root';
	$config['db']['pwd']	 = '';
	$config['db']['db']		 = 'midpo';
	$config['db']['charset'] = 'utf8';
	
	// Настройки контроллеров
	$config['system']['index_method'] 		= 'index';            // Метод который загружаается по умолчанию
	$config['system']['main_controller'] 	= 'main_controller';  // Контроллер главной страницы
	$config['system']['default_controller'] = 'pages_controller'; // Контроллер страницы по умолчанию
	$config['system']['admin_dir'] 			= 'admin';

	// Пути в системе
	$config['files']['file'] 		= APPLICATION.'includes'.DS.'files'.DS;
	$config['files']['images'] 		= APPLICATION.'includes'.DS.'uploadIMG'.DS;
	$config['files']['images_main'] = APPLICATION.'includes'.DS.'main'.DS;
	$config['files']['partners']    = APPLICATION.'includes'.DS.'partners'.DS;
	$config['files']['img'] 		= APPLICATION.'includes'.DS.'img'.DS;
	$config['files']['faq'] 		= APPLICATION.'includes'.DS.'faq'.DS;
	$config['files']['reviews'] 		= APPLICATION.'includes'.DS.'reviews'.DS;
	$config['files']['texts'] 		= APPLICATION.'includes'.DS.'texts'.DS;
	$config['files']['pages'] 		= APPLICATION.'includes'.DS.'texts'.DS;
	$config['files']['news'] 		= APPLICATION.'includes'.DS.'news'.DS;
	$config['files']['articles'] 	= APPLICATION.'includes'.DS.'articles'.DS;
	$config['files']['portfolio'] 	= APPLICATION.'includes'.DS.'portfolio'.DS;
	$config['files']['slides'] 		= APPLICATION.'includes'.DS.'slides'.DS;
	$config['files']['actions'] 	= APPLICATION.'includes'.DS.'actions'.DS;
	$config['files']['catalog'] 	= APPLICATION.'includes'.DS.'catalog'.DS;
	$config['files']['profile'] 	= APPLICATION.'includes'.DS.'profile'.DS;
	$config['files']['personal'] 	= APPLICATION.'includes'.DS.'personal'.DS;
	$config['files']['upload'] 		= APPLICATION.'includes'.DS.'upload'.DS;
	$config['files']['banners']     = APPLICATION.'includes'.DS.'visban'.DS;	
	$config['files']['vacancy']     = APPLICATION.'includes'.DS.'vacancy'.DS;	
	$config['files']['firms']       = APPLICATION.'includes'.DS.'firms'.DS;	
	$config['images']['blocks'] 		= APPLICATION.'includes'.DS.'images'.DS.'blocks'.DS;


	$config['chpu']['active'] = 1;

	$config['slides']['img_width'] = 955;
	$config['slides']['img_height'] = 405;

	// -- настройки изображений для эскиза в подсемействе
	$config['sketch_image']['img_width'] = 300;
	$config['sketch_image']['img_height'] = 425;

	// -- настройки изображений кривых ксс для подсемейства
	$config['kss_big']['img_width'] = 620;
	$config['kss_big']['img_height'] = 620;
	$config['kss_small']['img_width'] = 250;
	$config['kss_small']['img_height'] = 250;
	
	// -- настройки изображений для блоков на главной
	$config['blocks_img']['img_width'] = 250;
	$config['blocks_img']['img_height'] = 200;
	
	// -- настройки изображений для новостей
	$config['news']['img_width'] = 106;
	$config['news']['img_height'] = 100;
	
	// -- настройки изображений для отзывов
	$config['reviews']['img_width'] = 256;
	$config['reviews']['img_height'] = 256;

	// -- настройки изображений для статей
	$config['articles']['img_width'] = 140;
	$config['articles']['img_height'] = 80;
	
	// -- настройки изображений для акций
	$config['actions']['img_width'] = 364;
	$config['actions']['img_height'] = 182;

	// -- настройки изображений для портфолио
	$config['portfolio']['img_width'] = 260;
	$config['portfolio']['img_height'] = 147;
	
	// -- настройки изображений для партнеров
	$config['partners']['img_width'] = 140;
	$config['partners']['img_height'] = 80;

	// -- настройки для изображений
	$config['images']['width_big']	  = 800;
	$config['images']['height_big']	  = 600;
	$config['images']['width_small']  = 170;
	$config['images']['height_small'] = 130;
	$config['images']['quality'] 	  =  100;
	
	// -- изображение продукции каталога (только характеризующие картиники)
	// $config['images']['product_main_w']   = 830;
	// $config['images']['product_main_h']   = 400;
	// $config['images']['product_big_w']   = 370;
	// $config['images']['product_big_h']   = 220;
	// $config['images']['product_small_w']   = 450;
	// $config['images']['product_small_h']   = 300;
	$config['minigallery_small']['img_width']   = 69;
	$config['minigallery_small']['img_height']   = 69;
	// $config['images']['product_big_w']   = 450;
	// $config['images']['product_big_h']   = 300;
	// $config['images']['product_small_w'] = 150;
	// $config['images']['product_small_h'] = 100;
	$config['images']['category_w'] = 55;
	$config['images']['category_h'] = 50;
	$config['images']['product_big_w'] = 394;
	$config['images']['product_big_h'] = 318;
	$config['images']['product_medium_w'] = 188;
	$config['images']['product_medium_h'] = 189;
	$config['images']['product_small_w'] = 120;
	$config['images']['product_small_h'] = 121;
	
	// Мультиключи для Яндекс.карт ($_SERVER['SERVER_NAME'])
	$config['mapkeys']['6.direkt.z8.ru']  = 'AJIEsE4BAAAAeTbvTQQAOCkqwarKmMZYzcnL7BlCJdTxo7MAAAAAAAAAAACI7rkWjKCP2viSumiKmyWUrzL9Cg==';
	$config['mapkeys']['ptcard.ru']  = 'AE5UY04BAAAA5Q3_KwIA5hAmTh719TdKdvfBr3iOBJOkQXcAAAAAAAAAAACJdrQcsSz0s6bTRMTiWPFyZAHXEg==';
	$config['mapkeys']['www.ptcard.ru']  = 'AE5UY04BAAAA5Q3_KwIA5hAmTh719TdKdvfBr3iOBJOkQXcAAAAAAAAAAACJdrQcsSz0s6bTRMTiWPFyZAHXEg==';

	// -- настройки YouTube
	// -- 1. профиль Google
	$config['ytube']['user'] = '';
	$config['ytube']['pass'] = '';
	// -- 2. ключ приложения
	$config['ytube']['dkey'] = 'AI39si4EeaocIFBIf0M3kt_QYxq3YlUIcHD3TzFo6KXSWz8R2RcwBhvtJ2OwWTaGbJKRV2kJxb_2nF0gsGAazOCp71TuUfFHow';
	
	// Стандратный email на который отправляется почта с сайтов
	$config['dl']['mail']      = 'alextret91@yandex.ru'; 
	$config['support']['mail'] = 'alextret91@yandex.ru'; 

	$config['site']['org']    = 'Midpo';
	$config['site']['domain'] = $_SERVER['SERVER_NAME']; // URL-сайта, подставляется много где

	$config['catalog']['tree'] = 2;
?>
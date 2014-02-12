<?php
	include_once 'define.php';
	include_once APPLICATION.'config.php';
	
	if(file_exists(INDEX.'CLEANED')) die('CLEANED');
	
	// -- подключение к БД
	mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pwd']);
	mysql_select_db($config['db']['db']);
	
	// -- массив таблиц для отчистки
	$tables = array(
		'banners', 'catalog', 'catalog_categories', 'catalog_techchars', 'faq', 'files',
		'firms', 'images', 'main', 'maps', 'maps_placemarks', 'news', 'order_history',
		'order_history_prod', 'personal', 'photos', 'question', 'question_answer',
		'question_ip', 'recalls', 'search_index', 'users', 'videos'
	);
	
	// -- отчистка таблиц
	foreach($tables as $i => $table) {
		mysql_query('TRUNCATE TABLE `'.$table.'`');
	}
	
	// -- добавление путей
	if(isset($config['files']['catalog'])) {
		$config['files']['catalog_b'] = $config['files']['catalog'].'b'.DS;
		$config['files']['catalog_s'] = $config['files']['catalog'].'s'.DS;
	}
	if(isset($config['files']['img'])) {
		$config['files']['img_b'] = $config['files']['img'].'b'.DS;
		$config['files']['img_l'] = $config['files']['img'].'l'.DS;
	}
	if(isset($config['files']['profile'])) {
		$config['files']['profile_ava_b'] = $config['files']['profile'].'ava_b'.DS;
		$config['files']['profile_ava_s'] = $config['files']['profile'].'ava_s'.DS;
		$config['files']['profile_ava_t'] = $config['files']['profile'].'ava_temp'.DS;
	}
	
	// -- отчиска от файлов
	if(!empty($config['files'])) {
		foreach($config['files'] as $dir_name => $dir_path) {
			if(file_exists($dir_path) && is_dir($dir_path)) {
				$dir_ptr = dir($dir_path);
				while($file_name = $dir_ptr->read()) {
					$file_path = $dir_path.$file_name;
					if(is_file($file_path)) {
						unlink($file_path);
					}
				}
			}
		}
	}
	
	file_put_contents(INDEX.'CLEANED', ' ');
?>
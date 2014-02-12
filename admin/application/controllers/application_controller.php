<?php
	class application_controller extends libs_controller {
		
		private $upload_path;
		private $upload_path_small;
		private $upload_path_big;
		private $image_width;
		private $image_height;
		
		protected $data       = null; // -- $_POST['data']
		protected $active_id  = 0;    // -- id активного раздела
		protected $module_id  = 0;    // -- id теккущего модуля
		protected $admin_dir  = null; // -- метка {admin_dir}
		protected $volume_dir = null; // -- директория раздела (пока не подключено)
		
		public function __construct() {
			// -- определяем пути
			$this->upload_path       = $this->config->get('img','files');
			$this->upload_path_small = $this->upload_path.DS.'l'.DS;
			$this->upload_path_big   = $this->upload_path.DS.'b'.DS;
			$this->image_width       = $this->config->get('width_small','images');
			$this->image_height      = $this->config->get('height_small','images');
			$this->image_widthBig    = $this->config->get('width_big','images');
			$this->image_heightBig   = $this->config->get('height_big','images');
			$this->image_quality     = $this->config->get('quality','images');
		}	
		
		public function __before() {			
			//контроль обновления сессий
			if( ($this->session->get('admin') === false) ) {
				$this->login_controller->index();
				return true;
			}
			$this->html->tpl_vars['user_name'] = $_SESSION['name'];
			
			// $this->character_image->test();
			
			// -- определение активного раздела
			$vars = $this->url->get('vars');
			$this->active_id = (isset($vars[0])) ? (int)$vars[0] : 0;
			
			// -- базовые метки шаблона
			$this->html->tpl_vars['admin_dir'] = $this->admin_dir = $this->config->get('admin_dir','system');
			$this->html->tpl_vars['site_org']  = $this->config->get('org','site');
			$this->html->tpl_vars['version']   = 'версия: '.VERSION;
			
			// -- для автосохранения моделей (используется крайне редко)
			$this->data = (!empty($_POST['data'])) ? $_POST['data'] : array();
			
			// -- подключаем wysiwyg (почти везде нужен)
			$this->html->render('layouts/wysiwyg.html', array(), 'wysiwyg');
		}
		
		// -- ajax-закачка для подмодуля "Файлы на страницу"
		public function files($module_id) {
			$files_count = sizeof($_FILES['file']['name']);
			$i = 0;
			for ($i = 0; $i < $files_count-1; $i++) {
				$result = $this->file->getFileInfo($_FILES['file']['name'][$i]);
				if(false !== $result) {
					$tmp = array('pid'=>$_POST['id'],'module_id'=>$module_id,'filetype'=>$_FILES['file']['type'][$i],'filesize'=>$_FILES['file']['size'][$i]);
					$result = array_merge($result,$tmp);
					$name = $this->db->insert('files',$result);
					$this->file->upload('file',$_FILES['file']['tmp_name'][$i],$name.'.'.$result['extension']);
				}
			}
			die('files');
		}
		
		// -- удаляем раздел из базы "насовсем"
		public function delete_volume($id = 0) {
			$this->db->delete('main', (int)$id);
			$this->session->set('alert', 'Раздел был безвозвратно удален');
			$this->url->redirect('::referer');
		}
		
		
		// -- ajax-закачка для подмодуля "Фотогалерея"
		public function images($module_id) {
			$files_count = sizeof($_FILES['image']['name']);
			for($i = 0; $i < $files_count-1; $i++) {
				$file = $this->file->getFileInfo($_FILES['image']['name'][$i]);
				if(false !== $file) {
					$image = $this->image->analyze($_FILES['image']['tmp_name'][$i]);
					$tmp = array(
						'title'     => $_FILES['image']['name'][$i],
						'pid'       => $_POST['id'],
						'module_id' => $module_id,
						'l_width'   => $this->image_width,
						'l_height'  => $this->image_height,
						'date'      => $this->date->sql_format(time())
					);
					$image = array_merge($image,$tmp);

					$name = $this->db->insert('images',$image);
					
					$ext  = strtolower($file['extension']);
					if($ext == 'jpeg') $ext = 'jpg';
					$this->file->upload('img', $_FILES['image']['tmp_name'][$i], $name.'.'.$ext);
					$this->image->toFile($this->upload_path_small.$name.'.'.$ext,'80',$this->image_width,$this->image_height);
					$this->image->toFile($this->upload_path_big.$name.'.'.$ext,'80',$this->image_widthBig,$this->image_heightBig);
					
					$this->image->toFile($this->upload_path.DS.'t'.DS.$name.'.'.$ext,'80',145,145);
					unlink($this->upload_path.$name.'.'.$ext);
				}
			}
			die('images');
		}
		
		// -- ajax-закачка для подмодуля "Фото на страницу"
		public function photos($module_id) {
			$files_count = count($_FILES['photos']['name']);
			for($i = 0; $i < $files_count-1; $i++) {
				$file = $this->file->getFileInfo($_FILES['photos']['name'][$i]);
				if(!empty($file)) {
					$temp_path = $_FILES['photos']['tmp_name'][$i];
					$image = $this->image->analyze($temp_path);
					$photo = array(
						'pid'       => $_POST['id'],
						'module_id' => $module_id,
						'title'     => $_FILES['photos']['name'][$i],
						'l_width'   => $image['b_width'],
						'l_height'  => $image['b_height'],
						'b_width'   => $image['b_width'],
						'b_height'  => $image['b_height'],
						'extension' => $file['extension']
					);
					$id = $this->db->insert('photos', $photo);
					$load_path = $id.'.'.strtolower($file['extension']);
					$this->file->upload('image', $temp_path, $load_path);
					
				}
			}
			die('photos');
		}
		
		// -- обработка модуля добавить видео
		public function video($act = false) {
			$link = empty($_POST['link']) ? false : trim($_POST['link']);
			$pid  = empty($_POST['pid'])  ? false : (int)$_POST['pid'];
			$mid  = empty($_POST['mid'])  ? false : (int)$_POST['mid'];
			switch($act) {
				case 'add':
					if($link && $pid && $mid) {
						$video = array(
							'pid'       => $pid,
							'module_id' => $mid,
							'link'      => $link
						);
						$id    = $this->db->insert('videos', $video);
						$sql   = 'SELECT MAX(sort) FROM videos WHERE pid = '.$pid.' AND module_id = '.$mid;
						$sort  = (int)get_one($sql) + 1;
						$video = array('sort' => $sort);
						$this->db->update('videos', $video, $id);
					}
					break;
			}
		}

		public function listing($id, $mode, $module_id = 1) {
			switch($mode) {
				case 'images':
					$listing['pid']     = $id;
					$listing['listing'] = $this->main->getImages($id, $module_id);
					if(!empty($listing['listing'])) {
						foreach($listing['listing'] as $i => &$item) {
							$item['src']   = '/application/includes/img/t/'.$item['id'].'.'.$item['extension'].'?_='.rand();
							$item['block'] = 'images';
						}
						echo $this->html->render('submodules/images_list.html', $listing);
					}
					break;
				case 'photos':
					$listing['pid']     = $id;
					$listing['listing'] = $this->main->getPhotos($id, $module_id);
					if(!empty($listing['listing'])) {
						$c = sizeof($listing['listing']);
						foreach($listing['listing'] as $i => &$item) {
							$item['src']   = '/application/includes/uploadIMG/t/'.$item['id'].'.'.$item['extension'].'?_='.rand();
							$item['url']   = '/application/includes/uploadIMG/'.$item['id'].'.'.$item['extension'].'?_='.rand();
							$item['block'] = 'photos';
							if ( ($i+1) < $c ) $item['zap'] = ',';
						}
						$tiny_image = $this->html->render('lists/tiny_image.html', $listing);
						$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, false);
						echo $this->html->render('submodules/photos_list.html', $listing);
					}
					break;
				case 'files':
					$listing['pid']     = $id;
					$listing['listing'] = $this->main->getFiles($id, $module_id);
					if(!empty($listing['listing'])) {
						foreach($listing['listing'] as $i => &$item) {
							$item['filename'] = htmlspecialchars($item['filename']);
							$item['date']     = $this->date->format_list($item['date']);
							$item['eye']      = ($item['is_show']) ? 'eye_show' : 'eye_hide';
						}
						echo $this->html->render('submodules/files_list.html', $listing);
					}
					break;
			}
			die();
		}

		// -- AJAX сортировка для drug-and-drop
		public function ajaxsort($table, $pid) {
			if(empty($table) || empty($pid) || empty($_POST['data'])) {
				die();
			}
			$sort_list = explode(',', $_POST['data']);
			if(empty($sort_list)) die();
			foreach($sort_list as $sort => $id) {
				$this->db->update($table, array('sort' => $sort), $id);
			}
			die();
		}
		
		// -- управление загруженными картинками через ajax
		public function multiupload($type, $act, $id) {
			$types = array('images','photos','files');
			if(in_array($type, $types)) {
				$data = $this->db->get_row('SELECT * FROM '.$type.' WHERE id = '.(int)$id);
				if(!empty($data)) {
					$path      = '';
					$file_name = $id.'.'.$data['extension'];
					switch($type) {
						case 'photos': {
							$path = $this->config->get('images', 'files').$file_name; 
							$path_t = $this->config->get('images', 'files').'t/'.$file_name;
							break;
						}
						case 'images': {
							$path = $this->config->get('img', 'files').'b/'.$file_name; 
							$path_t = $this->config->get('img', 'files').'t/'.$file_name;
							$path_l = $this->config->get('img', 'files').'l/'.$file_name;
							break;
						}
					}
					$echo = '';
					if(file_exists($path)) {
						switch($act) {
							case 'del':
								$info = $this->db->get_row('SELECT pid, module_id FROM '.$type.' WHERE id = '.(int)$id);
								if(!empty($info)) {
									$this->db->delete($type, $id);
									unlink($path);
									unlink($path_t);
									
									if($type == 'photos') {
										// -- пересчитать "image_list" для wysiwyg
										$sql  = 'SELECT * FROM '.$type.' WHERE pid = '.$info['pid'].' AND module_id = '.$info['module_id'].' ORDER BY sort, id';
										$list = $this->db->get_all($sql);
										if(!empty($list)) {
											$c = sizeof($list);
											foreach($list as $i => &$item) {
												$item['url'] = '/application/includes/uploadIMG/'.$item['id'].'.'.$item['extension'];
												if (($i+1)<$c) $item['zap'] = ',';
											}
											
											$tiny_image = $this->html->render('lists/tiny_image.html', array('listing' => $list));
											$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, false);
										}
									} else {
										unlink($path_l);
									}
								}
								break;
							case 'wcc':
							case 'wc':
								$this->image->analyze($path);
								$this->image->rotate(($act == 'wc') ? 90 : -90);
								$this->image->toFile($path, 90);
								$this->image->analyze($path);
								$this->image->toFile($path_t, 80, 145, 145);	//Для отображения в админке
								
								switch($type) {
									case 'photos':
										$echo .= '<div class="image" style="background-image: url(\'/application/includes/uploadIMG/t/'.$file_name.'?_='.rand().'\');">';
										$echo .= '	<div class="images_listing_panel">';
										$echo .= '		<table><tr>';
										$echo .= '			<td width="20%"><input type="checkbox" class="delete_photos" name="ids" value="'.$id.'" /></td>';
										$echo .= '			<td width="20%"><a class="anchor"     href="#" title="Получить ссылку на фото" onClick="alert(\'Ссылка на фото:\n\n/application/includes/uploadIMG/'.$id.'.'.$data['extension'].'\'); return false;">&nbsp;</a></td>';
										$echo .= '			<td width="20%"><a class="rotate_wc"  href="#" title="Повернуть изображение на 90&deg; против часовой стрелки" onClick="return multi_upload_panel(\'photos\',\'wc\', '.$id.');">&nbsp;</a></td>';
										$echo .= '			<td width="20%"><a class="rotate_wcc" href="#" title="Повернуть изображение на 90&deg; по часовой стрелке" onClick="return multi_upload_panel(\'photos\',\'wcc\','.$id.');">&nbsp;</a></td>';
										$echo .= '			<td width="20%"><a class="delete"     href="#" title="Удалить изображение" onClick="return multi_upload_panel(\'photos\',\'del\','.$id.');">&nbsp;</a></td>';
										$echo .= '		</tr></table>';
										$echo .= '	</div>';
										$echo .= '</div>';
										
										break;
									case 'images':
										$image = $this->image->analyze($path);
										if(!empty($image)) {
											$w_s = $this->config->get('width_small',  'images');
											$h_s = $this->config->get('height_small', 'images');
											if($image['b_width'] > $image['b_height']) {
												$this->image->toFile($path_l, 80, $w_s, $h_s);
											} else {
												$this->image->toFile($path_l, 80, $h_s, $w_s);
											}
										}
										
										$echo  = '<div class="image" style="background-image: url(\'/application/includes/img/t/'.$file_name.'?_='.rand().'\');">';
										$echo .= '	<div class="images_listing_panel">';
										$echo .= '		<table><tr>';
										$echo .= '			<td width="20%"><input type="checkbox" class="delete_images" name="ids" value="'.$id.'" /></td>';
										$echo .= '			<td width="20%"><a class="note"       href="#" title="Редактировать подпись изображения" onClick="return multi_upload_panel(\'images\',\'note\','.$id.');">&nbsp;</a></td>';
										$echo .= '			<td width="20%"><a class="rotate_wc"  href="#" title="Повернуть изображение на 90&deg; против часовой стрелки" onClick="return multi_upload_panel(\'images\',\'wc\', '.$id.');">&nbsp;</a></td>';
										$echo .= '			<td width="20%"><a class="rotate_wcc" href="#" title="Повернуть изображение на 90&deg; по часовой стрелке" onClick="return multi_upload_panel(\'images\',\'wcc\','.$id.');">&nbsp;</a></td>';
										$echo .= '			<td width="20%"><a class="delete"     href="#" title="Удалить изображение" onClick="return multi_upload_panel(\'images\',\'del\','.$id.');">&nbsp;</a></td>';
										$echo .= '		</tr></table>';
										$echo .= '	</div>';
										$echo .= '</div>';

										// -- меняем ширину с высотой
										$dim  = $this->db->get_row('SELECT b_width, b_height FROM images WHERE id = '.$id);
										if(!empty($dim)) {
											$this->db->query('UPDATE images SET b_width = '.$dim['b_height'].', b_height = '.$dim['b_width'].' WHERE id = '.$id);
										}
										break;
								}
								break;
						}
					}
					echo $echo;
				}
			}
			die();
		}
		
		// -- групповое удаление картинок из фотогалереи
		public function delete_images() {
			if(!empty($_POST['ids'])) {
				$ids = explode(',', $_POST['ids']);
				foreach($ids as $id) {
					$path_b = $this->config->get('img','files').'b/'.$id.'.jpg';
					$path_l = $this->config->get('img','files').'l/'.$id.'.jpg';
					$path_t = $this->config->get('img','files').'t/'.$id.'.jpg';
					if(file_exists($path_b)) unlink($path_b);
					if(file_exists($path_l)) unlink($path_l);
					if(file_exists($path_t)) unlink($path_t);
					$this->db->delete('images', (int)$id);
				}
			}
			die();
		}
		
		// -- групповое удаление картинок из настраничных фото
		public function delete_photos() {
			if(!empty($_POST['ids'])) {
				$ids = explode(',', $_POST['ids']);
				
				$old_id = $ids[0];
				$old_info = $this->db->get_row('SELECT pid, module_id FROM photos WHERE id = '.(int)$old_id);
				
				foreach($ids as $id) {
					$info = $this->db->get_row('SELECT * FROM photos WHERE id = '.(int)$id);
					if(!empty($info)) {
						$path = $this->config->get('images','files').$id.'.'.$info['extension'];
						$path_t = $this->config->get('images','files').'t/'.$id.'.'.$info['extension'];
						if(file_exists($path)) unlink($path);
						if(file_exists($path_t)) unlink($path_t);
						$this->db->delete('photos', (int)$id);
					}
				}
				
				$sql  = 'SELECT * FROM photos WHERE pid = '.$old_info['pid'].' AND module_id = '.$old_info['module_id'].' ORDER BY sort, id';
				$list = $this->db->get_all($sql);
				
				if(!empty($list)) {
					$c = sizeof($list);
					foreach($list as $i => &$item) {
						$item['url'] = '/application/includes/uploadIMG/'.$item['id'].'.'.$item['extension'];
						if ( ($i+1) < $c ) $item['zap'] = ',';
					}
		
					$tiny_image = $this->html->render('lists/tiny_image.html', array('listing' => $list));

					$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, false);
				}
			}
			die();
		}
		
		// -- групповое удаление файлов
		public function delete_files() {
			if(!empty($_POST['ids'])) {
				$file_path = $this->config->get('file', 'files');
				$ids = explode(',', $_POST['ids']);
				foreach($ids as $id) {
					$path = $file_path.$this->main->getFilePath($id);
					if(file_exists($path)) @unlink($path);
					$this->db->delete('files', $id);
				}
			}
			die();
		}
		
		// -- групповое отображение файлов
		public function show_files($pid, $mid) {
			if(empty($_POST['ids'])) die();
			$ids = explode(',', $_POST['ids']);
			foreach($ids as $id) {
				$this->db->update('files', array('is_show' => 1), (int)$id);
			}
			$this->listing($pid, 'files', $mid);
		}
		
		// -- групповое скрытие файлов
		public function hide_files($pid, $mid) {
			if(empty($_POST['ids'])) die();
			$ids = explode(',', $_POST['ids']);
			foreach($ids as $id) {
				$this->db->update('files', array('is_show' => 0), (int)$id);
			}
			$this->listing($pid, 'files', $mid);
		}
		
		// -- переименовать файл из "Прикрепленные файлы"
		public function file_rename($id) {
			if(!empty($id)) {
				$this->db->update('files', array('filename'=>$_POST['filename']), $id);
			}
			die();
		}
		
		// -- отдаем подпись изображения для редактирования
		public function getnote($type, $id) {
			switch($type) {
				case 'photos': {
					echo $this->db->get_one('SELECT note FROM photos WHERE id = '.(int)$id);
					break;
				}
				case 'images': {
					echo $this->db->get_one('SELECT note FROM images WHERE id = '.(int)$id);
					break;
				}
			}
			die();
		}
		
		// -- сохряняем подпись изображения
		public function setnote($type, $id) {
			if(isset($_GET['note'])) {
				
				switch($type) {
					case 'photos': {
						$this->db->update('photos', array('note'=>$_GET['note']), $id);
						break;
					}
					case 'images': {
						$this->db->update('images', array('note'=>$_GET['note']), $id);
						break;
					}
				}
			}
			
			// -- пересчитать "image_list" для wysiwyg
			$info = $this->db->get_row('SELECT pid, module_id FROM '.$type.' WHERE id = '.(int)$id);
			$sql  = 'SELECT * FROM '.$type.' WHERE pid = '.$info['pid'].' AND module_id = '.$info['module_id'].' ORDER BY sort, id';
			$list = $this->db->get_all($sql);
			

			if(!empty($list)) {
				$c = sizeof($list);
				foreach($list as $i => &$item) {
					$item['url'] = '/application/includes/uploadIMG/'.$item['id'].'.'.$item['extension'];
					if ( ($i+1) < $c ) $item['zap'] = ',';
				}
	
				$tiny_image = $this->html->render('lists/tiny_image.html', array('listing' => $list));

				$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, false);
			}
			
			
			die();
		}
		
		public function __after() {
			header('Content-Type: text/html; charset='.$this->config->get('charset'));
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header('Expires: '.date('r'));

			if($alert = $this->session->get('alert')) {
				$this->session->del('alert');
				$this->html->tpl_vars['alert'] = '<script type="text/javascript">alert("'.$alert.'");</script>';
			}
			
			$this->html->tpl_vars['menu_block'] = $this->menu_controller->print_tree($this->active_id);
			
			//Показываем раздел если есть права
			if ($this->role_controller->CheckAccess(1))
				$this->html->tpl_vars['role_link'] = '<li><a href="/admin/role/" class="main_page_button eye_show">Администрирование</a></li>';
			if ($this->role_controller->CheckAccess(3))
				$this->html->tpl_vars['main_link'] = '<li><a href="/admin/config/main/" class="main_page_button">Главная страница</a><span class="help" rel="7"></span></li>';
			if ($this->role_controller->CheckAccess(4))
				$this->html->tpl_vars['add_btn'] = '<button onclick="window.location=\'/admin/module/add/\'">Добавить страницу</button>';
			if ($this->role_controller->CheckAccess(7))
				$this->html->tpl_vars['banners'] = '<li><a href="/admin/visban/" class="banner_button">Баннеры</a></li>';
			
			//Только для админа
			if (($role_id = $this->session->get('role_id')) === 0) {
				$this->html->tpl_vars['reserv'] = '<li><a href="/admin/backup/">Резервное копирование</a></li>';
				$this->html->tpl_vars['struct'] = '<li><a href="/admin/structure/">Структура</a><span class="help" rel="4:3"></span></li>';
				$this->html->tpl_vars['tehpodd'] = '<li><a href="/admin/config/question/">Задать вопрос в службу поддержки</a></li>';
				$this->html->tpl_vars['configs'] = '<li><a href="/admin/config/">Конфигурация</a></li>';
			}
			
			$this->html->tpl_vars['mapkey'] = $this->config->get($_SERVER['SERVER_NAME'], 'mapkeys'); // -- ключ
			$this->html->tpl_vars['title_common'] = $this->config->get('title_common', 'site');
			$this->html->tpl_vars['title_browser'] = $this->config->get('title_browser', 'site');
			
			if($this->url->get('page') !== 'login') {
				echo $this->html->render('layouts/layout.html');
			}
		}

	}
?>
<?php
	class trash_controller extends application_controller {
		
		private $trash_table;
		
		public function __construct() {
			parent::__construct();
			
			// -- массив таблиц, данные которых подлежат восстановлению 
			$this->trash_table = array(
				'main'
			); 
		}
		
		public function index() {
			$content = '<h2>Корзина</h2>';
			if(empty($this->trash_table)) {
				$content .= '<p>Модуль &laquo;Корзина&raquo; отключен</p>';
			} else {
				$trash_is_empty = true;
				foreach($this->trash_table as $i => $table) {
					
					if ($table == 'main' ) {	//Проверяем права
						$role_id = $this->session->get('role_id');
						$access = $this->role->getAccessPages($role_id);
						$trash = array('list' => $this->trash->getTrash($this->trash_table[$i], $access));
					} else 
						$trash = array('list' => $this->trash->getTrash($this->trash_table[$i]));
					
					
					if(!empty($trash['list'])) {
						$trash['name'] = $table;
						$trash['module_title'] = $this->main->getModuleTitleByName($table);
						$content .= $this->html->render('trash/list.html', $trash);
						$trash_is_empty = false;
					}
				}
				if($trash_is_empty) {
					$content .= '<p>Корзина пуста</p>';
				}
			}
			
			header('Content-Type: text/html; charset='.$this->config->get('charset'));
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header('Expires: '.date('r'));
			
			$this->html->tpl_vars['content_path'] = $content;
		}
		
		// -- удаляем безвозвратно
		public function delete($name, $id) {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			if ($role_id>0) 
				$this->session->set('rights', $this->role->getRoleRights($role_id));
			
			// -- удаляем свои файлы, если они есть
			$main_path = $this->config->get($name, 'files').$id;
			$arr_path  = array('txt','jpg','gif','png');
			foreach($arr_path as $i => $ext) {
				if(file_exists($main_path.'.'.$ext)) {
					unlink($main_path.'.'.$ext); 
				}
			}
			
			// -- ассоциативные записи в БД
			$mid = ($name == 'main') ? 1 : $this->main->getModuleIdByName($name);
			
			$this->delete_addition($id, $mid);
			
			// -- удаляем основную запись в БД
			$this->db->delete($name, $id);
			
			$this->db->delete('auth_rights', array('action_id'=>2, 'param1'=>$id) );
			
			$this->url->redirect('::referer');
		}
		
		
		//Удаление дополнительных модулей
		//Удаление дополнительных модулей
		public function delete_addition($id, $mid, $subpage = false) {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			if ($subpage) {
				$table = $this->base->getTableName($mid);
				$pid = $this->db->get_one('SELECT pid FROM '.$table.' WHERE id='.(int)$id);
				if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			} else {
				if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
				if ($role_id>0) 
					$this->session->set('rights', $this->role->getRoleRights($role_id));
			}
			
			$ass   = array('images','photos','files');
			$ass_p = array(
				'images' => array('img',array('b/','l/', 't/')),
				'photos' => array('images',array('', 't/')),
				'files'  => array('file',array(''))
			);
			foreach($ass as $ass_item) {
				$images = $this->db->get_all('SELECT id FROM '.$ass_item.' WHERE pid = '.$id.' AND module_id = '.$mid);
				if(!empty($images)) {
					foreach($images as $i => $item) {
						foreach($ass_p[$ass_item][1] as $postfix) {
							$path = $this->config->get($ass_p[$ass_item][0], 'files').$postfix.$item['id'].'.jpg';
							
							if(file_exists($path)) {
								unlink($path);
							}
						}
					}
					$this->db->delete($ass_item, array('pid'=>$id,'module_id'=>$mid));
				}
			}
			$maps = $this->db->get_all_one('SELECT id FROM maps WHERE pid = '.$id.' AND module_id = '.$mid);
			if(!empty($maps)) {
				foreach ($maps as $map) {
					$this->db->delete('maps_placemarks',array('pid'=>$map));
				}
				$this->db->delete('maps', array('pid'=>$id, 'module_id'=>$mid) );
			}
			
			$this->db->delete('search_index', array('pid'=>$id, 'module_id'=>$mid) );
		}
		
		
		// -- восстанавливаем из корзины
		public function rebuild($name, $id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$this->db->update($name, array('active' => 1), $id);
			$this->url->redirect('::referer');
		}
		
		// -- удаляем разлинкованные файлы
		public function clear() {
			$this->clear_files();
			$this->url->redirect('/'.$this->admin_dir.'/trash/');
		}
		
		public function clear_files() {
			$path_file = $this->config->get('file','files');
			$dir = dir($path_file);
			while($name = $dir->read()) {
				$path = $path_file.$name;
				if(is_file($path)) {
					$id    = explode('.', $name);
					$id    = (int)$id[0];
					$isset = (bool)$this->db->get_one('SELECT 1 FROM files WHERE id = '.$id);
					if(!$isset) {
						unlink($path);
					}
				}
			}
			$path_file = $this->config->get('images','files');
			$dir = dir($path_file);
			while($name = $dir->read()) {
				$path = $path_file.$name;
				if(is_file($path)) {
					$id    = explode('.', $name);
					$id    = (int)$id[0];
					$isset = (bool)$this->db->get_one('SELECT 1 FROM photos WHERE id = '.$id);
					if(!$isset) {
						unlink($path);
					}
				}
			}
			$path_file   = $this->config->get('img','files');
			$path_file_b = $path_file.'b/';
			$path_file_l = $path_file.'l/';
			$dir = dir($path_file_b);
			while($name = $dir->read()) {
				$path = $path_file_b.$name;
				if(is_file($path)) {
					$id    = explode('.', $name);
					$id    = (int)$id[0];
					$isset = (bool)$this->db->get_one('SELECT 1 FROM images WHERE id = '.$id);
					if(!$isset) {
						unlink($path);
					}
				}
			}
			$dir = dir($path_file_l);
			while($name = $dir->read()) {
				$path = $path_file_l.$name;
				if(is_file($path)) {
					$id    = explode('.', $name);
					$id    = (int)$id[0];
					$isset = (bool)$this->db->get_one('SELECT 1 FROM images WHERE id = '.$id);
					if(!$isset) {
						unlink($path);
					}
				}
			}
		}

	}
?>
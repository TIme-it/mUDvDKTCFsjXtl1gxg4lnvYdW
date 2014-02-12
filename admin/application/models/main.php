<?php
	class main extends app_model {
		
		// -- показывать ли раздел в меню
		public function getInMenuChecked($id) {
			$in_menu = (int)$this->db->get_one('SELECT inmenu FROM main WHERE id = '.(int)$id);
			return $in_menu ? ' checked="checked"' : '';
		}
		
		// -- список модулей
		public function getModuleList($act = 0) {
			$sql = 'SELECT id mod_id, title mod_title, IF(id = '.(int)$act.', \'selected="selected"\', "") mod_selected FROM module WHERE is_show_add <> 0 ORDER BY id';
			return $this->db->get_all($sql);
		}
		
		// -- выбираем дерево структуры сайта
		public function getDirectoriesTree() {
			$tree = array();
			$this->getDirectoriesTreeReq($tree, 0, 0);
			return $tree;
		}
		
		// -- рекурсивное вхождение в СД
		public function getDirectoriesTreeReq(&$node, $pid, $deep) {
			if($deep > 20) {
				// -- ограничение по уровню вложенности
				// -- мало ли какой хрен чего в БД ручками поправил
				return false;
			}
			$sql  = 'SELECT id, title FROM main WHERE pid = '.(int)$pid.' AND active = 1 ORDER BY tree, id, pid';
			$node = $this->db->get_all($sql);
			if(empty($node)) {
				// -- деток нету - валим нах
				return false;
			}
			foreach($node as $i => &$item) {
				$item['childs'] = array();
				$this->getDirectoriesTreeReq($item['childs'], $item['id'], $deep+1);
			}
			return true;
		}
		
		// -- получить список файлов или их кол-во для ДМ "Файлы на страницу"
		public function getFiles($pid, $module_id, $return_mode = false) {
			if($return_mode) {
				// -- вернуть только количество
				$sql = 'SELECT COUNT(*) FROM files WHERE pid = '.(int)$pid.' AND module_id = '.(int)$module_id;
				return (int)$this->db->get_one($sql);
			}
			$sql = 'SELECT * FROM files	WHERE pid = '.(int)$pid.' AND module_id = '.(int)$module_id.' ORDER BY sort, id';
			return $this->db->get_all($sql);
		}
		
		// -- получить список картинок или их кол-во для ДМ "Фото на страницу" + подготовка для wysiwyg
		public function getPhotos($pid, $module_id, $return_mode = false) {
			$sql  = 'SELECT * FROM photos WHERE pid = '.(int)$pid.' AND module_id = '.(int)$module_id.' ORDER BY sort, id';
			$data = $this->db->get_all($sql);
			if(!empty($data)) {
				foreach($data as $i => &$item) {
					$item['url'] = '/application/includes/uploadIMG/'.$item['id'].'.'.$item['extension'];
				}
				$tiny_image = $this->html->render('lists/tiny_image.html', array('listing' => $data));
				$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, true);
				return ($return_mode) ? count($data) : $data;
			}
			$tiny_image = $this->html->render('lists/tiny_image.html', array('listing' => array()));
			$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, true);
			return ($return_mode) ? 0 : array();
		}
		
		// -- получить список картинок или их кол-во для ДМ "Фотогалерея"
		public function getImages($pid, $module_id, $return_mode = false) {
			if($return_mode) {
				// -- вернуть только количество
				$sql = 'SELECT COUNT(*) FROM images WHERE pid = '.(int)$pid.' AND module_id = '.(int)$module_id;
				return (int)$this->db->get_one($sql);
			}
			$sql = 'SELECT * FROM images WHERE pid = '.(int)$pid.' AND module_id = '.(int)$module_id.' ORDER BY sort, id';
			return $this->db->get_all($sql);
		}
		
		
		// -- получить данные раздела по его id
		public function getVolumeData($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		// -- получить данные раздела по его id
		public function getInfoById($id) {
			$sql = 'SELECT pid, alias FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		// -- получить имя модуля по id раздела
		public function getModuleNameById($id) {
			$sql = 'SELECT module.name FROM main LEFT JOIN module ON module.id = main.module WHERE main.id = "'.(int)$id.'"';
			return $this->db->get_one($sql);
		}
		
		// -- получить имя модуля по id модуля
		public function getModuleNameByModuleId($id) {
			$sql = 'SELECT name FROM module WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}
		
		// -- получить имя id модуля по имени
		public function getModuleIdByName($name) {
			$sql = 'SELECT id FROM module WHERE name = '.$this->db->escape($name);
			return $this->db->get_one($sql);
		}
		
		// -- получить наименование модуля по его имени
		public function getModuleTitleByName($module_name) {
			if($module_name == 'main') {
				$module_name = 'pages';
			}
			$sql = 'SELECT title FROM module WHERE name = '.$this->db->escape($module_name);
			return $this->db->get_one($sql);
		}
		
		// -- есть ли данный модуль в системе
		public function issetModule($module_name) {
			$sql = 'SELECT 1 FROM module WHERE name = '.$this->db->escape($module_name);
			return (bool)$this->db->get_one($sql);
		}
		
		// -- построить URL (для сайта) по id из main
		public function buildURL($id) {
			$module_name = $this->getModuleNameById($id);
			$url = '';
			switch($module_name) {
				// URL создаётся по алиасам
				case 'pages':    
					do {
						$info = $this->getInfoById($id);
						$url  = $info['alias'].'/'.$url;
						$id   = $info['pid'];
					} while(!empty($info['pid']));
					break;
				// URL создаётся по id
				default:
					$url  = $id.'/';
					break;
			}
			$url = '/'.$module_name.'/'.$url;
			return $url;
		}
		
		// -- построить URL (для админки) по id из main
		public function buildAdminURL($id) {
			$module_name = $this->getModuleNameById($id);
			return '/admin/'.$module_name.'/view/'.$id.'/';
		}
		
		// -- получить имя файла по id из таблицы files
		public function getFilePath($id) {
			$sql = 'SELECT CONCAT(id,".",extension) FROM files WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}
		
	}
?>
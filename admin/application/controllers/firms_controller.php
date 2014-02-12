<?php
	class firms_controller extends application_controller {

		protected $path;
		protected $module_id = 5;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('firms','files');
		}

		public function index() {
			// -- переводим на первый повавшийся раздел, если такой есть
			$id = $this->db->get_one('SELECT id FROM main WHERE module = '.$this->module_id.' LIMIT 1');
			$this->url->redirect($id ? $this->url->redirect($this->main->buildAdminURL($id)) : '::referer');
		}

		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$page = $this->firms->getPage($id);
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			
			// -- текст страницы
			$text_path = $this->path.$id.'_volume.txt';
			if(file_exists($text_path)) {
				$page['text'] = htmlspecialchars(file_get_contents($text_path));
			}
			
			if(!$this->menu->isChilds($id)) {
				$page['delete_page'] = '<a class="trash_button" href="/admin/module/delete/'.$id.'/" '.
									   'onClick=\'if(!confirm("Вы действительно хотите удалить?")) return false;\'>Удалить страницу</a>';
			}
			
			// -- системные модули
			$page['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$page['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$page['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$page['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$page['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$page['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			// -- основной рендер
			$this->html->render('firms/view.html', $page, 'content_path');	
		}
		
		// -- редактирование данных раздела
		public function firms_edit($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$page = $this->firms->getPage($id);
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			
			// -- обновление данных раздела
			$main = array(
				'pid'         => (int)$_POST['pid'],
				'title'       => strip_tags($_POST['title']),
				'title_page'  => strip_tags($_POST['title_page']),
				'description' => strip_tags($_POST['description']),
				'keywords'    => strip_tags($_POST['keywords']),
				'inmenu'      => (int)$_POST['inmenu'],
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)(!empty($_POST['feedback'])),
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
				'subsection'  => (int)(!empty($_POST['subsection'])),
			);
			$this->db->update('main', $main, $id);
			
			// -- сохранение текста
			$this->file->toFile($this->path.$id.'_volume.txt', $_POST['text']);
			
			// -- особая ситуация: изменение основного модуля
			if((int)$_POST['module_id'] !== $this->module_id) {
				$this->module_controller->changeModule($id, $this->module_id, (int)$_POST['module_id'], $_POST['link']);
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}
		
		
		//Список клиентов
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$page = $this->firms->getPage($id);
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			
			$page['pid']        = $id;
			$page['admin_dir']  = $this->config->get('admin_dir', 'system');
			$page['module'] = 'firms';
			
			//Кол-во клиентов на странице
			$page['firms_count'] = $this->config->get('firms_count', 'site');
			
			$all_count = $this->firms->getCount($id);
			$page_count = ceil($all_count / $page['firms_count']);
			if ($page_count > 1) {
				$navigate['navigate'] = array();
				for ($i=0;$i<$page_count;$i++)
					$navigate['navigate'][] = array('num' => ($i+1), 'module_name' => 'firms');
				$navigate['navigate'][0]['class'] = 'act';
				$page['nav'] = $this->html->render('layouts/navigate.html', $navigate);
			}
			
			// -- список клиентов
			$page['list'] = $this->firms->getListPart($id, 1, $page['firms_count']);
			if(!empty($page['list'])) {
				foreach($page['list'] as $i => &$item) {
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
				}
				$page['html'] = $this->html->render('firms/view_list.html', $page);
			}
			
			$this->html->render('firms/view_items.html', $page, 'content_path');
		}
		
		
		//Список клиентов Ajax
		public function ajax_navigate($pid = false, $page = 1) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			//Кол-во услуг на странице
			$firm['firms_count'] = $this->config->get('firms_count', 'site');
			$firm['module'] = 'firms';
			
			// -- список услуг
			$firm['list'] = $this->firms->getListPart($pid, $page, $firm['firms_count']);
			if(!empty($firm['list'])) {
				foreach($firm['list'] as $i => &$item) {
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
				}
				$firm['html'] = $this->html->render('firms/view_list.html', $firm);
				die(json_encode($firm['html']));
			}
			die();
		}
		
		
		//Добавление/изменение клиента
		public function add_item($pid = false, $id = false) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			// -- данные раздела
			$page = $this->firms->getPage($pid);
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			
			// -- данные организации
			$item = $this->firms->getItem($id);
			if(empty($item)) {
				$data['id']      = 0;
				$data['header'] = $page['title'];
				$data['add_header'] = ' / Новая организация';
				
				$data['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
			} else {
				$data = $item;
				$data['header'] = $page['title'];
				$data['add_header'] = ' / '.$item['title'];

				// -- текст страницы
				if(file_exists($this->path.$item['id'].'.txt')) {
					$data['text'] = file_get_contents($this->path.$item['id'].'.txt');
				}
				
				// -- картинка
				$data['character'] = (file_exists($this->path.$id.'.jpg'))?'<a href="/application/includes/firms/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/firms/deleteImg/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
				
				$data['delete_firm'] = '<a href="/'.$this->admin_dir.'/firms/delete_item/'.$id.'/" class="trash_button" '.
								   'onClick="if(!confirm(\'Вы действительно хотите удалить эту организацию?\')) return false;">Удалить организацию</a>';
				
				$data['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
				
				// -- системные модули
				$data['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
				
				// -- дополнительные модули
				$data['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
				$data['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
				$data['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			}
			
			$data['pid'] = $page['id'];
			
			// -- основной рендер
			$this->html->render('firms/add_item.html', $data, 'content_path');	
		}
		
		
		//Сохранение клиента
		public function edit_item() {
			$id = (int)$_POST['id'];
			$pid = (int)$_POST['pid'];
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			// -- данные раздела
			$page = $this->firms->getPage($pid);
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			
			// -- данные организации в БД
			$firm = array(
				'pid'         => (int)$pid,
				'title'       => strip_tags($_POST['title']),
				'note'        => strip_tags($_POST['note']),
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)(!empty($_POST['feedback'])),
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
			);
			
			if(empty($id)) {
				$id = $this->db->insert('firms', $firm);
			} else {
				$this->db->update('firms', $firm, $id);
			}
			
			// -- текст организации
			$this->file->toFile($this->path.$id.'.txt', $_POST['text']);
			
			// -- картинка
			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, 175, 175);
					}
				}
			}
			
			$this->url->redirect('/admin/firms/view_items/'.$pid.'/');
		}
		
		
		// -- удаление одной записи
		public function delete_item($id) {
			$pid = $this->delete($id);
			
			$this->url->redirect('/admin/firms/view_items/'.$pid.'/');
		}
		
		// -- групповое удаление
		public function group_delete() {
			if(!empty($_POST['ids'])) {
				foreach($_POST['ids'] as $id) {
					$this->delete($id);
				}
			}
			$this->url->redirect('::referer');
		}
		
		private function delete($id) {
			//Проверяем права
			$pid = $this->db->get_one('SELECT pid FROM firms WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			if (file_exists($this->path.$id.'.txt')) unlink($this->path.$id.'.txt');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->trash_controller->delete_addition($id, $this->module_id, true);
			
			$this->db->delete('firms', (int)$id);
			
			return $pid;
		}
		
		// -- удалить хар. картинку
		public function deleteImg($id) {
			$pid = $this->db->get_one('SELECT pid FROM firms WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->session->set('alert', ALERT_DEL_IMAGE);
			$this->url->redirect('::referer');
		}
	}
?>
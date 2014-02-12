<?php
	class personal_controller extends application_controller {
		
		protected $path;
		protected $module_id = 9;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('personal', 'files');
		}
		
		// -- общая страница для всего персонала
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$personal = $this->personal->getInfoAboutPersonal($id);
			$personal[$personal['template'].'Selected'] = 'selected="selected"';

			// -- текст страницы
			if(file_exists($this->path.$id.'_volume.txt')) {
				$personal['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			if(file_exists($this->path.$id.'_main.jpg')) {
				$personal['viewMainImg']   = '<a href="/application/includes/personal/'.$id.'_main.jpg" target="_blank">Смотреть</a>';
				$personal['deleteMainImg'] = '<a href="/admin/personal/deleteMainImg/'.$id.'">Удалить</a>';
			}
			
			$personal['per_list'] = $personal['list'] = $this->personal->getPersonal($id);
			$personal['delete']   = '<a onclick="return confirm(\'Вы действительно хотите удалить этот раздел?\');" href="/admin/module/delete/'.$id.'/" class="trash_button">Удалить страницу</a>';

			// -- системные модули
			$personal['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$personal['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$personal['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$personal['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$personal['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$personal['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			// -- основной рендер
			$this->html->render('personal/view.html', $personal, 'content_path');
		}
		
		// -- редактирование общей информации о разделе
		public function edit_main() {
			if(!empty($this->data)) {
				$this->data['main']['pid']        = (int)$_POST['pid'];
				$this->data['main']['inmenu']     = (int)$_POST['inmenu'];
				$this->data['main']['sendfile']   = (int)(!empty($_POST['sendfile']));
				$this->data['main']['print']      = (int)(!empty($_POST['print']));
				$this->data['main']['feedback']   = (int)$_POST['feedback'];
				$this->data['main']['subsection'] = (int)(!empty($_POST['subsection']));
				
				$id = $this->main->save($this->data);
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			// -- сохранение конфига
			$config['site'] = array(
				'print_word'     => $_POST['print_word'],
				'feedback_email' => $_POST['feedback_email'],
			);
			$this->config_controller->add_config($config);
			
			// -- картинка компании
			if(isset($_FILES['mainImg']['tmp_name']) && file_exists($_FILES['mainImg']['tmp_name'])) {
				$image = $this->image->analyze($_FILES['mainImg']['tmp_name']);
				if(!empty($image)) {
					$this->image->toFile($this->path.$id.'_main.jpg', 80, 540, 405);
				}
			}
			
			// -- текст страницы
			$this->file->toFile($this->path.$id.'_volume.txt', $_POST['text']);
			
			// -- особая ситуация: изменение основного модуля
			if((int)$_POST['module_id'] !== $this->module_id) {
				$this->module_controller->changeModule($id, $this->module_id, (int)$_POST['module_id'], $_POST['link']);
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}
		
		// -- страница конкретной персоны
		public function view_one($pid, $id = false) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			$info = $this->personal->getInfo($pid);
			if(empty($info)) {
				$this->url->redirect('::referer');
			}
			
			if(!empty($id)) {
				$personal = $this->personal->getOnePerson($id);
				$img_path = $this->path.$id.'.jpg';
				if(file_exists($img_path)) {
					$personal['person_img'] = 
					'<a href="/application/includes/personal/'.$id.'.jpg" target="_blank">Смотреть</a> '.
					'<a onClick="if(!confirm("Вы действительно хотите удалить изображение?")) return false;" href="/admin/personal/deleteImg/'.$id.'/">Удалить</a>';
				}
				$personal['departments'] = $this->personal->getDepartments($pid);
				if(!empty($personal['departments'])) {
					foreach($personal['departments'] as $i => &$item) {
						if($personal['department'] == $item['dep_title']) {
							$item['selected'] = 'selected="selected"';
						}
					}
				}
				
				$dir_path = $this->path.$pid.'.ser';
				if(file_exists($dir_path)) {
					$direktor_data = unserialize(file_get_contents($dir_path));
					$personal['direktorWord'] = $direktor_data['word'];
					if(!empty($direktor_data['id']) && $direktor_data['id'] == $id) {
						$personal['checked_is_direktor'] = 'checked="checked"';
						$personal['direktor_word_show']  = 'style="display: block;"';
					}
				}
				
				// -- системные модули
				$personal['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
							
				// -- дополнительные модули
				$personal['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
				$personal['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
				$personal['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			} else {
				$personal = array(
					'id'    => 0,
					'pid'   => $info['id'],
					'title' => $info['title'],
					'departments' => array()
				);
			}
			
			// -- системные модули
			$personal['modules_block'] = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
			
			$this->html->render('personal/add_person.html', $personal, 'content_path');
		}
		
		// -- редактирование конкретной персоны
		public function add_person() {
			$pid = $this->data['personal']['pid'];
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			
			if(empty($this->data['personal']['department']) && !empty($_POST['new_department'])) {
				$this->data['personal']['department'] = $_POST['new_department'];
			}
			$this->data['personal']['print']      = (int)(!empty($_POST['print']));
			$this->data['personal']['feedback']   = (int)$_POST['feedback'];
			$this->data['personal']['sendfile']   = (int)(!empty($_POST['sendfile']));

			$id = $this->personal->save($this->data);

			// -- портрет
			if(!empty($_FILES['person']['tmp_name']) && file_exists($_FILES['person']['tmp_name'])) {
				$image = $this->image->analyze($_FILES['person']['tmp_name']);
				if(!empty($image)) {
					$this->image->toFile($this->path.$id.'_b.jpg', 80, 200, 0);
					$this->image->toFile($this->path.$id.'.jpg', 80, 60, 79);
				}
			}
			
			
			// -- информация о директоре
			$direktor = array('id' => 0, 'word' => '');
			if(!empty($_POST['is_direktor'])) {
				$direktor = array(
					'id'   => $id,
					'word' => trim($_POST['direktor_word']),
				);
			}
			file_put_contents($this->path.$pid.'.ser', serialize($direktor));
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/admin/personal/view/'.$pid.'/');
		}
		
		// -- удалить изображение компании
		public function deleteMainImg($id) {
			$path = $this->path.$id.'_main.jpg';
			if(file_exists($path)) {
				unlink($path);
			}
			
			$this->session->set('alert', ALERT_DEL_IMAGE);
			$this->url->redirect('::referer');
		}
		
		// -- удалить портрет
		public function deleteImg($id) {
			$path = $this->path.$id.'.jpg';
			if(file_exists($path)) unlink($path);
			$path = $this->path.$id.'_b.jpg';
			if(file_exists($path)) unlink($path);
			
			$this->session->set('alert', ALERT_DEL_IMAGE);
			$this->url->redirect('::referer');
		}
		
		// -- удалить персону
		public function delete($id) {
			//Проверяем права
			$pid = $this->db->get_one('SELECT pid FROM personal WHERE id='.(int)$id);
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			$this->db->delete('personal',$id);
			if(file_exists($this->path.$id.'.jpg'))
				unlink($this->path.$id.'.jpg');
			$this->url->redirect('::referer');
		}
	}
?>
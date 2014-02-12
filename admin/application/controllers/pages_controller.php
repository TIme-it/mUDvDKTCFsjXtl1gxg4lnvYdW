<?php
	class pages_controller extends application_controller {

		protected $path;
		protected $module_id = 1;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('texts','files');
		}

		// -- "пустой" метод
		public function index() {
			// -- переводим на первый повавшийся раздел, если такой есть
			$catalog_id = $this->db->get_one('SELECT id FROM main WHERE module = '.$this->module_id.' LIMIT 1');
			if($catalog_id) {
				$this->url->redirect($this->main->buildAdminURL($catalog_id));
			}
			$this->url->redirect('::referer');
		}

		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$page = $this->main->getVolumeData($id);
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			
			// -- текстовый файл
			if(file_exists($this->path.$id.'.txt')) {
				$page['text'] = htmlspecialchars(file_get_contents($this->path.$id.'.txt'));
			}
			
			if (isset($page['text']))
			{
				$page['image']=$page['text'];
		
			
			$page['image']=preg_replace('#(src.{7}http://www.trios.ru/Pages/)#','src="/application/includes/pagesImg/',$page['text']);
			//preg_match_all('/(.{4}a href.{7}http://www.trios.ru/Pages/(.*)a)/', $page['text'], $buf);
			preg_match_all('/.{4}a.href.{10,30}trios(.*).lt.\/a.{4}/Uix', $page['text'], $buf);
			//var_dump($buf);
			$page['timage']='';

				foreach($buf[0] as $ke=>$item)
				//var_dump($buf);
							$page['timage'].=$ke.')'.$item; 
				$page['text']=$page['image'];
				$page['image']='';
			}
			// -- формирование данных
			$page['url']            = 'http://'.$this->config->get('domain', 'site').$page['url'];
			$page['module_id']      = $this->module_id;
			$page['module_name']    = 'main';
			$page['date']           = date('d-m-Y',(empty($page['date']) ? time() : $page['date']));
			$page['title']          = htmlspecialchars($page['title']);
			$page['source']         = htmlspecialchars($page['source']);
			$page['title_page']     = htmlspecialchars($page['title_page']);
			$page['feedback_checked'] = ($page['feedback'] == 1) ? 'checked="checked"': false;
			
			if(!$this->menu->isChilds($id)) {
				$page['delete_page'] = '<a class="trash_button" href="/'.$this->admin_dir.'/module/delete/'.$id.'/" '.
									   'onClick=\'if(!confirm("Вы действительно хотите удалить?")) return false;\'>Удалить страницу</a>';
			}

			// -- показывать дату
			if(!empty($page['is_show_date'])) {
				$page['is_show_date'] = 'checked="checked"';
			} else {
				$page['date_disabled'] = 'disabled="disabled"';
			}
			
			// -- системные модули
			$page['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$page['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			$page['modules_block']     = $this->all_controller->generateModulesBlock($id, 1);
			
			// -- дополнительные модули
			$page['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
			$page['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
			$page['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			//$page['videos_block']      = $this->addmodules_controller->generateVideosBlock($id, $this->module_id);
			
			// -- основной рендер
			$this->html->render('pages/view.html', $page, 'content_path');	
			unset($page);
		}
		
		public function add_page() {
			$id  = (int)$_POST['id'];
			$mid = (int)$_POST['module_id'];

			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			// -- формирование данных для таблицы main
			$main = array(
				'pid'          => (int)$_POST['pid'],
				'title'        => strip_tags($_POST['title']),
				'title_page'   => strip_tags($_POST['title_page']),
				'description'  => strip_tags($_POST['description']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'keywords'     => strip_tags($_POST['keywords']),
				'inmenu'       => (int)(!empty($_POST['inmenu'])),
				'date'         => strtotime($_POST['date']),
				'is_show_date' => (int)($_POST['is_show_date']),
				'source'       => strip_tags($_POST['source']),
				'sendfile'     => (int)(!empty($_POST['sendfile'])),
				'print'        => (int)(!empty($_POST['print'])),
				'feedback'     => (int)$_POST['feedback'],
				'subsection'   => (int)(!empty($_POST['subsection']))
			);
			
			if($mid !== $this->module_id) {
				$link = (!empty($_POST['link'])) ? trim($_POST['link']) : '';
				$this->module_controller->changeModule($id, $this->module_id, $mid, $link);
			} else {
				// -- формирование данных для таблицы поиска
				$search = array(
					'pid'       => $id,
					'module_id' => $this->module_id,
					'title'     => $_POST['title'],
					'text'      => $_POST['text'],
				);
				$this->search->saveIndex($search);

				
				$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
				
				// -- сохранение контента
				$this->file->toFile($this->path.$id.'.txt', $text);
			}
			
			$this->db->update('main', $main, $id);
			
			// -- перестраиваем url для main согласно новому pid
			$this->db->update('main', array('url' => $this->main->buildURL($id)), $id);

			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}
		
		public function delete($table,$id,$pid,$module_id = 1,$flag = true) {
			if($table === 'images') {
				if($flag) {
					unlink($this->config->get('img','files').'l'.DS.$id.'.jpg');
					unlink($this->config->get('img','files').'b'.DS.$id.'.jpg');
					unlink($this->config->get('img','files').'t'.DS.$id.'.jpg');
				}
				$this->db->delete($table, $id);
				$result = 'images';
			} elseif($table === 'photos') {
				if($flag) {
					unlink($this->config->get('images','files').$id.'.jpg');
				}
				$this->db->delete($table, $id);
				$result = 'photos';
			} elseif($table === 'files') {
				$file = $this->page->getFile($id);
				unlink($this->config->get('file','files').$id.'.'.$file['extension']);
				$this->db->delete($table,$id);
				$result = 'files';
			}
			echo $result;
			die();
		}
		
		public function changeField($table,$id) {
			if(!empty($_POST)) {
				$this->db->update($table,$_POST,$id);
			}
		}
		
	}
?>
<?php
	class faq_controller extends application_controller {
		
		protected $module_id = 4;
		protected $path;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('faq', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$faqs = $this->faq->getInfo($id);
			if(empty($faqs)) {
				$this->url->redirect('::referer');
			}
			
			// -- текст страницы
			if(file_exists($this->path.$id.'_volume.txt')) {
				$faqs['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- конфиг
			$faqs['config'] = unserialize($faqs['config']);
			$faqs['checked_send_notice'] = (!empty($faqs['config']['send_notice'])) ? ' checked="checked"' : '';
			$faqs['checked_out_valid']   = (!empty($faqs['config']['out_valid']))   ? ' checked="checked"' : '';
			//$faqs['template']            = (!empty($faqs['config']['template']))    ? $faqs['config']['template'] : 'layoutFaq';
			$faqs[$faqs['template'].'Selected'] = 'selected="selected"';
			
			$faqs['admin_dir'] = $this->config->get('admin_dir','system');
			$faqs['faq_count'] = $count = $this->config->get('faq_count','site');
			$faqs['pid']  = $id;

			
			// -- системные модули
			$faqs['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$faqs['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$faqs['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$faqs['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$faqs['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$faqs['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			// -- основной рендер
			$this->html->render('faqs/view.html', $faqs, 'content_path');
		}
		
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$faqs = $this->faq->getInfo($id);
			if(empty($faqs)) {
				$this->url->redirect('::referer');
			}
			
			$faqs['pid']  = $id;
			$faqs['main_title'] = $this->db->get_one('SELECT title main_title FROM main WHERE id='.(int)$id);
			$faqs['faq_count'] = $count = $this->config->get('admin_faq_count','site');
			
			// -- список вопросов
			$faqs['list'] = $this->faq->getFaqs($id);
			if(!empty($faqs['list'])) {
				$gid = 0;
				$faqs_screen = array();
				foreach($faqs['list'] as $i => &$item) {
					// -- форматируем дату
					if(mb_strlen($item['question'], 'utf-8') > 50) {
						$item['question'] = mb_substr($item['question'], 0, 47, 'utf-8').'...';
					}
					if(empty($item['answer'])) {
						$item['fioUser']   = '<b>'.$item['fioUser'];
						$item['question'] .= '</b>';
					}
					$item['dateQuestion'] = join('.',array_reverse(explode('-',substr($item['dateQuestion'],0,10)))).substr($item['dateQuestion'],10,6);
					$faqs_screen[$gid]['list'][] = $item;
					if(count($faqs_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($faqs_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$faqs['list_screen'][] = array('html' => $this->html->render('faqs/view_list.html', $item));
					$faqs['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($faqs['list']);
				unset($faqs_screen);
			}
			
			if(!empty($faqs['nav_screen']) && count($faqs['nav_screen']) > 1) {
				$this->html->render('faqs/nav.html',  $faqs, 'nav');
			}
			
			$this->html->render('faqs/view_items.html', $faqs, 'content_path');
		}
		
		
		// -- редактируем вопрос по id
		public function edit($id) {
			$faq = $this->faq->getOne($id);
			if(empty($faq)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $faq['pid'])) $this->role_controller->AccessError();
			
			$this->active_id = $faq['pid'];
			
			$faq['main_title'] = $this->db->get_one('SELECT title main_title FROM main WHERE id='.(int)$faq['pid']);
			
			$timestamp     = strtotime($faq['dateQuestion']);
			$faq['date']   = date('Y-m-d', $timestamp);
			$faq['date_h'] = date('H', $timestamp);
			$faq['date_m'] = date('i', $timestamp);

			$timestamp = (empty($faq['dateAnswer'])) ? time() : strtotime($faq['dateAnswer']);
			$faq['date_a']   = date('Y-m-d', $timestamp);
			$faq['date_a_h'] = date('H', $timestamp);
			$faq['date_a_m'] = date('i', $timestamp);
			
			if(!empty($faq['active'])) {
				$faq['active_checked'] = ' checked="checked"';
			}
			$faq['admin_dir'] = $this->config->get('admin_dir','system');
			
			$faq['delete_page'] = '<a class="trash_button" href="/'.$this->admin_dir.'/faq/delete/'.$id.'/" '.
									   'onClick=\'if(!confirm("Вы действительно хотите удалить?")) return false;\'>Удалить вопрос</a>';
			
			$this->html->render('faqs/edit.html', $faq, 'content_path');
		}
		
		public function answer() {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $_POST['pid'])) $this->role_controller->AccessError();
			
			$_POST['active']       = (int)(!empty($_POST['active']));
			$_POST['dateQuestion'] = $this->date->sql_format($_POST['date'].' '.$_POST['date_h'].':'.$_POST['date_m'].':00', true);
			$_POST['dateAnswer']   = $this->date->sql_format($_POST['date_a'].' '.$_POST['date_a_h'].':'.$_POST['date_a_m'].':00', true);
			unset($_POST['date']);   unset($_POST['date_a']);
			unset($_POST['date_h']); unset($_POST['date_a_h']);
			unset($_POST['date_m']); unset($_POST['date_a_m']);
			
			if(!empty($_POST['active'])) {
				$data = $this->db->get_row('SELECT feedback, dateQuestion FROM faq WHERE id = '.(int)$_POST['id']);
				if($data['feedback'] == 1) {
					$data = array_merge($_POST, $data);
					$data['domain'] = $this->config->get('domain', 'site');
					$data['dateQuestion'] = $this->date->format($data['dateQuestion']);
					$data['dateAnswer']   = $this->date->format($data['dateAnswer']);
					$letter = $this->html->render('letters/faq_user.html', $data);
					$this->mail->send_mail($data['email'], $letter);
					$_POST['feedback'] = 2;
				}
			}
			$this->db->update('faq', $_POST, $_POST['id']);
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('::referer');
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем в конфиг
			$faq_count = ((int)$_POST['faq_count'] < 1 || (int)$_POST['faq_count'] > 100) ? 10 : (int)$_POST['faq_count'];
			$this->config_controller->add_config(array('site'=>array('faq_count'=>$faq_count)));
			
			// -- сохраняем параметры раздела faq
			$main = array(
				'pid'         => (int)$_POST['pid'],
				'title'       => $_POST['title'],
				'title_page'  => $_POST['title_page'],
				'description' => $_POST['description'],
				'keywords'    => $_POST['keywords'],
				'template'    => $_POST['template'],
				'config'      => serialize($_POST['config']),
				'inmenu'      => (int)$_POST['inmenu'],
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)$_POST['feedback'],
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
				'subsection'  => (int)(!empty($_POST['subsection'])),
			);
			$this->db->update('main', $main, $id);
			
			// -- сохраняем текст страницы
			$this->file->toFile($this->path.$id.'_volume.txt', $_POST['text']);
			
			// -- особая ситуация: изменение основного модуля
			if((int)$_POST['module_id'] !== $this->module_id) {
				$this->module_controller->changeModule($id, $this->module_id, (int)$_POST['module_id'], $_POST['link']);
			}
		
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}
		
		// -- одиночное удаление
		public function delete($id) {
			$pid = $this->db->get_one('SELECT pid FROM faq WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			$this->db->delete('faq', (int)$id);
			$this->url->redirect('::referer');
		}
		
		// -- групповое удаление
		public function group_delete() {
			if(!empty($_POST['ids'])) {
				$pid = $this->db->get_one('SELECT pid FROM faq WHERE id = '.(int)$_POST['ids'][0]);
				if(empty($pid)) {
					$this->url->redirect('::referer');
				}
				
				//Проверяем права
				if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
				
				foreach($_POST['ids'] as $id) {
					$this->db->delete('faq', (int)$id);
				}
			}
			$this->url->redirect('::referer');
		}
		
	}
?>
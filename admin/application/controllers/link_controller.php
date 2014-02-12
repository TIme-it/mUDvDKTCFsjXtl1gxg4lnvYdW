<?php
	class link_controller extends application_controller {
		
		protected $module_id = 6;
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$page = $this->main->getInfo($id);
			if(empty($page)) {
				$this->url->redirect('::referer');
				return false;
			}
			
			$page['page_link']   = $page['link'];
			$page['admin_dir']   = $this->config->get('admin_dir', 'system');
			
			// -- системные модули
			$page['settings_block'] = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			$this->html->render('link/view.html', $page, 'content_path');
		}
		
		public function edit($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			$main = array(
				'pid'    => (int)$_POST['pid'],
				'title'  => trim($_POST['title']),
				'link'   => trim($_POST['link']),
				'inmenu' => (int)$_POST['inmenu'],
			);
			$this->db->update('main', $main, $id);
			
			// -- особая ситуация: изменение основного модуля
			if((int)$_POST['module_id'] !== $this->module_id) {
				$this->module_controller->changeModule($id, $this->module_id, (int)$_POST['module_id'], $_POST['link']);
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}
		
	}
?>
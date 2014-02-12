<?php
	class module_controller extends application_controller {
		
		public function add() {
			if (!$this->role_controller->CheckAccess(4)) $this->role_controller->AccessError();
			
			if(!empty($_POST)) {
				$module_id = (int)$_POST['module'];
				// -- формирование данных для main
				$main = array(
					'title'      => trim($_POST['title']),
					'title_page' => trim($_POST['title']),
					'alias'      => trim(str_replace(array('«','»'),'',$_POST['alias'])),
					'active'     => (int)(!empty($_POST['active'])),
					'inmenu'     => (int)(!empty($_POST['inmenu'])),
					'pid'        => (int)$_POST['pid'],
					'module'     => $module_id,
				);
				$id = $this->db->insert('main', $main);
				$this->db->update('main', array('url' => $this->main->buildURL($id)), $id);
				
				//Добавляем права для страницы
				$role_id = $this->session->get('role_id');
				if ($role_id>0) {
					$data = array(
						'role_id' => $role_id,
						'action_id' => 2,
						'param1' => $id
					);
					$this->db->insert('auth_rights', $data);
					$this->session->set('rights', $this->role->getRoleRights($role_id));
				}
				
				// -- уникальные действия для каждого модуля
				switch($module_id) {
					case 6: // link
						$link = array(
							'link' => trim($_POST['link']),
							'url'  => trim($_POST['link']),
						);
						$this->db->update('main', $link, $id);
						break;
				}
				
				// -- переходим в только что созданный раздел
				$this->url->redirect($this->main->buildAdminURL($id));
			}
			
			$role_id = $this->session->get('role_id');
			$access = ($role_id>0) ? $this->role->getAccessPages($role_id) : array();
			
			// -- отображаем раздел добавления страницы
			$module['modules']        = $this->menu->getModules('add');
			$module['active_checked'] = 'checked = "checked"';
			$module['inmenu_checked'] = 'checked = "checked"';
			$module['action']         = 'add';
			$module['directories']    = $this->get_structure(0,0,false, $access);
			
			$this->html->render('module/add.html',$module,'content_path');
		}
		
		public function changeModule($id, $old_mid, $new_mid, $link = '') {
			if(empty($id) || empty($old_mid) || empty($new_mid)) return false;
			$main['module'] = $new_mid;
			$main['link']   = '';
			// -- уникальные действия для изменения на новый модуль
			switch($new_mid) {
				case 6:	$main['link'] = trim($link); break;
			}
			$this->db->update('main', $main, $id);
			$this->db->update('main', array('url' => $this->main->buildURL($id)), $id);
			// -- удаление данные предыдущего модуля
			switch($old_mid) {
				case  1:
				case  6:
					break;
				default: $this->db->delete($this->main->getModuleNameByModuleId($old_mid), array('pid' => $id));
			}
			return true;
		}
		
		// -- универсальный метод удаления одной записи в БД
		public function delete($id, $table = 'main') {
			if($table == 'pages') {
				$table == 'main';
			}
			
			//Проверяем права и удаляем права на эту страницу
			if ($table == 'main') {
				if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
				$this->db->delete('auth_rights', array('action_id'=>2, 'param1'=>$id));
			}
			
			$this->db->update($table, array('active' => 0), $id);
			
			if($table == 'main') {
				// -- если удаляем раздел, то редирект корзину
				$this->url->redirect('/admin/trash/');
			}
			$this->url->redirect('::referer');
		}
		
		/*public function rebuild($id) {			
			$this->db->update('main', array('active' => 1), $id);
		}*/
		
		public function exist_alias($alias,$action) {
			$rows = $this->module->is_alias($alias);
			die((!$rows || $rows == 1 && $action == 'edit') ? 'false' : 'true');
		}
		
		// -- формируем select
		public function get_structure($pid = 0,$level = 0,$return = false, $access = array()) {
			$newStruct = array();
			$structure = $this->menu->getDirectories2($pid, $access);
			$nbsp = str_repeat('&mdash;', $level+1).'&nbsp;';
			if(!empty($structure)) {
				foreach($structure as $key=>&$val) {
					$structure[$key]['title'] = $nbsp.$structure[$key]['title'];
					if($this->menu->isChilds($structure[$key]['id']) && (!empty($access)) && (in_array($structure[$key]['id'], $access))) {
						$newStruct[] = $structure[$key];
						$struct = $this->get_structure($structure[$key]['id'],$level+1,true, $access);
						$newStruct = array_merge($newStruct,$struct);
					} else 
						$newStruct[] = $structure[$key];
				}
			}
			return $newStruct;
		}
	}
?>
<?php
	/*
		TODO: права на вложенные страницы
	*/
	
	class role_controller extends application_controller {
		
		//Список пользователей и ролей
		public function index() {
			if (!$this->CheckAccess(1)) $this->AccessError();
			
			$role_id = $this->session->get('role_id');
			if ($role_id > 0) $role['self'] = 1;
			
			//Список пользователей
			$user_id = $this->session->get('admin');
			$role['users'] = $this->role->getSubUsers($user_id);
			if (!empty($role['users'])) {
				foreach ($role['users'] as &$v) {
					if ($v['role_id'] == 0) $v['role'] = '&nbsp;нет роли';
					else $v['role'] = '<a href="/admin/role/role/'.$v['role_id'].'/" style="margin-left: 3px;">'.$v['title'].'</a>';
					
					if ($v['pid'] == 0) $v['creator'] = '&nbsp;Главный администратор';
					else $v['creator'] = '<a href="/admin/role/user/'.$v['pid'].'/" style="margin-left: 3px;">'.$v['creator'].'</a>';
				}
			} else $role['message'] = '<div style="margin:5px 0 5px 14px;">Пользователей нет</div>';
			
			//Список ролей
			$role['roles'] = $this->role->getSubRoles($role_id);
			if (empty($role['roles'])) 
				$role['message2'] = '<div style="margin:5px 0 5px 14px;">Ролей нет</div>';
			else {
				$counts = $this->role->getRoleUserCount();
				foreach ($role['roles'] as &$v) {
					$v['count'] = $counts[$v['id']];
					
					if ($v['pid'] == 0) $v['creator'] = '&nbsp;Главный администратор';
					else $v['creator'] = '<a href="/admin/role/role/'.$v['pid'].'/" style="margin-left: 3px;">'.$v['creator'].'</a>';
				}
			}
			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/view.html', $role);
		}
		
		//Вызов ошибки прав
		public function AccessError() {
			$this->session->set('alert','У вас нет прав на эту операцию');
			$this->url->redirect('::referer');
			die();
		}
		
		//Проверка можно ли сделать действие
		public function CheckAccess($action, $param1 = false) {
			$user_id = $this->session->get('admin');
			if ($user_id === 0) return true;	//Если главный - всё можно
			
			$rights = $this->session->get('rights');
			if (empty($rights)) {				//Если вообще нет прав, то выходим
				return false;
			}
			
			if (empty($param1)) {				//Если параметр не указан - значит он не имеет значения и можно проверить только действие
				return (isset($rights[$action]));
			} else {							//Иначе проверяем действие с параметром
				return (isset($rights[$action][$param1]));
			}
		}
		
		//Добавление пользователя
		public function add_user() {
			if (!$this->CheckAccess(1)) $this->AccessError();
			
			if (!empty($_POST)) {
				$login = empty($_POST['login']) ? '' : $_POST['login'];
				$pass = empty($_POST['pass']) ? '' : $_POST['pass'];
				$pass2 = empty($_POST['pass2']) ? '' : $_POST['pass2'];
				
				if ( $pass != $pass2 ) {
					$this->session->set('alert', 'Пароли не совпадают!');
					$this->url->redirect('::referer','/admin/role/');
				}
				
				$login = mb_strtolower(strip_tags($login), 'UTF-8');
				$pass = md5( 'egi'.mb_strtolower($pass, 'UTF-8').'pet' );
				
				$data = array(
					'login'=>$login,
					'pid' => $this->session->get('admin'),
					'pass'=>$pass,
					'role_id' => empty($_POST['role_id']) ? 0 : (int)$_POST['role_id'],
					'email' => trim(mb_strtolower($_POST['email'],'UTF-8')),
				);
				
				if ($this->role->HasLogin($login) == false) {
					$id = $this->db->insert('auth_user', $data);
					if ($id) {	
						$this->url->redirect('/admin/role/');
					} else $this->session->set('alert', 'Ошибка добавления');
				} else $this->session->set('alert', 'Логин занят!');
				
				$this->url->redirect('::referer','/admin/role/');
			}
			$role_id = $this->session->get('role_id');
			$user['roles'] = $this->role->getSubRoles($role_id);
			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/add_user.html', $user);
		}
		
		
		//Редактирование пользователя
		public function user($id = false) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			$user_id = $this->session->get('admin');
			$users = $this->role->getSubUsers($user_id, true);
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
			if (($id != $user_id) && !in_array($id, $users)) $this->AccessError();
			
			if (!empty($_POST)) {
				if (!$id) {
					$this->session->set('alert', 'Ошибка добавления');
					$this->url->redirect('::referer','/admin/role/');
				}
				
				$login = empty($_POST['login']) ? '' : $_POST['login'];
				$email = empty($_POST['login']) ? '' : trim(mb_strtolower($_POST['email'],'UTF-8'));
				$pass = empty($_POST['pass']) ? '' : $_POST['pass'];
				$pass2 = empty($_POST['pass2']) ? '' : $_POST['pass2'];
				
				$login = mb_strtolower(strip_tags($login), 'UTF-8');
				
				$data = array(
					'login'=>$login,
					'email'=>$email,
					'role_id' => empty($_POST['role_id']) ? 0 : (int)$_POST['role_id']
				);
				
				if (!empty($pass)) {
					if ($pass != $pass2) {
						$this->session->set('alert', 'Пароли не совпадают!');
						$this->url->redirect('::referer','/admin/role/');
					} else {
						$pass = md5( 'egi'.mb_strtolower($pass, 'UTF-8').'pet' );
						$data['pass'] = $pass;
					}
				}
				
				if ($this->role->HasLoginNew($id, $login) == false) {
					$this->db->update('auth_user', $data, $id);
				} else $this->session->set('alert', 'Логин занят!');
				
				$this->session->set('alert', 'Изменения сохранены');
				$this->url->redirect('/admin/role/');
			}
			$user = $this->role->getUser($id);
			
			$role_id = $this->session->get('role_id');
			$user['roles'] = $this->role->getSubRoles($role_id);
			
			if (!empty($user['roles'])) {
				foreach ($user['roles'] as &$v) {
					if ($v['id'] == $user['role_id']) {
						$v['selected'] = 'selected';
						break;
					}
				}
			}
			
			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/user.html', $user);
		}
		
		
		//Ajax проверка существования логина
		public function CheckUserLogin() {
			$login = empty($_POST['login']) ? '' : $_POST['login'];
			if ($this->role->HasLogin($login)) die(true);
			die(false);
		}
		
		//Ajax проверка существования логина при изменении
		public function CheckUserLoginNew() {
			$id = empty($_POST['id']) ? 0 : (int)$_POST['id'];
			$login = empty($_POST['login']) ? '' : $_POST['login'];
			if ($this->role->HasLoginNew($id, $login)) die(true);
			die(false);
		}
		
		//Ajax проверка существования логина при изменении
		public function CheckRoleTitle() {
			$title = empty($_POST['title']) ? '' : $_POST['title'];
			if ($this->role->HasRoleTitle($title)) die(true);
			die(false);
		}
		
		//Ajax проверка существования логина при изменении
		public function CheckRoleTitleNew() {
			$id = empty($_POST['id']) ? 0 : (int)$_POST['id'];
			$title = empty($_POST['title']) ? '' : $_POST['title'];
			if ($this->role->HasRoleTitleNew($id, $title)) die(true);
			die(false);
		}
		
		//Удаление пользователя
		public function DeleteUser($id) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			$users = $this->role->getSubUsers($this->session->get('admin'), true);
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
			if (!in_array($id, $users)) $this->AccessError();
			
			$this->db->delete('auth_user', $id);
			$this->db->delete('auth_user', array('pid'=>$id));
			
			$this->url->redirect('::referer', '/admin/role/');
		}
		
		//==========================================================
		
		//Добавление роли
		public function add_role() {
			if (!$this->CheckAccess(1)) $this->AccessError();
			
			if (!empty($_POST)) {
				$title = empty($_POST['title']) ? '' : $_POST['title'];
				
				$title = mb_strtolower(strip_tags($title), 'UTF-8');
				$data = array(
					'title' => $title,
					'pid' => $this->session->get('role_id')
				);
				
				if ($this->role->HasRoleTitle($title) == false) {
					$id = $this->db->insert('auth_role', $data);
					if ($id) {	
						$this->url->redirect('/admin/role/role/'.$id);
					} else $this->session->set('alert', 'Ошибка добавления');
				} else $this->session->set('alert', 'Название роли занято!');
				
				$this->url->redirect('::referer','/admin/role/');
			}
			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/add_role.html');
		}
		
		
		//Редактирование роли
		public function role($id = false) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			$roles = $this->role->getSubRoles($this->session->get('role_id'), true);
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
			if (!in_array($id, $roles)) $this->AccessError();
			
			if (!empty($_POST)) {
				if (!$id) {
					$this->session->set('alert', 'Ошибка добавления');
					$this->url->redirect('::referer','/admin/role/');
				}
				
				$title = empty($_POST['title']) ? '' : $_POST['title'];
				$title = mb_strtolower(strip_tags($title), 'UTF-8');
				
				$data = array(
					'title'=>$title
				);
				
				if ($this->role->HasRoleTitleNew($id, $title) == false) {
					$this->db->update('auth_role', $data, $id);
				} else $this->session->set('alert', 'Название роли занято!');
				
				//----
				$this->db->query('DELETE FROM auth_rights WHERE role_id='.$id.' AND action_id NOT IN(2, 6)');
				$rights = array('role_id' => $id);
				
				if ( (!empty($_POST['role_edit'])) && ((int)$_POST['role_edit'] == 1) ) $this->db->insert('auth_rights', $rights+array('action_id'=>1));
				if ( (!empty($_POST['main_edit'])) && ((int)$_POST['main_edit'] == 1) ) $this->db->insert('auth_rights', $rights+array('action_id'=>3));
				if ( (!empty($_POST['page_add'])) && ((int)$_POST['page_add'] == 1) ) $this->db->insert('auth_rights', $rights+array('action_id'=>4));
				if ( (!empty($_POST['banners_edit'])) && ((int)$_POST['banners_edit'] == 1) ) $this->db->insert('auth_rights', $rights+array('action_id'=>7));
				
				//----
				
				$this->session->set('alert', 'Изменения сохранены');
				$this->url->redirect('/admin/role/role/'.$id);
			}
			
			$role = $this->role->getRole($id);
			$rights = $this->role->getRoleRights($id);
			
			//=========== Права предка
			if ($role['pid'] > 0) {
				$p_rights = $this->role->getRoleRights($role['pid']);
				if (!isset($p_rights[1])) $role['no_role_edit'] = 1;
				if (!isset($p_rights[3])) $role['no_main_edit'] = 1;
				if (!isset($p_rights[4])) $role['no_page_add'] = 1;
				if (!isset($p_rights[7])) $role['no_banners'] = 1;
			}
			//-----------
			$role['role_edit'] = isset($rights[1]) ? 'checked="checked"' : '';
			$role['main_edit'] = isset($rights[3]) ? 'checked="checked"' : '';
			$role['page_add'] = isset($rights[4]) ? 'checked="checked"' : '';
			$role['banners_edit'] = isset($rights[7]) ? 'checked="checked"' : '';
			//-----------
			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/role.html', $role);
		}
		
		
		//Редактирование прав на страницы
		public function role_pages($id = false) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			$roles = $this->role->getSubRoles($this->session->get('role_id'), true);
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
			if (!in_array($id, $roles)) $this->AccessError();
			
			if (!empty($_POST)) {
				if (!$id) {
					$this->session->set('alert', 'Ошибка добавления');
					$this->url->redirect('::referer','/admin/role/');
				}
				
				$this->db->query('DELETE FROM auth_rights WHERE role_id='.$id.' AND action_id=2');
				$rights = array('role_id' => $id);
				
				if (!empty($_POST['page_edit'][2])) {
					foreach ($_POST['page_edit'][2] as $page_id) {
						$this->db->insert('auth_rights', $rights+array('action_id'=>2, 'param1'=>$page_id));
					}
				}
				
				$this->session->set('alert', 'Изменения сохранены');
				$this->url->redirect('/admin/role/role/'.$id);
			}
			
			$role = $this->role->getRole($id);
			$rights = $this->role->getRoleRights($id);
			
			//=========== Права предка
			if ($role['pid'] > 0) {
				$access = $this->role->getAccessPages($role['pid']);
			} else $access = array();
			//-----------
			$role['pages'] = $this->role->getPages($access);
			if (!empty($role['pages'])) {
				foreach ($role['pages'] as &$value) {
					if (isset($rights[2][$value['id']])) $value['page_edit'] = 'checked="checked"';
				}
			}
			//-----------
			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/role_pages.html', $role);			
		}
		
		
		//Удаление роли
		public function DeleteRole($id) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			$roles = $this->role->getSubRoles($this->session->get('role_id'), true);
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
			if (!in_array($id, $roles)) $this->AccessError();
			
			$this->db->update('auth_user',array('role_id'=>0), array('role_id'=>$id));
			$this->db->delete('auth_role', $id);
			$this->db->delete('auth_rights', array('role_id'=>$id));
			$this->url->redirect('::referer', '/admin/role/');
		}
		
		//========================================================
		
		//Редактирование роли
		public function self() {
			$user_id = $this->session->get('admin');
			if ($user_id > 0) {
				if (!empty($_POST)) {
					$login = empty($_POST['login']) ? '' : $_POST['login'];
					$pass = empty($_POST['pass']) ? '' : $_POST['pass'];
					$pass2 = empty($_POST['pass2']) ? '' : $_POST['pass2'];
					
					$login = mb_strtolower(strip_tags($login), 'UTF-8');
					
					$data = array(
						'login'=>$login
					);
					
					if (!empty($pass)) {
						if ($pass != $pass2) {
							$this->session->set('alert', 'Пароли не совпадают!');
							$this->url->redirect('::referer','/admin/role/');
						} else {
							$pass = md5( 'egi'.mb_strtolower($pass, 'UTF-8').'pet' );
							$data['pass'] = $pass;
						}
					}
					
					if ($this->role->HasLoginNew($user_id, $login) == false) {
						$this->db->update('auth_user', $data, $user_id);
					} else $this->session->set('alert', 'Логин занят!');
					
					$this->session->set('alert', 'Изменения сохранены');
					$this->url->redirect('/admin/role/');
				}
				$user = $this->role->getUser($user_id);
			}			
			$this->html->tpl_vars['content_path'] = $this->html->render('role/user.html', $user);
		}
	}
?>
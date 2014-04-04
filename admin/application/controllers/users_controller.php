<?php
	/*
		TODO: права на вложенные страницы
	*/
	
	class users_controller extends application_controller {
		
		//Список пользователей и ролей
		public function index() {
			if (!$this->CheckAccess(1)) $this->AccessError();
			
			$role_id = $this->session->get('role_id');
			if ($role_id > 0) $role['self'] = 1;
			
			//Список пользователей
			$user_id = $this->session->get('admin');
			$role['users'] = $this->users->getUsers();
			if (empty($role['users']))
				$role['message'] = '<div style="margin:5px 0 5px 14px;">Пользователей нет</div>';
									
			$this->html->tpl_vars['content_path'] = $this->html->render('users/view.html', $role);
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
				
				$name = empty($_POST['name']) ? '' : strip_tags($_POST['name']);
				$lastname = empty($_POST['lastname']) ? '' : strip_tags($_POST['lastname']);
				$middlename = empty($_POST['middlename']) ? '' : strip_tags($_POST['middlename']);
				
				if ( $pass != $pass2 ) {
					$this->session->set('alert', 'Пароли не совпадают!');
					$this->url->redirect('::referer','/admin/role/');
				}
				
				$login = mb_strtolower(strip_tags($login), 'UTF-8');
				$pass = md5( 'egi'.mb_strtolower($pass, 'UTF-8').'pet' );
											
				
				$data = array(
					'login'			=>	$login,				
					'name'			=>	$name,				
					'lastname'		=>	$lastname,				
					'middlename'	=>	$middlename,				
					'pass'			=>	$pass,
					'state'			=>	1,
					'email' 		=> 	trim(mb_strtolower($_POST['email'],'UTF-8')),
				);
				
				if ($this->users->HasLogin($login) == false) {
					$id = $this->db->insert('users', $data);
					if ($id) {	
						$this->url->redirect('/admin/users/');
					} else $this->session->set('alert', 'Ошибка добавления');
				} else $this->session->set('alert', 'Логин занят!');
				
				$this->url->redirect('::referer','/admin/users/');
			}
			$role_id = $this->session->get('role_id');
			$user['roles'] = $this->role->getSubRoles($role_id);
			
			$this->html->tpl_vars['content_path'] = $this->html->render('users/add_user.html', $user);
		}
		
		
		//Редактирование пользователя
		public function user($id = false) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			$user_id = $this->session->get('admin');
			$users = $this->users->getSubUsers($user_id, true);
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
					
			if (!empty($_POST)) {
				if (!$id) {
					$this->session->set('alert', 'Ошибка добавления');
					$this->url->redirect('::referer','/admin/users/');
				}
				
				$login = empty($_POST['login']) ? '' : $_POST['login'];
				$email = empty($_POST['login']) ? '' : trim(mb_strtolower($_POST['email'],'UTF-8'));
				$pass = empty($_POST['pass']) ? '' : $_POST['pass'];
				$pass2 = empty($_POST['pass2']) ? '' : $_POST['pass2'];
				
				$login = mb_strtolower(strip_tags($login), 'UTF-8');
				
				$name = empty($_POST['name']) ? '' : strip_tags($_POST['name']);
				$lastname = empty($_POST['lastname']) ? '' : strip_tags($_POST['lastname']);
				$middlename = empty($_POST['middlename']) ? '' : strip_tags($_POST['middlename']);				
				
				$data = array(
					'login'			=>	$login,
					'name'			=>	$name,
					'lastname'		=>	$lastname,
					'middlename'	=>	$middlename,
					'email'			=>	$email,
				);
				
				if (!empty($pass)) {
					if ($pass != $pass2) {
						$this->session->set('alert', 'Пароли не совпадают!');
						$this->url->redirect('::referer','/admin/users/');
					} else {
						$pass = md5( 'egi'.mb_strtolower($pass, 'UTF-8').'pet' );
						$data['pass'] = $pass;
					}
				}
				
				if ($this->users->HasLoginNew($id, $login) == false) {
					$this->db->update('users', $data, $id);
				} else $this->session->set('alert', 'Логин занят!');
				
				$this->session->set('alert', 'Изменения сохранены');
				$this->url->redirect('/admin/users/');
			}
			$user = $this->users->getUser($id);				
			
			$this->html->tpl_vars['content_path'] = $this->html->render('users/user.html', $user);
		}
		
		
		//Ajax проверка существования логина
		public function CheckUserLogin() {
			$login = empty($_POST['login']) ? '' : $_POST['login'];
			if ($this->users->HasLogin($login)) die(true);
			die(false);
		}
		
		//Ajax проверка существования логина при изменении
		public function CheckUserLoginNew() {
			$id = empty($_POST['id']) ? 0 : (int)$_POST['id'];
			$login = empty($_POST['login']) ? '' : $_POST['login'];
			if ($this->users->HasLoginNew($id, $login)) die(true);
			die(false);
		}
		
		//Ajax проверка существования логина при изменении
		public function CheckRoleTitle() {
			$title = empty($_POST['title']) ? '' : $_POST['title'];
			if ($this->users->HasRoleTitle($title)) die(true);
			die(false);
		}
		
		//Ajax проверка существования логина при изменении
		public function CheckRoleTitleNew() {
			$id = empty($_POST['id']) ? 0 : (int)$_POST['id'];
			$title = empty($_POST['title']) ? '' : $_POST['title'];
			if ($this->users->HasRoleTitleNew($id, $title)) die(true);
			die(false);
		}
		
		//Удаление пользователя
		public function DeleteUser($id) {
			if (!$this->CheckAccess(1)) $this->AccessError();
			
			$id = empty($_POST['id']) ? (int)$id : (int)$_POST['id'];
						
			$this->db->delete('users', $id);			
			
			$this->url->redirect('::referer', '/admin/users/');
		}
		
		//==========================================================
		
		
	}
?>
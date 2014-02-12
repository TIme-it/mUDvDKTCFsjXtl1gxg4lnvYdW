<?php
	// -- сюда больше никаких паблик методов лучше не дописавать
	// -- из соображения безопастности, т. к. доступ к ним (пабликам)
	// -- можно получить без авторизации, ибо __before() переписан,
	// -- а значит нет проверки на авторизацию админа
	
	class login_controller extends application_controller {

		public function __construct() {
			$this->login = "admin";
			$this->pwd   = "1";
		}
		
		// -- переопределям __before() в "ноль" (сбрасываем), чтобы
		// -- избежать замыкания при проверки на админа в родительском калбеке
		public function __before() {
			// -- empty
		}
		
		public function index() {
			$login['admin_dir'] = $this->config->get('admin_dir','site');
			$login['site_org']  = $this->config->get('org','site');
			$enter = $this->session->get('login_admin');
			if($enter === 'denied') $login['error'] = 'Неправильный логин или пароль';
			$login['title_common'] = $this->config->get('title_common', 'site');
			$login['title_browser'] = $this->config->get('title_browser', 'site');
			echo $this->html->render('layouts/login_form.html', $login);
			die();
		}
		
		public function enter() {			
			if(($_POST['login'] == $this->login) && ($_POST['pwd'] == $this->pwd)) {
				$this->session->set('admin', 0);
				$this->session->set('role_id', 0);
				$this->session->set('name', 'Главный администратор');
			} else {
				$login = mb_strtolower(strip_tags($_POST['login']), 'UTF-8');
				$pass = md5( 'egi'.mb_strtolower($_POST['pwd'], 'UTF-8').'pet' );
				
				$user = $this->role->getUserByLogin($login);
				if (!empty($user) && ($pass == $user['pass']) ) {
					$this->session->set('admin', $user['id']);
					$this->session->set('name', $user['login']);
					$this->session->set('role_id', $user['role_id']);
					$this->session->set('rights', $this->role->getRoleRights($user['role_id']));
				}
				$this->session->set('login_admin', 'denied');
			}
			
			$this->url->redirect('/admin/');
		}
		
		public function logout() {
			$this->session->del('admin');
			$this->session->del('name');
			$this->session->del('role_id');
			$this->session->del('rights');
			$this->session->del('login_admin');
			$this->url->redirect('/admin/login/');
		}
	}
?>
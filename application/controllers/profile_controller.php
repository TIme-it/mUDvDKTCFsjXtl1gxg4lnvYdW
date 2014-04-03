<?php
	// -- этот контроллер полностью включает в себя методы
	// -- организации личного кабинета
	// -- так сказать на все случаи жизни, ну почти на все
	
	class profile_controller extends application_controller {
		
		private $path = false;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('profile', 'files');
		}
		
		// -- профиль юзера
		public function index() {
			if(empty(self::$user_id)) {
				$this->html->render('profile/login.html', array(), 'content');
			} else {
				// -- данные профиля
				$user = $this->profile->getUser(self::$user_id);
				
				// -- аватарка
				$user['avatar_src'] = '/application/includes/images/profile/avatar_default_men.png';				
				if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.jpg')) {
					$user['avatar_src'] = '/application/includes/profile/ava_b/'.self::$user_id.'.jpg?_='.rand(0, 10000);					
				}				
				if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.png')) {
					$user['avatar_src'] = '/application/includes/profile/ava_b/'.self::$user_id.'.png?_='.rand(0, 10000);
				}				
				if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.gif')) {
					$user['avatar_src'] = '/application/includes/profile/ava_b/'.self::$user_id.'.gif?_='.rand(0, 10000);
				}
				
				// -- дата рождения									
				$user['bday_opt_d'] = '';
				for($i = 1; $i <= 31; $i++) {
					$user['bday_opt_d'] .= '<option value="'.$i.'"'.
					($i == date('d', $user['bday']) ? ' selected="selected"' : '').'>'.sprintf('%02d', $i).'&nbsp;</option>';
				}
				// -- день рождения: месяцы
				$user['bday_opt_m'] =
					'<option value="01"'.(date('m', $user['bday']) ==  1 ? ' selected="selected"' : '').'>января&nbsp;</option>'.
					'<option value="02"'.(date('m', $user['bday']) ==  2 ? ' selected="selected"' : '').'>февраля&nbsp;</option>'.
					'<option value="03"'.(date('m', $user['bday']) ==  3 ? ' selected="selected"' : '').'>марта&nbsp;</option>'.
					'<option value="04"'.(date('m', $user['bday']) ==  4 ? ' selected="selected"' : '').'>апреля&nbsp;</option>'.
					'<option value="05"'.(date('m', $user['bday']) ==  5 ? ' selected="selected"' : '').'>мая&nbsp;</option>'.
					'<option value="06"'.(date('m', $user['bday']) ==  6 ? ' selected="selected"' : '').'>июня&nbsp;</option>'.
					'<option value="07"'.(date('m', $user['bday']) ==  7 ? ' selected="selected"' : '').'>июля&nbsp;</option>'.
					'<option value="08"'.(date('m', $user['bday']) ==  8 ? ' selected="selected"' : '').'>августа&nbsp;</option>'.
					'<option value="09"'.(date('m', $user['bday']) ==  9 ? ' selected="selected"' : '').'>сентября&nbsp;</option>'.
					'<option value="10"'.(date('m', $user['bday']) == 10 ? ' selected="selected"' : '').'>октября&nbsp;</option>'.
					'<option value="11"'.(date('m', $user['bday']) == 11 ? ' selected="selected"' : '').'>ноября&nbsp;</option>'.
					'<option value="12"'.(date('m', $user['bday']) == 12 ? ' selected="selected"' : '').'>декабря&nbsp;</option>';
				// -- день рождения: годы
				$user['bday_opt_y'] = '';
				for($i = date('Y'); $i >= 1930; $i--) {
					$user['bday_opt_y'] .= '<option value="'.$i.'"'.
					($i == date('Y', $user['bday']) ? ' selected="selected"' : '').'>'.$i.'&nbsp;</option>';
				}					
				
				if (!empty($user['sex'])) {
					 $user['selected_1']	=	" selected";
				}
											
								
				$this->layout = 'profile';
				$this->html->render('profile/index.html', $user, 'content');
			}
		}
		

		
		// -- регистрaция
		/*public function reg() {
			$data = array();
			
			if(!empty($_POST)) {
				$data['login'] = (empty($_POST['login'])) ? '' : htmlspecialchars($_POST['login']);
				$data['email'] = (empty($_POST['email'])) ? '' : htmlspecialchars($_POST['email']);
				if(empty($_POST['captcha']) || $_POST['captcha'] !== $this->session->get('captcha_reg')) {
					$this->session->set('alert', 'Не верно указан защитный код');
				} elseif(empty($_POST['login'])) {
					$this->session->set('alert', 'Необходимо заполнить поле логин');
				} elseif($this->db->get_one('SELECT 1 FROM users WHERE login = '.$this->db->escape($_POST['login']))) {
					$this->session->set('alert', 'Этот логин уже занят');
				} elseif(empty($_POST['email'])) {
					$this->session->set('alert', 'Необходимо заполнить поле e-mail');
				} elseif(!preg_match('/^[a-z0-9\-\.]+\@[a-z0-9\-\.]+\.[a-z]{2,6}$/iuU', trim($_POST['email']))) {
					$this->session->set('alert', 'Поле e-mail заполено не верно');
				} elseif(empty($_POST['pass'])) {
					$this->session->set('alert', 'Необходимо заполнить поле пароль');
				} elseif(empty($_POST['pass2']) || $_POST['pass'] !== $_POST['pass2']) {
					$this->session->set('alert', 'Пароли не совпадают');
				} else {
					// -- валидация пройдена - регистрируем
					$user = array(
						'login' => htmlspecialchars_decode($_POST['login']),
						'email' => $_POST['email'],
						'pass'  => md5($_POST['pass']),
						'state' => 0
					);
					$this->db->insert('users', $user);
					
					$reg_data = array(
						'login'  => $user['login'],
						'domain' => $this->config->get('domain', 'site'),
						'key'    => md5($user['login'].'1'.$user['email'].'2'),
					);
					$letter  = $this->html->render('letters/reg.html', $reg_data);
					$subject = 'Регистрация на сайте '.$reg_data['domain'];
					$to      = $user['email'];
					$this->mail->send_mail($to, $letter, $subject);
					
					$this->url->redirect('/profile/reg_ok/');
				}
			}
		
			$this->html->render('profile/registration.html', $data, 'content');
		}
		
		// -- подтверждение регистрации
		public function confirm($key = '') {
			if(empty($key)) {
				$this->session->alert('Неправильная ссылка');
				$this->url->redirect('/profile/reg/');
			}
			$sql  = 'SELECT id, pass FROM users '.
					'WHERE MD5(CONCAT(login,"1",email,"2")) = '.$this->db->escape($key);
			$user_data = $this->db->get_row($sql);
			if(empty($user_data)) {
				$this->session->alert('Неправильная ссылка');
				$this->url->redirect('/profile/reg/');
			}
			// -- юзер существует
			$this->db->update('users', array('state' => 1), $user_data['id']);
			$this->toAuth($user_data['id'], $user_data['pass'], 0);
			$this->session->set('alert', 'Аккаунт был успешно подтверждён!');
			$this->url->redirect('/profile/');
		}
		
		public function reg_ok() {
			$this->html->render('profile/registration_ok.html', array(), 'content');
		}
		*/
		
		
		// -- форма аутентификации
		public function login() {
			if(!empty($_SESSION)){
				session_start();
			}
			$data = array();

			if(!empty(self::$user_id)) {
				$this->url->redirect('/profile/');
			}
			if(!empty($_POST)) {
				$data['login'] = empty($_POST['login']) ? '' : htmlspecialchars($_POST['login']);
				if(empty($_POST['login']) || empty($_POST['pass'])) {
					$this->session->set('alert', 'Необходимо указать логин и пароль');
				} else {
					$pass = md5( 'egi'.mb_strtolower($_POST['pass'], 'UTF-8').'pet' );
					$sql  = 'SELECT id, state, pass FROM users WHERE '.
							'	login = '.$this->db->escape($_POST['login']).' AND '.
							'	pass  = "'.$pass.'" ';
					$user_data = $this->db->get_row($sql);
															
					if(!empty($user_data)) {
						switch($user_data['state']) {
							case 0:
								$this->session->set('alert', 'Ваш аккаунт не подтвержден');
								break;
							case 1:
								$expire = empty($_POST['remember']) ? 0 : time() + 3600 * 24 * 365;
								$this->toAuth($user_data['id'], $user_data['pass'], $expire);
								$this->url->redirect('/profile/');
								break;
						}
					} else {
						$this->session->set('alert', 'Неправильная пара логин/пароль');
					}
				}
			}
			$this->html->render('profile/login.html', $data, 'content');
		}
		
		// -- ajax-метод: аутентификация
		public function auth() {
			if(!empty($_POST['login']) && !empty($_POST['pass'])) {
				$sql  = 'SELECT id, state, pass FROM users WHERE '.
						'	login = '.$this->db->escape($_POST['login']).' AND '.
						'	pass  = MD5('.$this->db->escape($_POST['pass']).')';
				$user_data = $this->db->get_row($sql);
				if(!empty($user_data)) {
					switch($user_data['state']) {
						case 0:
							die(json_encode(array('result' => 1)));
							break;
						case 1:
							$expire = empty($_POST['remember']) ? 0 : time() + 3600 * 24 * 365;
							$this->toAuth($user_data['id'], $user_data['pass'], $expire);
							die(json_encode(array('result' => 2, 'href' => $_SERVER['HTTP_REFERER'])));
							break;
					}
				}
			}
			die(json_encode(array('result' => 0)));
		}
		
		// -- выход из профиля
		public function logout() {
			$this->toAuth(self::$user_id, '', time() - 30*24*3600);
			$this->url->redirect('::referer');
		}
		
		
		
		
		// -- редактировать профиль
		public function edit() {
			if(empty(self::$user_id)) {
				$this->html->render('profile/login.html', array(), 'content');
			} else {
				// -- если мы аутентифицированы
				$user = array();
				if(!empty($_POST)) { 
															
					switch (trim($_POST['month__sexyCombo'])) {
						case 'января':
							$_POST['month__sexyCombo']	=	1;
							break;
						case 'февраля':
							$_POST['month__sexyCombo']	=	2;
							break;
						case 'марта':
							$_POST['month__sexyCombo']	=	3;
							break;
						case 'апреля':
							$_POST['month__sexyCombo']	=	4;
							break;
						case 'мая':
							$_POST['month__sexyCombo']	=	5;
							break;
						case 'июня':
							$_POST['month__sexyCombo']	=	6;
							break;
						case 'июля':
							$_POST['month__sexyCombo']	=	7;
							break;
						case 'августа':
							$_POST['month__sexyCombo']	=	8;
							break;
						case 'сентября':
							$_POST['month__sexyCombo']	=	9;
							break;						
						case 'октября':
							$_POST['month__sexyCombo']	=	10;
							break;						
						case 'ноября':
							$_POST['month__sexyCombo']	=	11;
							break;						
						case 'декабря':
							$_POST['month__sexyCombo']	=	12;
							break;
					}
															
					$bday	=	mktime(0, 0, 0, (int)$_POST['month__sexyCombo'], (int)$_POST['day__sexyCombo'], (int)$_POST['year__sexyCombo']);
				
					// $ava = $this->path.'ava_b'.DS.$user['id'].'.jpg';
					
					if(!empty($_FILES['avatar']['tmp_name']) && file_exists($_FILES['avatar']['tmp_name'])) {
						$ext	=	$this->getExtension($_FILES['avatar']['name']);						
						switch ($ext) {
							case "jpg":								
							case "gif":								
							case "png":
								$file	=	$this->path.'ava_b'.DS.self::$user_id.'.'.$ext;
								copy($_FILES['avatar']['tmp_name'], $file);
								$img	=	$this->image->analyze($file);
								$this->image->toFile($file, 80, 210, 210);
								break;								
						}							
					}					
					
					// -- сохраняем данные о юзере
					$user = array(
						'bday'  		=> $bday,
						'name'  		=> strip_tags($_POST['name']),
						'lastname'  	=> strip_tags($_POST['lastname']),
						'middlename'  	=> strip_tags($_POST['middlename']),						
						'email'  		=> strip_tags($_POST['email']),						
						'tel'  			=> strip_tags($_POST['tel']),
						'adr'  			=> strip_tags($_POST['adr']),
						'sex'  			=> (int)$_POST['sex'],
						'subscribe'  	=> (int)$_POST['subscribe'],
					);
					$this->db->update('users', $user, self::$user_id);
					$this->session->set('alert', 'Данные профиля были успешно изменены');
					$this->url->redirect('/profile/');
					die();
				}
				$user = $this->profile->getUser(self::$user_id);
				// -- день рождения
				$user['bday'] = explode('/', date('d/m/Y', $user['bday']));
				// -- день рождения: дни
				$user['bday_opt_d'] = '';
				for($i = 1; $i <= 31; $i++) {
					$user['bday_opt_d'] .= '<option value="'.$i.'"'.
					($i == $user['bday'][0] ? ' selected="selected"' : '').'>'.sprintf('%02d', $i).'&nbsp;</option>';
				}
				// -- день рождения: месяцы
				$user['bday_opt_m'] =
					'<option value="01"'.($user['bday'][1] ==  1 ? ' selected="selected"' : '').'>января&nbsp;</option>'.
					'<option value="02"'.($user['bday'][1] ==  2 ? ' selected="selected"' : '').'>февраля&nbsp;</option>'.
					'<option value="03"'.($user['bday'][1] ==  3 ? ' selected="selected"' : '').'>марта&nbsp;</option>'.
					'<option value="04"'.($user['bday'][1] ==  4 ? ' selected="selected"' : '').'>апреля&nbsp;</option>'.
					'<option value="05"'.($user['bday'][1] ==  5 ? ' selected="selected"' : '').'>мая&nbsp;</option>'.
					'<option value="06"'.($user['bday'][1] ==  6 ? ' selected="selected"' : '').'>июня&nbsp;</option>'.
					'<option value="07"'.($user['bday'][1] ==  7 ? ' selected="selected"' : '').'>июля&nbsp;</option>'.
					'<option value="08"'.($user['bday'][1] ==  8 ? ' selected="selected"' : '').'>августа&nbsp;</option>'.
					'<option value="09"'.($user['bday'][1] ==  9 ? ' selected="selected"' : '').'>сентября&nbsp;</option>'.
					'<option value="10"'.($user['bday'][1] == 10 ? ' selected="selected"' : '').'>октября&nbsp;</option>'.
					'<option value="11"'.($user['bday'][1] == 11 ? ' selected="selected"' : '').'>ноября&nbsp;</option>'.
					'<option value="12"'.($user['bday'][1] == 12 ? ' selected="selected"' : '').'>декабря&nbsp;</option>';
				// -- день рождения: годы
				$user['bday_opt_y'] = '';
				for($i = date('Y'); $i >= 1970; $i--) {
					$user['bday_opt_y'] .= '<option value="'.$i.'"'.
					($i == $user['bday'][2] ? ' selected="selected"' : '').'>'.$i.'&nbsp;</option>';
				}
				// -- аватар
				$user['ava_src'] = '/application/includes/images/profile/avatar_default_men.png';				
				if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.jpg')) {
					$user['ava_src'] = $this->path.'ava_b'.DS.self::$user_id.'.jpg?_='.rand(0, 1000);					
				}				
				if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.png')) {
					$user['ava_src'] = $this->path.'ava_b'.DS.self::$user_id.'.png?_='.rand(0, 1000);
				}				
				if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.gif')) {
					$user['ava_src'] = $this->path.'ava_b'.DS.self::$user_id.'.gif?_='.rand(0, 1000);
				}
				// -- токен для смены аватара
				$user['user_id']      = $user['id'];
				$user['avatar_token'] = md5($user['id'].'aOr');
				
				$this->html->render('profile/edit.html', $user, 'content');
			}
		}
		
		function delete_avatar() {
			if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.jpg')) {				
				unlink($this->path.'ava_b'.DS.self::$user_id.'.jpg');
			}				
			if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.png')) {				
				unlink($this->path.'ava_b'.DS.self::$user_id.'.png');
			}				
			if(file_exists($this->path.'ava_b'.DS.self::$user_id.'.gif')) {			
				unlink($this->path.'ava_b'.DS.self::$user_id.'.gif');
			}		
						
			$this->url->redirect('::referer');
		}	
		
		function getExtension($filename) {
			return end(explode(".", $filename));
		}		
		// -- сменить аватар: используется библиотекой uploadify
		/*public function change_avatar() {
			if(!empty($_POST['user_id']) && !empty($_POST['token']) && $_POST['token'] == md5($_POST['user_id'].'aOr')) {
				self::$user_id = (int)$_POST['user_id'];
				// -- загрузка временного изображения
				$ava_temp = $this->path.'ava_temp/'.self::$user_id.'.jpg';
				if(!empty($_FILES['Filedata']['tmp_name']) && file_exists($_FILES['Filedata']['tmp_name'])) {
					copy($_FILES['Filedata']['tmp_name'], $ava_temp);
				}
				// -- определение размеров изображения
				$tmp_sizes = $this->getAvatarSizes($ava_temp);
				$data  = array(
					'user_id' => self::$user_id,
					'only_w'  => $tmp_sizes['width'],
					'only_h'  => $tmp_sizes['height'],
					'w_and_h' => $tmp_sizes['w_and_h'],
					'token'   => $_POST['token'],
					'rand'    => rand()
				);
				echo $this->html->render('profile/avatar_editor.html', $data);
				die();
			}
			die('error');
		}
		
		// -- сменить аватар: сохранение аватарки
		public function change_avatar_complete() {
			if(!empty($_POST['user_id']) && !empty($_POST['token']) && $_POST['token'] == md5($_POST['user_id'].'aOr')) {
				self::$user_id = (int)$_POST['user_id'];
				$ava_temp  = $this->path.'ava_temp/'.self::$user_id.'.jpg';
				$ava_b     = $this->path.'ava_b/'.self::$user_id.'.jpg';
				$ava_s     = $this->path.'ava_s/'.self::$user_id.'.jpg';
				$tmp_sizes = $this->getAvatarSizes($ava_temp);
				if(!empty($tmp_sizes)) {
					// -- получение данных обрезки с учетом изменённой пропорции
					$size['w'] = (int)round((int)$_POST['w'] * $tmp_sizes['opt']);
					$size['h'] = (int)round((int)$_POST['h'] * $tmp_sizes['opt']);
					$size['t'] = (int)round((int)$_POST['t'] * $tmp_sizes['opt']);
					$size['l'] = (int)round((int)$_POST['l'] * $tmp_sizes['opt']);
					// -- защита от очень больших размеров
					if($size['w'] > $tmp_sizes['width'])  $size['w'] = $tmp_sizes['width'];
					if($size['h'] > $tmp_sizes['height']) $size['h'] = $tmp_sizes['height'];
					// -- защита от неожиданной неквадратности
					if($size['w'] < $size['h']) $size['h'] = $size['w'];
					if($size['h'] < $size['w']) $size['w'] = $size['h'];
					// -- защита от смещения
					if($size['w'] + $size['l'] > (int)round((int)$tmp_sizes['width'] * $tmp_sizes['opt']))  $size['l'] = $tmp_sizes['width']  - $size['w'];
					if($size['h'] + $size['t'] > (int)round((int)$tmp_sizes['height'] * $tmp_sizes['opt'])) $size['t'] = $tmp_sizes['height'] - $size['h'];
					
					// -- вырезаем заплатку
					if(!($img = imagecreatefromjpeg($ava_temp))) {
						$img = imagecreatefrompng($ava_temp);
					}
					if($img) {
						// -- определяем размеры
						$ava_s = array(230 => $ava_b, 60 => $ava_s);
						// -- создаем аватарки
						foreach($ava_s as $s => $path) {
							$dst = imagecreatetruecolor($s, $s);
							imagecopyresampled($dst, $img, 0, 0, $size['l'], $size['t'], $s, $s, $size['w'], $size['h']);
							imagejpeg($dst, $path, 80);
							imagedestroy($dst);
						}
						imagedestroy($img);
						// -- увеличиваем параметр сброса для борьбы с кешом
						$this->db->query('UPDATE users SET flush = flush + 1 WHERE id = '.self::$user_id);
						unlink($ava_temp);
						// -- сообщение об успехе
						die(json_encode(array(
							'result' => 'ok',
							'src'    => '/application/includes/profile/ava_b/'.self::$user_id.'.jpg?_='.rand()
						)));
					}
				}
			}
			die(json_encode(array('result' => 'error')));
		}
		
		
		// -- определяем размеры изобр, пропорцию, контейнер
		private function getAvatarSizes($img_path) {
			$sizes  = getimagesize($img_path);
			$result = array(
				'prop'    => 1.0,
				'width'   => $sizes[0],
				'height'  => $sizes[1],
				'w_and_h' => $sizes[3],
				'opt'     => 1.0
			);
			if($result['width'] > 600 || $result['height'] > 600) {
				$result['prop'] = $result['width'] / $result['height'];
				if($result['width'] > $result['height']) {
					$result['opt']    = $result['width'] / 600;
					$result['width']  = 600;
					$result['height'] = round(600 / $result['prop']);
				} else {
					$result['opt']    = $result['height'] / 600;
					$result['width']  = round(600 * $result['prop']);
					$result['height'] = 600;
				}
				$result['w_and_h'] = 'width="'.$result['width'].'" height="'.$result['height'].'"';
			}
			return $result;
		}*/
		
		
		// -- сменить пароль
		public function pass_change() {
			if(empty(self::$user_id)) {
				$this->html->render('profile/login.html', array(), 'content');
			} else {
				if(!empty($_POST)) {
					if(empty($_POST['pass']) || empty($_POST['pass2'])) {
						$this->session->set('alert', 'Некоторые поля были не заполнены');
					} elseif($_POST['pass'] !== $_POST['pass2']) {
						$this->session->set('alert', 'Пароли не совпадают');
					} else {
						$user = array('pass' => md5($_POST['pass']));
						$this->db->update('users', $user, self::$user_id);
						$this->toAuth(self::$user_id, md5($_POST['pass']), 0);
						$this->session->set('alert', 'Пароли был успешно изменен');
					}
				}
				$this->html->render('profile/pass_change.html', array(), 'content');
			}
		}
		
		// -- ха... забыли пароль?
		public function forgot($token = '') {
			$forgot = array();
			// -- проверка токена для предоставлении возможности сменить пароль
			$sql = 'SELECT id FROM users WHERE MD5(CONCAT("1u",id,login,"f2",pass,"*7")) = '.$this->db->escape($token);
			if(preg_match('/^[a-z0-9]{32}$/i', $token) && $user_id = (int)$this->db->get_one($sql)) {
				if(!empty($_POST)) {
					// -- шаг №4. смена пароля и авторизация пользователя
					if(empty($_POST['pass']) || empty($_POST['pass2'])) {
						$this->session->set('alert', 'Необходимо указать новый пароль в обоих полях ввода данных');
					} elseif($_POST['pass'] !== $_POST['pass2']) {
						$this->session->set('alert', 'Пароли не совпадают');
					} else {
						$this->db->update('users', array('pass' => md5($_POST['pass'])), $user_id);
						$this->toAuth($user_id, md5($_POST['pass']), 0);
						$this->url->redirect('/profile/');
					}
				}
				// -- шаг №3. выводим форму для смены пароля
				$forgot['login'] = $this->db->get_one('SELECT login FROM users WHERE id = '.$user_id);
				$this->html->render('profile/forgot_change.html', $forgot, 'content');
			} else {
				if(!empty($_POST)) {
					// -- шаг №2. проверяем e-mail и высылавем письмо с токен-ссылкой
					if(empty($_POST['email'])) {
						$this->session->set('alert', 'E-mail не заполнен');
					} else {
						$forgot['email'] = htmlspecialchars($_POST['email']);
						if(!preg_match('/[a-z0-9\-\.]+\@[a-z0-9\-\.]+\.[a-z]{2,6}/i', $_POST['email'])) {
							$this->session->set('alert', 'E-mail заполнен не правильно');
						} else {
							$sql  = 'SELECT id, login, pass FROM users '.
									'WHERE email = '.$this->db->escape($_POST['email']).' AND state = 1';
							$user_data = $this->db->get_row($sql);
							if(empty($user_data)) {
								$this->session->set('alert', 'Данный e-mail в базе данных не существует');
							} else {
								$forgot['email'] = '';
								$token = md5('1u'.$user_data['id'].$user_data['login'].'f2'.$user_data['pass'].'*7');
								$letter_data = array(
									'token'  => $token,
									'login'  => $user_data['login'],
									'domain' => $this->config->get('domain', 'site')
								);
								$subject = 'Восстановление пароля на сайте '.$letter_data['domain'];
								$letter  = $this->html->render('letters/forgot.html', $letter_data);
								$to      = $_POST['email'];
								$this->mail->send_mail($to, $letter, $subject, $subject);
								$this->session->set('alert', 'На ваш e-mail было выслано письмо с инструкцией по восстановлению пароля');
							}
						}
					}
				}
				// -- шаг №1. просто выводим форму для ввода e-mail
				$this->html->render('profile/forgot.html', $forgot, 'content');
			}
		}
		
		
		
		// -- определяем аутентифицировано пользователя
		public function detectUser() {
			if(!empty($_COOKIE)) {
				foreach($_COOKIE as $name => $value) {
					if(preg_match('/^user([0-9]+)$/', $name, $user)) {
						if($value === md5('321'.md5($user[1].'123'))) {
							self::$user_id = (int)$user[1];
							return true;
						}
					}
				}
			}
			return false;
		}
		
		// -- блок авторизации (запускается в application_controller) 
		public function getProfileBlock() {
			if(empty(self::$user_id)) {
				$this->html->render('profile/profile_block_noauth.html', array(), 'profile_block');
			} else {
				$user = $this->profile->getUser(self::$user_id);
				// -- аватарка
				$user['avatar_src'] = '/application/includes/images/no_avatar_s.jpg';
				if($this->path.'ava_s'.DS.self::$user_id.'.jpg') {
					$user['avatar_src'] = '/application/includes/profile/ava_s/'.self::$user_id.'.jpg?_='.$user['flush'];
				}
				$this->html->render('profile/profile_block_auth.html', $user, 'profile_block');
			}
		}
		
		// -- ставим/убираем куку авторизации
		private function toAuth($id, $pass, $expire) {
			setcookie('user'.$id, md5('321'.md5($id.'123')), $expire, '/');
		}
		
	}
?>
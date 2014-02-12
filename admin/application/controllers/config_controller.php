<?php
	class config_controller extends application_controller {
		
		public function index($form_id = 0) {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			require(APPLICATION.'config.inc.php');
			if(empty($config['site'])) {
				$config['site'] = array();
			}
			$config['site']['style_1'] = 'style="display: none;"';
			$config['site']['style_2'] = 'style="display: none;"';
			$config['site']['style_3'] = 'style="display: none;"';
			$config['site']['style_4'] = 'style="display: none;"';
			$config['site']['style_5'] = 'style="display: none;"';
			if(!$form_id) {
				$config['site']['act_1']   = 'act';
				$config['site']['style_1'] = '';
			} elseif((int)$form_id > 0) {
				$config['site']['act_'.(int)$form_id]   = 'act';
				$config['site']['style_'.(int)$form_id] = '';
			}
			// -- логотип для печати
			if(file_exists(INCLUDES.'images'.DS.'logop.jpg')) {
				$config['site']['logop_show'] = '<a target="_blank" href="/application/includes/images/logop.jpg">смотреть</a>';
			}
			// -- изображения
			$config['site']['main_image_list'] = $this->db->get_all('SELECT id FROM images WHERE module_id = 100 AND pid = 1');
			$this->html->render('layouts/config.html', $config['site'], 'content_path');
		}
		
		// -- конфигурация главной страницы
		public function main() {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(3)) 
				$this->role_controller->AccessError();
			
			require(APPLICATION.'config.inc.php');
			if(empty($config['site'])) {
				$config['site'] = array();
			}
			$this->html->render('layouts/config_main.html', $config['site'], 'content_path');
		}
		
		public function add($form_id = 0) {
			$path = $this->config->get('catalog','files');
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			if (empty($form_id))	//Проверяем права на главную
				if (!$this->role_controller->CheckAccess(3)) $this->role_controller->AccessError();
			
			
			$form_id = (int)$form_id;
			$_POST = $this->base->del_keys($_POST, array('title','link'));
			$config['site'] = $_POST;
			$this->add_config($config);
			$this->session->set('alert', ALERT_CHANGE_DATA);

			if(isset($_FILES['center_image']['tmp_name']) && file_exists($_FILES['center_image']['tmp_name'])) {

				$file = $this->file->getFileInfo($_FILES['center_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['center_image']['tmp_name'], $path.'/center_image.jpg')) {
						$this->image->analyze($path.'/center_image.jpg');
						if(in_array($_POST['type'], array(1,2)))
							$this->image->ToFile($path.'/center_image.jpg', 80, $this->config->get('img_width','center_image'), $this->config->get('img_height','center_image'));
						else
						{
							$this->image->ToFile($path.'/center_image.jpg', 80, $this->config->get('img_width','center_image'), $this->config->get('img_height','center_image'));
						}
					}
				}
			}


			if (!empty($_FILES['img'])){
				$path = $this->config->get('blocks','images');
				for ($i=0; $i < 3; $i++) { 
					foreach ($_FILES as $j => &$item) {
						// var_dump($cnt);
						// var_dump($item['name'][$cnt]);
						if(isset($item['tmp_name'][$i]) && file_exists($item['tmp_name'][$i])) {
							$file = $this->file->getFileInfo($item['name'][$i]);
							if($file) {
								if(move_uploaded_file($item['tmp_name'][$i], $path.$i.'.jpg')) {
									$this->image->analyze($path.$i.'.jpg');
									if(in_array($_POST['type'], array(1,2)))
										$this->image->ToFile($path.$i.'.jpg', 80, $this->config->get('img_width','blocks_img'), $this->config->get('img_height','blocks_img'));
									else
									{
										$this->image->ToFile($path.$i.'.jpg', 80, $this->config->get('img_width','blocks_img'), $this->config->get('img_height','blocks_img'));
									}
								}
							}
						}
					}
				}
			}
			
			// -- лого для печати
			if(!empty($_FILES['logop']['tmp_name']) && file_exists($_FILES['logop']['tmp_name'])) {
				move_uploaded_file($_FILES['logop']['tmp_name'], INCLUDES.'images'.DS.'logop.jpg');
			}
			
			// -- формирование данных для таблицы поиска (главная страница)
			if(empty($form_id)) {
				$search = array(
					'pid'       => 0,
					'module_id' => 0,
				);
				$this->search->saveIndex($search);
			}
			
			// -- редирект
			if($form_id > 0) {
				$this->url->redirect('/admin/config/'.$form_id.'/');
			}
			$this->url->redirect('::referer');
		}
		
		// -- удаляем картинку из шапки
		/*public function delMainImg($id) {
			$path = INCLUDES.'img'.DS.'b'.DS.$id.'.jpg';
			if(file_exists($path)) {
				unlink($path);
				$this->db->delete('images', $id);
			}
			$this->session->set('alert', ALERT_DEL_IMAGE);
			$this->url->redirect('::referer');
		}*/
		
		public function question() {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			$data = array();
			if(!empty($_POST['action']) && $_POST['action'] == 'question') {
				$question = $this->html->render('letters/support.html', $_POST);
				$to_email = $this->config->get('mail','support');
				$send_config = array(
					'From'    => array($_POST['response_mail'], $_POST['from']),
					'ReplyTo' => array($_POST['response_mail'], $_POST['from'])
				);
				if($this->mail->send_mail($to_email, $question, false, false, $send_config)) {
					$this->session->set('alert', 'Ваш вопрос был успешно отправлен');
				} else {
					$this->session->set('alert', 'При отправке вопроса произошла ошибка');
				}
			}
			$this->html->render('layouts/question.html', $data, 'content_path');
		}
		
		public function add_config($array = false) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(3)) 
				$this->role_controller->AccessError();
			
			require(CONFIG.'config.inc.php');
			if(false !== $array) {
				$config['site'] = array_merge($config['site'],$array['site']);
				foreach($config['site'] as $key=>&$value) {
					$config['site'][$key] = htmlspecialchars_decode($config['site'][$key]);
				}
				$this->make_conf($config['site']);
			}
		}

		private function make_conf($array) {
			if(!is_array($array)) return false;
			$content = "<?php \n";
			foreach($array as $key=>&$value) {
				$value    = htmlspecialchars($value);
				$content .= '$config["site"]["'.$key.'"] = "'.$value.'";'."\n";
			}
			$content .= "?>";
			$this->file->toFile(APPLICATION.'config.inc.php', $content, 'w+', 0755, false);
			
		}

	}
?>
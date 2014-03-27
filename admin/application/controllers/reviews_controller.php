<?php
	class reviews_controller extends application_controller {
		
		protected $module_id = 14;
		protected $path;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('reviews', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$reviews = $this->reviews->getInfo($id);
			if(empty($reviews)) {
				$this->url->redirect('::referer');
			}
			
			// -- текст страницы
			if(file_exists($this->path.$id.'_volume.txt')) {
				$reviews['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- конфиг
			$reviews['config'] = unserialize($reviews['config']);
			$reviews['checked_send_notice'] = (!empty($reviews['config']['send_notice'])) ? ' checked="checked"' : '';
			$reviews['checked_out_valid']   = (!empty($reviews['config']['out_valid']))   ? ' checked="checked"' : '';
			//$reviews['template']            = (!empty($reviews['config']['template']))    ? $reviews['config']['template'] : 'layoutreviews';
			$reviews[$reviews['template'].'Selected'] = 'selected="selected"';
			
			$reviews['admin_dir'] = $this->config->get('admin_dir','system');
			$reviews['reviews_count'] = $count = $this->config->get('reviews_count','site');
			$reviews['pid']  = $id;

			
			// -- системные модули
			$reviews['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$reviews['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$reviews['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$reviews['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$reviews['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$reviews['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			// -- основной рендер
			$this->html->render('reviews/view.html', $reviews, 'content_path');
		}
		
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$reviews = $this->reviews->getInfo($id);
			if(empty($reviews)) {
				$this->url->redirect('::referer');
			}
			
			$reviews['pid']  = $id;
			$reviews['main_title'] = $this->db->get_one('SELECT title main_title FROM main WHERE id='.(int)$id);
			$reviews['reviews_count'] = $count = $this->config->get('admin_reviews_count','site');
			
			// -- список вопросов
			$reviews['list'] = $this->reviews->getreviews($id);
			if(!empty($reviews['list'])) {
				$gid = 0;
				$reviews_screen = array();
				foreach($reviews['list'] as $i => &$item) {
					// -- форматируем дату
					if(mb_strlen($item['question'], 'utf-8') > 50) {
						$item['question'] = mb_substr($item['question'], 0, 47, 'utf-8').'...';
					}
					if(empty($item['dateAnswer'])) {
						$item['fioUser']   = '<b>'.$item['fioUser'];
						$item['question'] .= '</b>';
					}
					$item['dateQuestion'] = join('.',array_reverse(explode('-',substr($item['dateQuestion'],0,10)))).substr($item['dateQuestion'],10,6);
					$reviews_screen[$gid]['list'][] = $item;
					if(count($reviews_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($reviews_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$reviews['list_screen'][] = array('html' => $this->html->render('reviews/view_list.html', $item));
					$reviews['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($reviews['list']);
				unset($reviews_screen);
			}
			
			if(!empty($reviews['nav_screen']) && count($reviews['nav_screen']) > 1) {
				$this->html->render('reviews/nav.html',  $reviews, 'nav');
			}
			
			$this->html->render('reviews/view_items.html', $reviews, 'content_path');
		}
		
		
		// -- редактируем вопрос по id
		public function edit($id) {
			$reviews = $this->reviews->getOne($id);
			if(empty($reviews)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $reviews['pid'])) $this->role_controller->AccessError();
			
			$this->active_id = $reviews['pid'];
			
			$reviews['main_title'] = $this->db->get_one('SELECT title main_title FROM main WHERE id='.(int)$reviews['pid']);
			
			$timestamp     = strtotime($reviews['dateQuestion']);
			$reviews['date']   = date('Y-m-d', $timestamp);
			$reviews['date_h'] = date('H', $timestamp);
			$reviews['date_m'] = date('i', $timestamp);

			$timestamp = (empty($reviews['dateAnswer'])) ? time() : strtotime($reviews['dateAnswer']);
			$reviews['date_a']   = date('Y-m-d', $timestamp);
			$reviews['date_a_h'] = date('H', $timestamp);
			$reviews['date_a_m'] = date('i', $timestamp);
			
			if(!empty($reviews['active'])) {
				$reviews['active_checked'] = ' checked="checked"';
			}
			$reviews['admin_dir'] = $this->config->get('admin_dir','system');
			
			$reviews['delete_page'] = '<a class="trash_button" href="/'.$this->admin_dir.'/reviews/delete/'.$id.'/" '.
									   'onClick=\'if(!confirm("Вы действительно хотите удалить?")) return false;\'>Удалить вопрос</a>';
			
			$this->html->render('reviews/edit.html', $reviews, 'content_path');
		}
		
		public function answer() {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $_POST['pid'])) $this->role_controller->AccessError();
			
			$_POST['active']       = (int)(!empty($_POST['active']));
			$_POST['dateQuestion'] = $this->date->sql_format($_POST['date'].' '.$_POST['date_h'].':'.$_POST['date_m'].':00', true);
			// $_POST['dateAnswer']   = $this->date->sql_format($_POST['date_a'].' '.$_POST['date_a_h'].':'.$_POST['date_a_m'].':00', true);
			unset($_POST['date']);   unset($_POST['date_a']);
			unset($_POST['date_h']); unset($_POST['date_a_h']);
			unset($_POST['date_m']); unset($_POST['date_a_m']);

			if(empty($_POST['id'])){
				$reviews = array(
					'ip'           => (!empty($_SERVER['HTTP_X_FORWARED_FOR'])) ? $_SERVER['HTTP_X_FORWARED_FOR'] : $_SERVER['REMOTE_ADDR'],
					'fioUser'      => (!empty($_POST['fioUser']))  ? trim($_POST['fioUser'])  : false,
					'postSpecialist'     => (!empty($_POST['postSpecialist']))  ? trim($_POST['postSpecialist'])  : false,
					'active'	   => $_POST['active'],
					// 'email'        => (!empty($_POST['email']))    ? trim($_POST['email'])    : false,
					// 'phone'        => (!empty($_POST['phone']))    ? trim($_POST['phone'])    : false,
					'question'     => (!empty($_POST['question'])) ? trim($_POST['question']) : false,
					'dateQuestion' => $_POST['dateQuestion'],
					'pid'          => $_POST['pid'],
					// 'company'	   => (!empty($_POST['company']))  ? trim($_POST['company'])  : false,
				);

				$id = $this->db->insert('reviews', $reviews);
			}
			else{	
				$id = $_POST['id'];

				if(!empty($_POST['active'])) {
					$data = $this->db->get_row('SELECT feedback, dateQuestion FROM reviews WHERE id = '.(int)$id);
					if($data['feedback'] == 1) {
						$data = array_merge($_POST, $data);
						$data['domain'] = $this->config->get('domain', 'site');
						$data['dateQuestion'] = $this->date->format($data['dateQuestion']);
						$data['dateAnswer']   = $this->date->format($data['dateAnswer']);
						$letter = $this->html->render('letters/reviews_user.html', $data);
						$this->mail->send_mail($data['email'], $letter);
						$_POST['feedback'] = 2;
					}
				}
				$answer = array(
					'fioUser' => $_POST['fioUser'],
					'postSpecialist' => $_POST['postSpecialist'],
					'question' => $_POST['question'],
					'active' => $_POST['active'],
					'id' => $_POST['id'],
					'pid' => $_POST['pid'],
					'dateQuestion' => $_POST['dateQuestion'],
				);
				$this->db->update('reviews', $answer, $id);
			}

			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','reviews'), $this->config->get('img_height','reviews'));
					}
				}
			}

			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/admin/reviews/view_items/'.$_POST['pid']);
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем в конфиг
			$reviews_count = ((int)$_POST['reviews_count'] < 1 || (int)$_POST['reviews_count'] > 100) ? 10 : (int)$_POST['reviews_count'];
			$this->config_controller->add_config(array('site'=>array('reviews_count'=>$reviews_count)));
			
			// -- сохраняем параметры раздела reviews
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
			$pid = $this->db->get_one('SELECT pid FROM reviews WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			$this->db->delete('reviews', (int)$id);
			unlink(INCLUDES.'reviews/'.$id.'.jpg');
			$this->url->redirect('::referer');
		}
		
		// -- групповое удаление
		public function group_delete() {
			if(!empty($_POST['ids'])) {
				$pid = $this->db->get_one('SELECT pid FROM reviews WHERE id = '.(int)$_POST['ids'][0]);
				if(empty($pid)) {
					$this->url->redirect('::referer');
				}
				
				//Проверяем права
				if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
				
				foreach($_POST['ids'] as $id) {
					$this->db->delete('reviews', (int)$id);
				}
			}
			$this->url->redirect('::referer');
		}

		// добавление отзыва (сперто из новостей)
		public function add_item($pid) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			$review = array(
				'id'             => 0,
				'pid'            => $pid,
				'caption'		 => 'Добавление отзыва',
				'date'           => $this->date->today(false),
				'module_id'      => $this->module_id,
				'module_name'    => 'reviews',
				'is_show_date'   => 'checked="checked"',
				'inmenu_checked' => 'checked="checked"'
			);
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->reviews->aboutReviewsCategory($review['pid']);
			if(!empty($volume)) {
				$review['volume_title'] = $volume['title'];
			}

			// -- основной шаблон
			$this->html->render('reviews/item.html', $review, 'content_path');
		}
		
	}
?>
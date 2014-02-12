<?php
	class partners_controller extends application_controller {
		
		protected $path;
		protected $module_id = 13;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('partners', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$partners = $this->partners->aboutpartnersCategory($id);
			if(empty($partners)) {
				$this->url->redirect('::referer');
			}
			$partners['pid']        = $id;
			$partners['partners_count'] = $count = $this->config->get('partners_count', 'site');
			$partners['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			$partners[$partners['template'].'Selected'] = 'selected="selected"';
			
			if(file_exists($this->path.$id.'_volume.txt')) {
				$partners['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- системные модули
			$partners['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$partners['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$partners['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$partners['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$partners['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$partners['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			$this->html->render('partners/view.html', $partners, 'content_path');
			unset($partners);
		}
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$partners = $this->partners->aboutpartnersCategory($id);
			if(empty($partners)) {
				$this->url->redirect('::referer');
			}
			$partners['pid']        = $id;
			$partners['partners_count'] = $count = $this->config->get('partners_count', 'site');
			$partners['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			// -- список новостей
			$partners['list'] = $this->partners->getpartnersList($id);
			if(!empty($partners['list'])) {
				$gid = 0;
				$partners_screen = array();
				foreach($partners['list'] as $i => &$item) {
					// -- форматируем дату
					$item['date'] = implode('.',array_reverse(explode('-',substr($item['date'],0,10))));
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
					$partners_screen[$gid]['list'][] = $item;
					if(count($partners_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($partners_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$partners['list_screen'][] = array('html' => $this->html->render('partners/view_list.html', $item));
					$partners['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($partners['list']);
				unset($partners_screen);
			}
			
			if(!empty($partners['nav_screen']) && count($partners['nav_screen']) > 1) {
				$this->html->render('partners/nav.html',  $partners, 'nav');
			}
			
			$this->html->render('partners/view_items.html', $partners, 'content_path');
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем сквозной параметр: кол-во новостей на странице
			$partners_count = (int)$_POST['partners_count'];
			$partners_count = ($partners_count < 1 || $partners_count > 100) ? 10 : $partners_count;
			$this->config_controller->add_config(array('site'=>array('partners_count'=>$partners_count)));
			
			// -- сохраняем параметры раздела новостей
			$main = array(
				'pid'         => (int)$_POST['pid'],
				'title'       => strip_tags($_POST['title']),
				'title_page'  => strip_tags($_POST['title_page']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'description' => strip_tags($_POST['description']),
				'keywords'    => strip_tags($_POST['keywords']),
				'template'    => $_POST['template'],
				'inmenu'      => (int)$_POST['inmenu'],
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)$_POST['feedback'],
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
				'subsection'  => (int)(!empty($_POST['subsection'])),
			);
			$this->db->update('main', $main, $id);
			
			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			$this->file->toFile($this->path.$id.'_volume.txt', $text);
			
			// -- особая ситуация: изменение основного модуля
			if((int)$_POST['module_id'] !== $this->module_id) {
				$this->module_controller->changeModule($id, $this->module_id, (int)$_POST['module_id'], $_POST['link']);
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}
		
		public function add_item($pid) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			$partners = array(
				'id'             => 0,
				'pid'            => $pid,
				'caption'		 => 'Добавление партнера',
				'date'           => $this->date->today(false),
				'module_id'      => $this->module_id,
				'module_name'    => 'partners',
				'is_show_date'   => 'checked="checked"',
				'inmenu_checked' => 'checked="checked"'
			);
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->partners->aboutpartnersCategory($partners['pid']);
			if(!empty($volume)) {
				$partners['volume_title'] = $volume['title'];
			}
			
			// -- системные модули
			$partners['modules_block']    = $this->all_controller->generateModulesBlock(0, $this->module_id, true, false);
			
			$this->html->render('submodules/images.html', $partners, 'images_form');
			$this->html->render('submodules/photos.html', $partners, 'photos_form');
			$this->html->render('submodules/files.html',  $partners, 'files_form');
			// -- основной шаблон
			$this->html->render('partners/item.html', $partners, 'content_path');
		}
		
		public function edit_item($id) {
			// -- выдираем информацию об одной новости по id
			$partners = $this->partners->getOnepartners($id);
			if(!$partners) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $partners['pid'])) $this->role_controller->AccessError();
			
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->partners->aboutpartnersCategory($partners['pid']);
			if(!empty($volume)) {
				$partners['volume_title'] = $volume['title'];
			}
			
			$this->active_id = $partners['pid'];
			
			$partners['active']   = $partners['active'] ? 'checked="checked"' : '';
			
			// -- прикрепляем текст
			$partners['text'] = (file_exists($this->path.$id.'.txt')) ? htmlspecialchars(file_get_contents($this->path.$id.'.txt')) : '';

			$partners['module_id']   = $this->module_id;
			$partners['module_name'] = 'partners';
			$partners['title']       = htmlspecialchars($partners['title']);
			$partners['delete_partners'] = '<a href="/'.$this->admin_dir.'/partners/delete_item/'.$id.'/" class="trash_button" '.
								   'onClick="if(!confirm(\'Вы действительно хотите удалить эту публикацию?\')) return false;">Удалить публикацию</a>';
						
			// -- системные модули
			$partners['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$partners['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
						
			// -- дополнительные модули
			$partners['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
			$partners['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
			$partners['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			
			$ts = (!is_numeric($partners['date'])) ? strtotime($partners['date']) : $partners['date'];
			$partners['time'] = (empty($partners['date']))? date('H:i:s') : date('H:i:s', $ts);			
			$partners['date'] = (empty($partners['date']))?$this->date->sql_format(time()):$this->date->sql_format($ts);
			$partners['character'] = (file_exists($this->path.$id.'.jpg'))?'<a href="/application/includes/partners/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/partners/deleteImg/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			
			if(!empty($partners['is_show_date'])) {
				$partners['is_show_date'] = 'checked="checked"';
			} else {
				$partners['date_disabled'] = 'disabled="disabled"';
			}
			
			if(!empty($partners['active'])) {
				$partners['active'] = 'checked="checked"';
			}
			
			$partners['caption'] = $partners['title'];
			
			// -- основной шаблон
			$this->html->render('partners/item.html', $partners, 'content_path');
		}
		
		public function add_partners() {
			$id   = (int)$_POST['id'];
			$date = $_POST['date'].' '.$_POST['time'];
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			$partners = array(
				'pid'          => (int)$_POST['pid'],
				'title'        => strip_tags($_POST['title']),
				'note'         => $_POST['note'],
				// 'task'         => strip_tags($_POST['task']),
				// 'created'      => strip_tags($_POST['created']),
				// 'result'       => strip_tags($_POST['result']),
				// 'review_head'  => strip_tags($_POST['review_head']),
				// 'review'       => strip_tags($_POST['review']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),

				'source'       => strip_tags($_POST['source']),
				'date'         => $this->date->sql_format($date, true),
				'active' 	   => (int)$_POST['active']>0 ? 1 : 0,
				'is_show_date' => (int)$_POST['is_show_date'],
				'print'        => (int)(!empty($_POST['print'])),
				'feedback'     => (int)$_POST['feedback'],
				'sendfile'     => (int)(!empty($_POST['sendfile'])),
			);
			
			if(empty($id)) {
				$id = $this->db->insert('partners', $partners);
			} else {
				$this->db->update('partners', $partners, $id);
			}
			
			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			$this->file->toFile($this->path.$id.'.txt', $text);
			
			
			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => $this->module_id,
				'title'     => trim($partners['title']),
				'text'      => trim($_POST['text']),
			);
			$this->search->saveIndex($search);
			
			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','partners'), $this->config->get('img_height','partners'));
					}
				}
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/'.$this->admin_dir.'/partners/edit_item/'.$id.'/');
		}
		
		public function deleteImg($id) {
			$pid = $this->db->get_one('SELECT pid FROM partners WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->session->set('alert', ALERT_DEL_IMAGE);
			$this->url->redirect('::referer');
		}

		// -- удаление одной записи
		public function delete_item($id) {
			$this->delete($id);
			
			$pid = $this->db->get_one('SELECT pid FROM partners WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			$this->url->redirect('/admin/partners/view_items/'.$pid.'/');
		}
		
		// -- групповое удаление
		public function group_delete() {
			if(!empty($_POST['ids'])) {
				foreach($_POST['ids'] as $id) {
					$this->delete($id);
				}
			}
			$this->url->redirect('::referer');
		}
		
		private function delete($id) {
			$pid = $this->db->get_one('SELECT pid FROM partners WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			$this->db->delete('partners', (int)$id);
			
			if (file_exists($this->path.$id.'.txt')) unlink($this->path.$id.'.txt');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->trash_controller->delete_addition($id, $this->module_id, true);
		}		
	}
?>
<?php
	class actions_controller extends application_controller {
		
		protected $path;
		protected $module_id = 12;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('actions', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$actions = $this->actions->aboutactionsCategory($id);
			if(empty($actions)) {
				$this->url->redirect('::referer');
			}
			$actions['pid']        = $id;
			$actions['actions_count'] = $count = $this->config->get('actions_count', 'site');
			$actions['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			$actions[$actions['template'].'Selected'] = 'selected="selected"';
			
			if(file_exists($this->path.$id.'_volume.txt')) {
				$actions['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- системные модули
			$actions['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$actions['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$actions['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$actions['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$actions['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$actions['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			$this->html->render('actions/view.html', $actions, 'content_path');
			unset($actions);
		}
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$actions = $this->actions->aboutactionsCategory($id);
			if(empty($actions)) {
				$this->url->redirect('::referer');
			}
			$actions['pid']        = $id;
			$actions['actions_count'] = $count = $this->config->get('admin_actions_count', 'site');
			$actions['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			// -- список новостей
			$actions['list'] = $this->actions->getactionsList($id);
			if(!empty($actions['list'])) {
				$gid = 0;
				$actions_screen = array();
				foreach($actions['list'] as $i => &$item) {
					// -- форматируем дату
					$item['date'] = implode('.',array_reverse(explode('-',substr($item['date'],0,10))));
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
					$actions_screen[$gid]['list'][] = $item;
					if(count($actions_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($actions_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$actions['list_screen'][] = array('html' => $this->html->render('actions/view_list.html', $item));
					$actions['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($actions['list']);
				unset($actions_screen);
			}
			
			if(!empty($actions['nav_screen']) && count($actions['nav_screen']) > 1) {
				$this->html->render('actions/nav.html',  $actions, 'nav');
			}
			
			$this->html->render('actions/view_items.html', $actions, 'content_path');
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем сквозной параметр: кол-во новостей на странице
			$actions_count = (int)$_POST['actions_count'];
			$actions_count = ($actions_count < 1 || $actions_count > 100) ? 10 : $actions_count;
			$this->config_controller->add_config(array('site'=>array('actions_count'=>$actions_count)));
			
			// -- сохраняем параметры раздела новостей
			$main = array(
				'pid'         => (int)$_POST['pid'],
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'title'       => strip_tags($_POST['title']),
				'title_page'  => strip_tags($_POST['title_page']),
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
			
			$actions = array(
				'id'             => 0,
				'pid'            => $pid,
				'caption'		 => 'Добавление новости',
				'date'           => $this->date->today(false),
				'module_id'      => $this->module_id,
				'module_name'    => 'actions',
				'is_show_date'   => 'checked="checked"',
				'inmenu_checked' => 'checked="checked"'
			);
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->actions->aboutactionsCategory($actions['pid']);
			if(!empty($volume)) {
				$actions['volume_title'] = $volume['title'];
			}
			
			// -- системные модули
			$actions['modules_block']    = $this->all_controller->generateModulesBlock(0, $this->module_id, true, false);
			
			$this->html->render('submodules/images.html', $actions, 'images_form');
			$this->html->render('submodules/photos.html', $actions, 'photos_form');
			$this->html->render('submodules/files.html',  $actions, 'files_form');
			// -- основной шаблон
			$this->html->render('actions/item.html', $actions, 'content_path');
		}
		
		public function edit_item($id) {
			// -- выдираем информацию об одной новости по id
			$actions = $this->actions->getOneactions($id);
			if(!$actions) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $actions['pid'])) $this->role_controller->AccessError();
			
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->actions->aboutactionsCategory($actions['pid']);
			if(!empty($volume)) {
				$actions['volume_title'] = $volume['title'];
			}
			
			$this->active_id = $actions['pid'];
			
			$actions['active']   = $actions['active'] ? 'checked="checked"' : '';
			$actions['proceed']   = $actions['proceed'] ? 'checked="checked"' : '';
			
			// -- прикрепляем текст
			$actions['text'] = (file_exists($this->path.$id.'.txt')) ? htmlspecialchars(file_get_contents($this->path.$id.'.txt')) : '';

			$actions['module_id']   = $this->module_id;
			$actions['module_name'] = 'actions';
			$actions['title']       = htmlspecialchars($actions['title']);
			$actions['delete_actions'] = '<a href="/'.$this->admin_dir.'/actions/delete_item/'.$id.'/" class="trash_button" '.
								   'onClick="if(!confirm(\'Вы действительно хотите удалить эту публикацию?\')) return false;">Удалить публикацию</a>';
						
			// -- системные модули
			$actions['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$actions['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
						
			// -- дополнительные модули
			$actions['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
			$actions['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
			$actions['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			
			$ts = (!is_numeric($actions['date'])) ? strtotime($actions['date']) : $actions['date'];
			$actions['time'] = (empty($actions['date']))? date('H:i:s') : date('H:i:s', $ts);			
			$actions['date'] = (empty($actions['date']))?$this->date->sql_format(time()):$this->date->sql_format($ts);
			$actions['character'] = (file_exists($this->path.$id.'.jpg'))?'<a href="/application/includes/actions/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/actions/deleteImg/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			$actions['character_banner'] = (file_exists(INCLUDES.'actions/b/'.$id.'.jpg'))?'<a href="/application/includes/actions/b/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/actions/deleteBanner/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить баннер?\')) return false;">Удалить</a>':'';
			
			if(!empty($actions['is_show_date'])) {
				$actions['is_show_date'] = 'checked="checked"';
			} else {
				$actions['date_disabled'] = 'disabled="disabled"';
			}
			
			if(!empty($actions['active'])) {
				$actions['active'] = 'checked="checked"';
			}
			
			$actions['caption'] = $actions['title'];
			
			// -- основной шаблон
			$this->html->render('actions/item.html', $actions, 'content_path');
		}
		
		public function add_actions() {
			$id   = (int)$_POST['id'];
			// $date = $_POST['date'].' '.$_POST['time'];
			$date = date('Y-m-d H:i:s');

			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			$actions = array(
				'pid'          => (int)$_POST['pid'],
				'title'        => strip_tags($_POST['title']),
				'note'         => $_POST['note'],
				'author'       => strip_tags($_POST['author']),
				'source'       => strip_tags($_POST['source']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'active' 	   => (int)$_POST['active']>0 ? 1 : 0,
				'proceed' 	   => (int)$_POST['proceed']>0 ? 1 : 0,
				'is_show_date' => (int)$_POST['is_show_date'],
				'print'        => (int)(!empty($_POST['print'])),
				'feedback'     => (int)$_POST['feedback'],
				'sendfile'     => (int)(!empty($_POST['sendfile'])),
			);
			
			if(empty($id)) {
				$actions['date'] = $date;
				$id = $this->db->insert('actions', $actions);
			} else {
				$this->db->update('actions', $actions, $id);
			}
			
			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			$this->file->toFile($this->path.$id.'.txt', $text);		
			
			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => $this->module_id,
				'title'     => trim($actions['title']),
				'text'      => trim($_POST['text']),
			);
			$this->search->saveIndex($search);
			
			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','actions'), $this->config->get('img_height','actions'));
					}
				}
			}
			
			if(isset($_FILES['banner_image']['tmp_name']) && file_exists($_FILES['banner_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['banner_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['banner_image']['tmp_name'], $this->path.'b/'.$id.'.jpg')) {
						$this->image->analyze($this->path.'b/'.$id.'.jpg');
						$this->image->ToFile($this->path.'b/'.$id.'.jpg', 80, $this->config->get('img_width','actions_banner'), $this->config->get('img_height','actions_banner'));
					}
				}
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/'.$this->admin_dir.'/actions/edit_item/'.$id.'/');
		}
		
		public function deleteImg($id) {
			$pid = $this->db->get_one('SELECT pid FROM actions WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->session->set('alert', ALERT_DEL_IMAGE);
			$this->url->redirect('::referer');
		}
		
		public function deleteBanner($id) {
			$pid = $this->db->get_one('SELECT pid FROM actions WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) $this->role_controller->AccessError();
			
			if (file_exists(INCLUDES.'actions/b/'.$id.'.jpg')) unlink(INCLUDES.'actions/b/'.$id.'.jpg');
			
			$this->session->set('alert', 'Баннер был успешно удален');
			$this->url->redirect('::referer');
		}

		// -- удаление одной записи
		public function delete_item($id) {
			$this->delete($id);
			
			$pid = $this->db->get_one('SELECT pid FROM actions WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			$this->url->redirect('/admin/actions/view_items/'.$pid.'/');
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
			$pid = $this->db->get_one('SELECT pid FROM actions WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			$this->db->delete('actions', (int)$id);
			
			if (file_exists($this->path.$id.'.txt')) unlink($this->path.$id.'.txt');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->trash_controller->delete_addition($id, $this->module_id, true);
		}		
	}
?>
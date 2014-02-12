<?php
	class articles_controller extends application_controller {
		
		protected $path;
		protected $module_id = 10;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('articles', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$articles = $this->articles->aboutarticlesCategory($id);
			if(empty($articles)) {
				$this->url->redirect('::referer');
			}
			$articles['pid']        = $id;
			$articles['articles_count'] = $count = $this->config->get('articles_count', 'site');
			$articles['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			$articles[$articles['template'].'Selected'] = 'selected="selected"';
			
			if(file_exists($this->path.$id.'_volume.txt')) {
				$articles['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- системные модули
			$articles['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$articles['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);

			$articles['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$articles['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$articles['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$articles['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			$this->html->render('articles/view.html', $articles, 'content_path');
			unset($articles);
		}
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$articles = $this->articles->aboutarticlesCategory($id);
			if(empty($articles)) {
				$this->url->redirect('::referer');
			}
			$articles['pid']        = $id;
			$articles['articles_count'] = $count = $this->config->get('articles_count', 'site');
			$articles['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			// -- список новостей
			$articles['list'] = $this->articles->getarticlesList($id);
			if(!empty($articles['list'])) {
				$gid = 0;
				$articles_screen = array();
				foreach($articles['list'] as $i => &$item) {
					// -- форматируем дату
					$item['date'] = implode('.',array_reverse(explode('-',substr($item['date'],0,10))));
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
					$articles_screen[$gid]['list'][] = $item;
					if(count($articles_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($articles_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$articles['list_screen'][] = array('html' => $this->html->render('articles/view_list.html', $item));
					$articles['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($articles['list']);
				unset($articles_screen);
			}
			
			if(!empty($articles['nav_screen']) && count($articles['nav_screen']) > 1) {
				$this->html->render('articles/nav.html',  $articles, 'nav');
			}
			
			$this->html->render('articles/view_items.html', $articles, 'content_path');
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем сквозной параметр: кол-во новостей на странице
			$articles_count = (int)$_POST['articles_count'];
			$articles_count = ($articles_count < 1 || $articles_count > 100) ? 10 : $articles_count;
			$this->config_controller->add_config(array('site'=>array('articles_count'=>$articles_count)));
			
			// -- сохраняем параметры раздела новостей
			$main = array(
				'pid'         => (int)$_POST['pid'],
				'title'       => strip_tags($_POST['title']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
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


			$articles = array(
				'id'             => 0,
				'pid'            => $pid,
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'caption'		 => 'Добавление новости',
				'date'           => $this->date->today(false),
				'module_id'      => $this->module_id,
				'module_name'    => 'articles',
				'is_show_date'   => 'checked="checked"',
				'inmenu_checked' => 'checked="checked"'
			);
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->articles->aboutarticlesCategory($articles['pid']);
			if(!empty($volume)) {
				$articles['volume_title'] = $volume['title'];
			}
			
			// -- системные модули
			$articles['modules_block']    = $this->all_controller->generateModulesBlock(0, $this->module_id, true, false);
			
			$this->html->render('submodules/images.html', $articles, 'images_form');
			$this->html->render('submodules/photos.html', $articles, 'photos_form');
			$this->html->render('submodules/files.html',  $articles, 'files_form');
			// -- основной шаблон
			$this->html->render('articles/item.html', $articles, 'content_path');
		}
		
		public function edit_item($id) {
			// -- выдираем информацию об одной новости по id
			$articles = $this->articles->getOnearticles($id);
			if(!$articles) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $articles['pid'])) $this->role_controller->AccessError();
			
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->articles->aboutarticlesCategory($articles['pid']);
			if(!empty($volume)) {
				$articles['volume_title'] = $volume['title'];
			}
			
			$this->active_id = $articles['pid'];
			
			$articles['active']   = $articles['active'] ? 'checked="checked"' : '';
			
			// -- прикрепляем текст
			$articles['text'] = (file_exists($this->path.$id.'.txt')) ? htmlspecialchars(file_get_contents($this->path.$id.'.txt')) : '';

			$articles['module_id']   = $this->module_id;
			$articles['module_name'] = 'articles';
			$articles['title']       = htmlspecialchars($articles['title']);
			$articles['delete_articles'] = '<a href="/'.$this->admin_dir.'/articles/delete_item/'.$id.'/" class="trash_button" '.
								   'onClick="if(!confirm(\'Вы действительно хотите удалить эту публикацию?\')) return false;">Удалить публикацию</a>';
						
			// -- системные модули
			$articles['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$articles['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
						
			// -- дополнительные модули
			$articles['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
			$articles['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
			$articles['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			
			$ts = (!is_numeric($articles['date'])) ? strtotime($articles['date']) : $articles['date'];
			$articles['time'] = (empty($articles['date']))? date('H:i:s') : date('H:i:s', $ts);			
			$articles['date'] = (empty($articles['date']))?$this->date->sql_format(time()):$this->date->sql_format($ts);
			$articles['character'] = (file_exists($this->path.$id.'.jpg'))?'<a href="/application/includes/articles/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/articles/deleteImg/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			
			if(!empty($articles['is_show_date'])) {
				$articles['is_show_date'] = 'checked="checked"';
			} else {
				$articles['date_disabled'] = 'disabled="disabled"';
			}
			
			if(!empty($articles['active'])) {
				$articles['active'] = 'checked="checked"';
			}
			
			$articles['caption'] = $articles['title'];
			
			// -- основной шаблон
			$this->html->render('articles/item.html', $articles, 'content_path');
		}
		
		public function add_articles() {
			$id   = (int)$_POST['id'];
			$date = $_POST['date'].' '.$_POST['time'];
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			$articles = array(
				'pid'          => (int)$_POST['pid'],
				'title'        => strip_tags($_POST['title']),
				'note'         => $_POST['note'],
				'author'       => strip_tags($_POST['author']),
				'source'       => strip_tags($_POST['source']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'date'         => $this->date->sql_format($date, true),
				'active' 	   => (int)$_POST['active']>0 ? 1 : 0,
				'is_show_date' => (int)$_POST['is_show_date'],
				'print'        => (int)(!empty($_POST['print'])),
				'feedback'     => (int)$_POST['feedback'],
				'sendfile'     => (int)(!empty($_POST['sendfile'])),
			);
			
			if(empty($id)) {
				$id = $this->db->insert('articles', $articles);
			} else {
				$this->db->update('articles', $articles, $id);
			}
			
			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			$this->file->toFile($this->path.$id.'.txt', $text);
			
			
			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => $this->module_id,
				'title'     => trim($articles['title']),
				'text'      => trim($_POST['text']),
			);
			$this->search->saveIndex($search);
			
			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','articles'), $this->config->get('img_height','articles'));
					}
				}
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/'.$this->admin_dir.'/articles/edit_item/'.$id.'/');
		}
		
		public function deleteImg($id) {
			$pid = $this->db->get_one('SELECT pid FROM articles WHERE id = '.(int)$id);
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
			
			$pid = $this->db->get_one('SELECT pid FROM articles WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			$this->url->redirect('/admin/articles/view_items/'.$pid.'/');
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
			$pid = $this->db->get_one('SELECT pid FROM articles WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			$this->db->delete('articles', (int)$id);
			
			if (file_exists($this->path.$id.'.txt')) unlink($this->path.$id.'.txt');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->trash_controller->delete_addition($id, $this->module_id, true);
		}		
	}
?>
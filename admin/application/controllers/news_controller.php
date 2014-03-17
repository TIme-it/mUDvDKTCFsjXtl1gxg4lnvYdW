<?php
	class news_controller extends application_controller {
		
		protected $path;
		protected $module_id = 2;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('news', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$news = $this->news->aboutNewsCategory($id);
			if(empty($news)) {
				$this->url->redirect('::referer');
			}
			$news['pid']        = $id;
			$news['news_count'] = $count = $this->config->get('news_count', 'site');
			$news['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			$news[$news['template'].'Selected'] = 'selected="selected"';
			
			if(file_exists($this->path.$id.'_volume.txt')) {
				$news['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- системные модули
			$news['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$news['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$news['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$news['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$news['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$news['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			$this->html->render('news/view.html', $news, 'content_path');
			unset($news);
		}
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$news = $this->news->aboutNewsCategory($id);
			if(empty($news)) {
				$this->url->redirect('::referer');
			}
			$news['pid']        = $id;
			$news['news_count'] = $count = $this->config->get('admin_news_count', 'site');
			$news['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			// -- список новостей
			$news['list'] = $this->news->getNewsList($id);
			if(!empty($news['list'])) {
				$gid = 0;
				$news_screen = array();
				foreach($news['list'] as $i => &$item) {
					// -- форматируем дату
					$item['date'] = implode('.',array_reverse(explode('-',substr($item['date'],0,10))));
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
					$news_screen[$gid]['list'][] = $item;
					if(count($news_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($news_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$news['list_screen'][] = array('html' => $this->html->render('news/view_list.html', $item));
					$news['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($news['list']);
				unset($news_screen);
			}
			
			if(!empty($news['nav_screen']) && count($news['nav_screen']) > 1) {
				$this->html->render('news/nav.html',  $news, 'nav');
			}
			
			$this->html->render('news/view_items.html', $news, 'content_path');
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем сквозной параметр: кол-во новостей на странице
			$news_count = (int)$_POST['news_count'];
			$news_count = ($news_count < 1 || $news_count > 100) ? 10 : $news_count;
			$this->config_controller->add_config(array('site'=>array('news_count'=>$news_count)));
			
			// -- сохраняем параметры раздела новостей
			$main = array(
				'pid'         => (int)$_POST['pid'],
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
			
			$news = array(
				'id'             => 0,
				'pid'            => $pid,
				'caption'		 => 'Добавление новости',
				'date'           => $this->date->today(false),
				'module_id'      => $this->module_id,
				'module_name'    => 'news',
				'is_show_date'   => 'checked="checked"',
				'inmenu_checked' => 'checked="checked"'
			);
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->news->aboutNewsCategory($news['pid']);
			if(!empty($volume)) {
				$news['volume_title'] = $volume['title'];
			}
			
			// -- системные модули
			$news['modules_block']    = $this->all_controller->generateModulesBlock(0, $this->module_id, true, false);
			
			$this->html->render('submodules/images.html', $news, 'images_form');
			$this->html->render('submodules/photos.html', $news, 'photos_form');
			$this->html->render('submodules/files.html',  $news, 'files_form');
			// -- основной шаблон
			$this->html->render('news/item.html', $news, 'content_path');
		}
		
		public function edit_item($id) {
			// -- выдираем информацию об одной новости по id
			$news = $this->news->getOneNews($id);
			if(!$news) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $news['pid'])) $this->role_controller->AccessError();
			
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->news->aboutNewsCategory($news['pid']);
			if(!empty($volume)) {
				$news['volume_title'] = $volume['title'];
			}
			
			$this->active_id = $news['pid'];
			
			$news['active']   = $news['active'] ? 'checked="checked"' : '';
			
			
			// -- прикрепляем текст
			$news['text'] = (file_exists($this->path.$id.'.txt')) ? htmlspecialchars(file_get_contents($this->path.$id.'.txt')) : '';

			$news['module_id']   = $this->module_id;
			$news['module_name'] = 'news';
			$news['title']       = htmlspecialchars($news['title']);
			$news['delete_news'] = '<a href="/'.$this->admin_dir.'/news/delete_item/'.$id.'/" class="trash_button" '.
								   'onClick="if(!confirm(\'Вы действительно хотите удалить эту публикацию?\')) return false;">Удалить публикацию</a>';
						
			// -- системные модули
			$news['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$news['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
						
			// -- дополнительные модули
			$news['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
			$news['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
			$news['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			
			$ts = (!is_numeric($news['date'])) ? strtotime($news['date']) : $news['date'];
			$news['time'] = (empty($news['date']))? date('H:i:s') : date('H:i:s', $ts);			
			$news['date'] = (empty($news['date']))?$this->date->sql_format(time()):$this->date->sql_format($ts);
			$news['character'] = (file_exists($this->path.$id.'.jpg'))?'<a href="/application/includes/news/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/news/deleteImg/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			
			if(!empty($news['is_show_date'])) {
				$news['is_show_date'] = 'checked="checked"';
			} else {
				$news['date_disabled'] = 'disabled="disabled"';
			}
			
			if(!empty($news['active'])) {
				$news['active'] = 'checked="checked"';
			}
			
			$news['caption'] = $news['title'];

			// var_dump($news['feedback_checked']);
			// die();
			
			// -- основной шаблон
			$this->html->render('news/item.html', $news, 'content_path');
		}
		
		public function add_news() {
			$id   = (int)$_POST['id'];
			// $date = $_POST['date'].' '.$_POST['time'];
			$date = date('Y-m-d H:i:s');
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			$news = array(
				'pid'          => (int)$_POST['pid'],
				'title'        => strip_tags($_POST['title']),
				'note'         => $_POST['note'],
				'author'       => strip_tags($_POST['author']),
				'source'       => strip_tags($_POST['source']),
				'alias'        => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'active' 	   => (int)$_POST['active']>0 ? 1 : 0,
				'is_show_date' => (int)$_POST['is_show_date'],
				'print'        => (int)(!empty($_POST['print'])),
				'feedback'     => (int)$_POST['feedback'],
				'sendfile'     => (int)(!empty($_POST['sendfile'])),
			);
			
			if(empty($id)) {
				$news['date'] = $date;
				$id = $this->db->insert('news', $news);
			} else {
				$this->db->update('news', $news, $id);
			}
			
			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			$this->file->toFile($this->path.$id.'.txt', $text);
			
			
			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => $this->module_id,
				'title'     => trim($news['title']),
				'text'      => trim($_POST['text']),
			);
			$this->search->saveIndex($search);
			
			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','news'), $this->config->get('img_height','news'));
					}
				}
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/'.$this->admin_dir.'/news/edit_item/'.$id.'/');
		}
		
		public function deleteImg($id) {
			$pid = $this->db->get_one('SELECT pid FROM news WHERE id = '.(int)$id);
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
			
			$pid = $this->db->get_one('SELECT pid FROM news WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			$this->url->redirect('/admin/news/view_items/'.$pid.'/');
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
			$pid = $this->db->get_one('SELECT pid FROM news WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			$this->db->delete('news', (int)$id);
			
			if (file_exists($this->path.$id.'.txt')) unlink($this->path.$id.'.txt');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->trash_controller->delete_addition($id, $this->module_id, true);
		}		
	}
?>
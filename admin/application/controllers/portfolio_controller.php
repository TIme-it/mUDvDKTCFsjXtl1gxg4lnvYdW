<?php
	class portfolio_controller extends application_controller {
		
		protected $path;
		protected $module_id = 11;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('portfolio', 'files');
		}
		
		public function index() {
			// empty
		}
		
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$portfolio = $this->portfolio->aboutportfolioCategory($id);
			if(empty($portfolio)) {
				$this->url->redirect('::referer');
			}
			$portfolio['pid']        = $id;
			$portfolio['portfolio_count'] = $count = $this->config->get('portfolio_count', 'site');
			$portfolio['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			$portfolio[$portfolio['template'].'Selected'] = 'selected="selected"';
			
			if(file_exists($this->path.$id.'_volume.txt')) {
				$portfolio['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			
			// -- системные модули
			$portfolio['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$portfolio['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$portfolio['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- дополнительные модули
			$portfolio['images_block']      = $this->addmodules_controller->generateImagesBlock($id, 0);
			$portfolio['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, 0);
			$portfolio['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  0);
			
			$this->html->render('portfolio/view.html', $portfolio, 'content_path');
			unset($portfolio);
		}
		
		public function view_items($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$portfolio = $this->portfolio->aboutportfolioCategory($id);
			if(empty($portfolio)) {
				$this->url->redirect('::referer');
			}
			$portfolio['pid']        = $id;
			$portfolio['portfolio_count'] = $count = $this->config->get('portfolio_count', 'site');
			$portfolio['admin_dir']  = $this->config->get('admin_dir', 'system');
			
			// -- список новостей
			$portfolio['list'] = $this->portfolio->getportfolioList($id);
			if(!empty($portfolio['list'])) {
				$gid = 0;
				$portfolio_screen = array();
				foreach($portfolio['list'] as $i => &$item) {
					// -- форматируем дату
					$item['date'] = implode('.',array_reverse(explode('-',substr($item['date'],0,10))));
					$item['full_title'] = htmlspecialchars($item['title']);
					if(mb_strlen($item['title'], 'utf-8') > 70) {
						$item['title'] = mb_substr($item['title'], 0, 67, 'utf-8').'...';
					}
					$portfolio_screen[$gid]['list'][] = $item;
					if(count($portfolio_screen[$gid]['list']) == $count && $count > 0) {
						$gid++;
					}
				}
				foreach($portfolio_screen as $gid => &$item) {
					$item['style'] = ($gid >= 1 ? 'style="display: none"' : '');
					$item['gid']   = $gid + 1;
					$portfolio['list_screen'][] = array('html' => $this->html->render('portfolio/view_list.html', $item));
					$portfolio['nav_screen'][]  = array(
						'num'   => $gid + 1,
						'class' => ($gid == 0 ? 'act' : '')
					);
				}
				unset($portfolio['list']);
				unset($portfolio_screen);
			}
			
			if(!empty($portfolio['nav_screen']) && count($portfolio['nav_screen']) > 1) {
				$this->html->render('portfolio/nav.html',  $portfolio, 'nav');
			}
			
			$this->html->render('portfolio/view_items.html', $portfolio, 'content_path');
		}
		
		public function edit_category($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			// -- сохраняем сквозной параметр: кол-во новостей на странице
			$portfolio_count = (int)$_POST['portfolio_count'];
			$portfolio_count = ($portfolio_count < 1 || $portfolio_count > 100) ? 10 : $portfolio_count;
			$this->config_controller->add_config(array('site'=>array('portfolio_count'=>$portfolio_count)));
			
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
			
			$portfolio = array(
				'id'             => 0,
				'pid'            => $pid,
			
				'caption'		 => 'Добавление новости',
				'date'           => $this->date->today(false),
				'module_id'      => $this->module_id,
				'module_name'    => 'portfolio',
				'is_show_date'   => 'checked="checked"',
				'inmenu_checked' => 'checked="checked"'
			);
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->portfolio->aboutportfolioCategory($portfolio['pid']);
			if(!empty($volume)) {
				$portfolio['volume_title'] = $volume['title'];
			}
			
			// -- системные модули
			$portfolio['modules_block']    = $this->all_controller->generateModulesBlock(0, $this->module_id, true, false);
			
			$this->html->render('submodules/images.html', $portfolio, 'images_form');
			$this->html->render('submodules/photos.html', $portfolio, 'photos_form');
			$this->html->render('submodules/files.html',  $portfolio, 'files_form');
			// -- основной шаблон
			$this->html->render('portfolio/item.html', $portfolio, 'content_path');
		}
		
		public function edit_item($id) {
			// -- выдираем информацию об одной новости по id
			$portfolio = $this->portfolio->getOneportfolio($id);
			if(!$portfolio) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $portfolio['pid'])) $this->role_controller->AccessError();
			
			// -- выдираем информацию об разделе новости по pid
			$volume = $this->portfolio->aboutportfolioCategory($portfolio['pid']);
			if(!empty($volume)) {
				$portfolio['volume_title'] = $volume['title'];
			}
			
			$this->active_id = $portfolio['pid'];
			
			$portfolio['active']   = $portfolio['active'] ? 'checked="checked"' : '';
			
			// -- прикрепляем текст
			$portfolio['text'] = (file_exists($this->path.$id.'.txt')) ? htmlspecialchars(file_get_contents($this->path.$id.'.txt')) : '';

			$portfolio['module_id']   = $this->module_id;
			$portfolio['module_name'] = 'portfolio';
			$portfolio['title']       = htmlspecialchars($portfolio['title']);
			$portfolio['delete_portfolio'] = '<a href="/'.$this->admin_dir.'/portfolio/delete_item/'.$id.'/" class="trash_button" '.
								   'onClick="if(!confirm(\'Вы действительно хотите удалить эту публикацию?\')) return false;">Удалить публикацию</a>';
						
			// -- системные модули
			$portfolio['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$portfolio['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
					
			// -- дополнительные модули
			$portfolio['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
			$portfolio['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
			$portfolio['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
			
			$ts = (!is_numeric($portfolio['date'])) ? strtotime($portfolio['date']) : $portfolio['date'];
			$portfolio['time'] = (empty($portfolio['date']))? date('H:i:s') : date('H:i:s', $ts);			
			$portfolio['date'] = (empty($portfolio['date']))?$this->date->sql_format(time()):$this->date->sql_format($ts);
			$portfolio['character'] = (file_exists($this->path.$id.'.jpg'))?'<a href="/application/includes/portfolio/'.$id.'.jpg" target="_blank">Смотреть</a> <a href="/admin/portfolio/deleteImg/'.$id.'/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			
			if(!empty($portfolio['is_show_date'])) {
				$portfolio['is_show_date'] = 'checked="checked"';
			} else {
				$portfolio['date_disabled'] = 'disabled="disabled"';
			}
			
			if(!empty($portfolio['active'])) {
				$portfolio['active'] = 'checked="checked"';
			}
			
			if(!empty($portfolio['show_in_main'])) {
				$portfolio['show_in_main'] = 'checked="checked"';
			}
			
			$portfolio['caption'] = $portfolio['title'];
			
			// -- основной шаблон
			$this->html->render('portfolio/item.html', $portfolio, 'content_path');
		}
		
		public function add_portfolio() {
			$id   = (int)$_POST['id'];
			$date = $_POST['date'].' '.$_POST['time'];
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, (int)$_POST['pid'])) $this->role_controller->AccessError();
			
			$portfolio = array(
				'pid'          => (int)$_POST['pid'],
				'title'        => strip_tags($_POST['title']),
				'note'         => $_POST['note'],
				'task'         => strip_tags($_POST['task']),
				'created'      => strip_tags($_POST['created']),
				'result'       => strip_tags($_POST['result']),
				'review_head'  => strip_tags($_POST['review_head']),
				'review'       => strip_tags($_POST['review']),
				'alias' 	  => $this->all_controller->rus2translit(strip_tags($_POST['alias'])),
				'source'       => strip_tags($_POST['source']),
				'date'         => $this->date->sql_format($date, true),
				'active' 	   => (int)$_POST['active']>0 ? 1 : 0,
				'show_in_main' => (int)$_POST['show_in_main'],
				'is_show_date' => (int)$_POST['is_show_date'],
				'print'        => (int)(!empty($_POST['print'])),
				'feedback'     => (int)$_POST['feedback'],
				'sendfile'     => (int)(!empty($_POST['sendfile'])),
			);
			
			if(empty($id)) {
				$id = $this->db->insert('portfolio', $portfolio);
			} else {
				$this->db->update('portfolio', $portfolio, $id);
			}
			
			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			$this->file->toFile($this->path.$id.'.txt', $text);
			
			
			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => $this->module_id,
				'title'     => trim($portfolio['title']),
				'text'      => trim($_POST['text']),
			);
			$this->search->saveIndex($search);
			
			if(isset($_FILES['character_image']['tmp_name']) && file_exists($_FILES['character_image']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['character_image']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['character_image']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','portfolio'), $this->config->get('img_height','portfolio'));
					}
				}
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/'.$this->admin_dir.'/portfolio/edit_item/'.$id.'/');
		}
		
		public function deleteImg($id) {
			$pid = $this->db->get_one('SELECT pid FROM portfolio WHERE id = '.(int)$id);
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
			
			$pid = $this->db->get_one('SELECT pid FROM portfolio WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			$this->url->redirect('/admin/portfolio/view_items/'.$pid.'/');
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
			$pid = $this->db->get_one('SELECT pid FROM portfolio WHERE id = '.(int)$id);
			if(empty($pid)) {
				$this->url->redirect('::referer');
			}
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $pid)) 
				$this->role_controller->AccessError();
			
			$this->db->delete('portfolio', (int)$id);
			
			if (file_exists($this->path.$id.'.txt')) unlink($this->path.$id.'.txt');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
			
			$this->trash_controller->delete_addition($id, $this->module_id, true);
		}		
	}
?>
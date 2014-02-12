<?php
	class visban_controller extends application_controller {
		
		private $path;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('banners', 'files');
		}
		
		public function index() {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(7)) 
				$this->role_controller->AccessError();
			
			if(!$this->banner->getCountCategories()) {
				// -- если список категорий пуст
				$banners['msg'] = '<p>Добавить баннер невозможно: не найдено ни одной точки вставки</p>';
				$banners['button_disable'] = 'disabled="disabled"';
			} else {
				$banners['list'] = $this->banner->getBanners();
				
				if(!empty($banners['list'])) {
					foreach($banners['list'] as $i => &$item) {
						$item['date_begin'] = join('.',array_reverse(explode('-',substr($item['date_begin'],0,10))));
						$item['date_end']   = join('.',array_reverse(explode('-',substr($item['date_end'],0,10))));
						if($item['date_begin'] == '00.00.0000' && $item['date_end'] == '00.00.0000') {
							$item['date'] = 'бесконечно';
						} elseif($item['date_begin'] == '00.00.0000') {
							$item['date'] = 'до '.$item['date_end'];
						} elseif($item['date_end'] == '00.00.0000') {
							$item['date'] = 'с '.$item['date_begin'];
						} else {
							$item['date'] = $item['date_begin'].' &ndash; '.$item['date_end'];
						}
					}
				} else {
					$banners['msg'] = '<p>Список баннеров пуст</p>';
				}
			}
			$this->html->render('banners/index.html', $banners, 'content_path');
		}

		//Добавление / сохранение баннера
		public function add($id = false) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(7)) 
				$this->role_controller->AccessError();
			
			$id = empty($_POST['banner_id']) ? (int)$id : (int)$_POST['banner_id'];
			
			if(!empty($_POST)) {
				$ext = '';
				if(!empty($_FILES['banner']['name'])) {
					$fileinfo = $this->file->getFileInfo($_FILES['banner']['name']);
					$ext      = $fileinfo['extension'];
				}
				$link = trim($_POST['link']);
				if(substr($link, 0, 7) != 'http://') {
					$link = 'http://'.$link;
				}
				$data = array(
					'title'       => trim($_POST['title']),
					'link'        => trim($_POST['link']),
					'category_id' => (int)$_POST['category_id'],
					'date_begin'  => (empty($_POST['date_begin'])) ? '0000-00-00 00:00:00': $_POST['date_begin'],
					'date_end'    => (empty($_POST['date_end']))   ? '0000-00-00 00:00:00': $_POST['date_end'],
					'active'      => 1,
				);
				if(!empty($ext)) $data['extension'] = $ext;
				
				if($id) {
					$this->db->update('banners', $data, $id);
				} else {
					$id = $this->db->insert('banners', $data);
				}
				if(!empty($id) && !empty($ext)) {
					$load_name = $id.'.'.$ext;
					$this->file->upload('banners', $_FILES['banner']['tmp_name'], $load_name);
					switch($data['extension']) {
						case 'jpg':
						case 'jpeg':
						case 'gif':
						case 'png':
							$info = $this->banner->getOneCategory((int)$_POST['category_id']);
							if(!empty($info)) {
								$this->image->analyze($this->path.$load_name);
								$this->image->toFile($this->path.$load_name, 80, $info['width'], $info['height']);
							}
							break;
					}
				}
				$this->session->set('alert', ALERT_CHANGE_DATA);
				$this->url->redirect('/admin/visban/');
			}
			
			$banner['caption'] = 'Добавление баннера';
			
			$banner['categories'] = $this->banner->getCategories();
			if(!empty($banner['categories'])) {
				foreach($banner['categories'] as $i => &$item) {
					$item['category_title'] = $item['category_title'].' ('.$item['w'].' x '.$item['h'].')';
				}
			}
			$banner['active'] = 'checked="checked"';
			$banner['link']   = 'http://';
			
			$this->html->render('banners/save.html', $banner, 'content_path');
		}
		
		//Редактирование баннера
		public function one($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(7)) 
				$this->role_controller->AccessError();
			
			$banner = $this->banner->getOneBanner($id);
			$banner['categories'] = $this->banner->getCategories_fix();
			foreach($banner['categories'] as $i => &$item) {
				$item['category_title'] = $item['category_title'].' ('.$item['w'].' x '.$item['h'].')';
				if($item['category_id_fix'] == $banner['category_id']) {
					$item['selected'] = 'selected="selected"';
				}
			}
			if(file_exists($this->path.$id.'.'.$banner['extension'])) {
				$banner['view'] = '<a class="view" href="/application/includes/visban/'.$id.'.'.$banner['extension'].'" target="_blank">смотреть</a>';
			}
			$banner['active_check'] = ($banner['active'] == 1)?'checked="checked"':'';
			if($banner['date_begin'] == '0000-00-00' && $banner['date_end'] == '0000-00-00') {
				$banner['forever_hide']    = 'disabled="disabled"';
				$banner['forever_checked'] = 'checked="checked"';
			}

			$banner['caption'] = $banner['title'];
			
			$this->html->render('banners/save.html', $banner, 'content_path');
		}

		//Ajax определение размеров баннера
		public function getBannersSize($type) {
			$size = $this->banner->getProps($type);
			$result = '{width:'.$size['width'].', height:'.$size['height'].'}';
			die($result);
		}
		
		public function delete($id = 0) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(7)) 
				$this->role_controller->AccessError();
			
			$ext = $this->banner->getExtension($id);
			$this->db->delete('banners', $id);
			$filepath = $this->path.$id.'.'.$ext;
			if(file_exists($filepath)) {
				unlink($filepath);
			}
			$this->url->redirect('/admin/visban/');
		}

	}
?>
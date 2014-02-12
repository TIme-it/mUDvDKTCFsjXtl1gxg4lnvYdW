<?php
	class firms_controller extends application_controller {
	
		private $path;
		private $exts;
		private $module_id = 5;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('firms', 'files');
			$this->exts = array('jpg','jpeg','gif','png');
		}

		public function index($id) {
			// -- получаем данные по разделу
			$info = $this->firms->getInfo($id);
			if(empty($info)) {
				$this->main_controller->page_404();
				return false;
			}
			
			// -- помечаем активный раздел
			$this->active_main_id = $id;
			
			// -- SEO
			$this->html->tpl_vars['meta_description'] = $info['description'];
			$this->html->tpl_vars['meta_keywords']    = $info['keywords'];
			
			$this->title .= ' | '.$info['title'];
			
			// -- текст страницы
			if(file_exists($this->path.$id.'_volume.txt')) {
				$data['text'] = file_get_contents($this->path.$id.'_volume.txt');
			} else $data['text'] = '';
			$is_long_text = (mb_strlen(strip_tags($data['text']), 'UTF-8') > 300);
				
			// -- добавляем Яндекс.Карты, если нужно
			$this->all_controller->buildYandexMaps($id, 0);
			
			// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
			$this->all_controller->buildModulesBlock($id, 0, $info['title'], $is_long_text);
			
			
			$data['title_page'] = $info['title_page'];

			$data['list'] = $this->firms->getList($id);
			if(!empty($data['list'])) {
				foreach($data['list'] as $i => &$item) {
					$item['img']   = '';
					foreach($this->exts as $j => $ext) {
						if(file_exists($this->path.$item['id'].'.'.$ext)) {
							$item['img'] = '<img src="/application/includes/firms/'.$item['id'].'.'.$ext.'" />';
							break;
						}
					}
					if(file_exists($this->path.$item['id'].'.txt') && filesize($this->path.$item['id'].'.txt') > 0) {
						$item['title'] = '<a href="/firms/show/'.$item['id'].'/">'.$item['title'].'</a>';
						$item['img']   = '<a href="/firms/show/'.$item['id'].'/">'.$item['img'].'</a>';
					}
				}
			}

			// -- дополнительные модули
			$data['galleryBlock'] = $this->all_controller->images($id, 0);
			$data['filesBlock']   = $this->all_controller->files($id,  0);
			
			// -- основной рендер
			$this->html->render('firms/index.html', $data, 'content');
		}
		
		public function show($id) {
			$data = $this->firms->getItem($id);
			if(empty($data)) {
				$this->main_controller->page_404();
				return false;
			}
			
			// -- помечаем активный раздел
			$this->active_main_id = $data['pid'];
			
			// -- текст
			$data['text'] = '';
			if(file_exists($this->path.$data['id'].'.txt')) {
				$data['text'] = file_get_contents($this->path.$data['id'].'.txt');
			}
			$is_long_text = (mb_strlen(strip_tags($data['text']), 'UTF-8') > 300);
			
			// -- добавляем Яндекс.Карты, если нужно
			$this->all_controller->buildYandexMaps($id, $this->module_id);
			// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
			$this->all_controller->buildModulesBlock($id, $this->module_id, $data['title'], $is_long_text);
			unset($data['print']);
			
			$data['gallery_block'] = $this->all_controller->images($id, $this->module_id);
			$data['files_block']   = $this->all_controller->files($id,  $this->module_id);
			
			// -- основной рендер
			$this->html->render('firms/show.html', $data, 'content');
		}

	}
?>
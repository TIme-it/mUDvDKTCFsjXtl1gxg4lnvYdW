<?php
	class search_controller extends application_controller {
		
		private $max_count = 4;
		
		public function __construct() {
			$this->max_count = $this->config->get('search_count','site');
		}
		
		public function index() {
			$this->url->redirect('::referer');
		}
		
		public function view($phrase = false) {
		
			$this->title .= ' | Поиск по сайту';
			$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
			
			$search_text   = (empty($phrase)) ? (empty($_POST['search_text']) ? false : $_POST['search_text']) : rawurldecode($phrase);
			
			if($search_text && is_string($search_text) && mb_strlen($search_text, 'UTF-8') > 3) {
				$this->html->tpl_vars['search_text'] = $search_text;
				$data = $this->search->getResults($search_text, $page, $this->max_count);
				if(!empty($data['list'])) {
					$count = $data['all_count'];
					$href  = 'http://'.$this->config->get('domain', 'site');
					$data['text'] = '<ul start="'.(($page-1)*$this->max_count+1).'">';
					foreach($data['list'] as $i => &$item) {
						switch ($item['module_id']) {
							case '2':
								if(file_exists($this->config->get('news', 'files').$item['pid'].'.jpg'))
									$item['img_url'] = '/application/includes/news/'.$item['pid'].'.jpg';
									$item['url'] = $this->news_controller->get_url($item['pid']);
								break;

							case '8':
								if(file_exists($this->config->get('catalog', 'files').$item['pid'].'.jpg'))
									$item['img_url'] = '/application/includes/catalog/catalog.jpg';
								break;

							case '12':
								if(file_exists($this->config->get('actions', 'files').$item['pid'].'.jpg'))
									$item['img_url'] = '/application/includes/actions/'.$item['pid'].'.jpg';
								$item['url'] = $this->actions_controller->get_url($item['pid']);
								break;

							case '200':
								if(file_exists(INCLUDES.'catalog/catalog_category/b/'.$item['pid'].'.png'))
									$item['img_url'] = '/application/includes/catalog/catalog_category/b/'.$item['pid'].'.png';
								$item['url'] = $this->application_controller->get_url($this->search->getMainIdProduct($item['pid']));
								break;

							case '300':
								if(file_exists(INCLUDES.'catalog/catalog_product/s/'.$item['pid'].'.png'))
									$item['img_url'] = '/application/includes/catalog/catalog_product/s/'.$item['pid'].'.png';
								$item['url'] = $this->application_controller->get_url($this->search->getMainIdProduct($item['pid']));
								break;
							
							default:
								break;
						}

						if (mb_strlen($item['note'], 'UTF-8') > 400) {
							$item['note'] = mb_substr($item['note'], 0, 297, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						if (!empty($item['img_url'])){
							$data['text'] .= '<li><img src="'.$item['img_url'].'" width="106" height="100" alt="" /><a href="'.$item['url'].'" styl="margin-left: 125px;"><span>'.$item['title'].'</span></a>';
							if(!empty($item['note'])) {
								$data['text'] .= '<p style="margin-left: 125px;">'.$item['note'].'</p>';
							}
						}
						else {
							$data['text'] .= '<li><a href="'.$item['url'].'" styl="margin-left: 125px;"><span>'.$item['title'].'</span></a>';
							if(!empty($item['note'])) {
								$data['text'] .= '<p>'.$item['note'].'</p>';
							}
						}
						// $data['text'] .= '<span class="search_link"><a href="'.$item['url'].'">'.$href.$item['url'].'</a></span><div class="border"></div></li>';
					}
					$data['text'] .= '</ul>';
					// -- подсветка
					$this->search->getLightRun($data['text']);
					
					// $data['pretext'] = '<p>Нашлось '.$this->printPages($count).'</p>';
					$data['pagination'] = $this->pagination_controller->index_ajax($count, $this->max_count, $page, 'search_ajax', ', \''.$search_text.'\'', '/popup/search/view/'.rawurlencode($search_text).'/');
				} 
				// else {
				// 	$data['text'] = '<p>По запросу &laquo;'.$search_text.'&raquo; ничего не найдено.</p>';
				// }
			} else {
				$data['text'] = '<p>Необходимо ввести слово или фразу состоящее из более чем 3 символов.</p>';
			}

			/*хлебные крошки*/
			// $this->GetBreadCrums($data['pid'],array('url' =>"" ,'title' =>$data['title'],'last_link' =>true ));
			
			$data['title'] = 'Результаты поиска';
			$data['search_text'] = $search_text;
			$data['search_res'] = $this->html->render('search/search_res.html', $data);
			$this->layout = 'pages';
			
			$this->html->render('search/index.html', $data, 'content');
		}
		
		//постраничка
		public function search_ajax($page) {
			$search_text = empty($_POST['text']) ? '' : $_POST['text'];
			
			if($search_text && is_string($search_text) && mb_strlen($search_text, 'UTF-8') > 3) {
				$data = $this->search->getResults($search_text, $page, $this->max_count);
				if(!empty($data['list'])) {
					$count = $data['all_count'];
					$href  = 'http://'.$this->config->get('domain', 'site');
					$data['text'] = '<ul start="'.(($page-1)*$this->max_count+1).'">';
					foreach($data['list'] as $i => &$item) {
						switch ($item['module_id']) {
							case '2':
								if(file_exists($this->config->get('news', 'files').$item['pid'].'.jpg'))
									$item['img_url'] = '/application/includes/news/'.$item['pid'].'.jpg';
									$item['url'] = $this->news_controller->get_url($item['pid']);
								break;

							case '8':
								if(file_exists($this->config->get('catalog', 'files').$item['pid'].'.jpg'))
									$item['img_url'] = '/application/includes/catalog/catalog.jpg';
								break;

							case '12':
								if(file_exists($this->config->get('actions', 'files').$item['pid'].'.jpg'))
									$item['img_url'] = '/application/includes/actions/'.$item['pid'].'.jpg';
									$item['url'] = $this->actions_controller->get_url($item['pid']);
								break;

							case '200':
								if(file_exists(INCLUDES.'catalog/catalog_category/b/'.$item['pid'].'.png'))
									$item['img_url'] = '/application/includes/catalog/catalog_category/b/'.$item['pid'].'.png';
								$item['url'] = $this->application_controller->get_url($this->search->getMainIdProduct($item['pid']));
								break;

							case '300':
								if(file_exists(INCLUDES.'catalog/catalog_product/s/'.$item['pid'].'.png'))
									$item['img_url'] = '/application/includes/catalog/catalog_product/s/'.$item['pid'].'.png';
								$item['url'] = $this->application_controller->get_url($this->search->getMainIdProduct($item['pid']));
								break;
							
							default:
								break;
						}

						if (mb_strlen($item['note'], 'UTF-8') > 400) {
							$item['note'] = mb_substr($item['note'], 0, 297, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}

						if (!empty($item['img_url'])){
							$data['text'] .= '<li><img src="'.$item['img_url'].'" width="106" height="100" alt="" /><a href="'.$item['url'].'" styl="margin-left: 125px;"><span>'.$item['title'].'</span></a>';
							if(!empty($item['note'])) {
								$data['text'] .= '<p style="margin-left: 125px;">'.$item['note'].'</p>';
							}
						}
						else {
							$data['text'] .= '<li><a href="'.$item['url'].'" styl="margin-left: 125px;"><span>'.$item['title'].'</span></a>';
							if(!empty($item['note'])) {
								$data['text'] .= '<p>'.$item['note'].'</p>';
							}
						}
						// $data['text'] .= '<span class="search_link"><a href="'.$item['url'].'">'.$href.$item['url'].'</a></span><div class="border"></div></li>';
					}
					$data['text'] .= '</ul>';
					// -- подсветка
					$this->search->getLightRun($data['text']);
					
					// $data['pretext'] = '<p>Нашлось '.$this->printPages($count).'</p>';
					$data['pagination'] = $this->pagination_controller->index_ajax($count, $this->max_count, $page, 'search_ajax', ', \''.$search_text.'\'', '/popup/search/view/'.rawurlencode($search_text).'/');
				} 
				// else {
				// 	$data['text'] = '<p>По запросу &laquo;'.$search_text.'&raquo; ничего не найдено.</p>';
				// }
			}
			
			die(json_encode($this->html->render('search/search_res.html', $data)));
		}
		
		private function printPages($count) {
			if($count % 10 == 1) return $count.' ответ.';
			if($count % 10 > 1 && $count % 10 < 5) return $count.' ответа.';
			return $count.' ответов.';
		}
		
	}
?>
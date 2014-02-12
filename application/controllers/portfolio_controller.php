<?php
	class portfolio_controller extends application_controller {

		private $portfolio_path;
		private $module_id = 11;
		
		public function __construct() {
			parent::__construct();
			$this->portfolio_path = $this->config->get('portfolio', 'files');
		}
		
		// $year, $month - нигде не используются, служат как декорации к URL
		public function index($pid = false, $year = false, $month = false, $id = false) {		
			if(($this->config->get('active','chpu') == 1) && (!empty($pid)) && (!is_numeric($pid))){
				$id = $this->portfolio->getPagePidAlias($pid);
				$pid = $this->portfolio->getPaged($id);
			}	
			// var_dump($id);
			// var_dump($pid);
			// die();

			// -- получаем данные по текущему разделу
			$info = $this->portfolio->getPageInfo($pid, $this->module_id);

			if(empty($info)) {
				$this->layout = 'pages';
				$this->main_controller->page_404();
				return false;
			}

			// // вызов ЧПУ
			// $this->get_url($id);
			
			// -- помечаем активный раздел
			$this->active_main_id = $pid;
			
			if(empty($id)) { // -- если не указана конкретная новость, то выводим весь список	
				// -- SEO
				$this->html->tpl_vars['meta_description'] = $info['description'];
				$this->html->tpl_vars['meta_keywords']    = $info['keywords'];
				
				$this->title .= ' | '.$info['title'];
				
				$portfolio['pageTitle'] = $info['title_page'];
				
				// -- шаблон по-умолчанию
				if(empty($info['template'])) {
					$info['template'] = 'layoutPortfolio';
				}
				
				// -- проверка на наличие text для всего раздела
				$portfolio['text'] = '';
				if(file_exists($this->portfolio_path.$pid.'_volume.txt')) {
					$portfolio['text'] = file_get_contents($this->portfolio_path.$pid.'_volume.txt');
				}
				$is_long_text = (mb_strlen(strip_tags($portfolio['text']), 'UTF-8') > 300);
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($pid, 0, $this->title);
				$portfolio['text'] = '<div class="list_note">'.$portfolio['text'].'</div>';
				
				// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($pid, 0, $info['title'], $is_long_text);
				
				$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
				$portfolio_count = $this->config->get('portfolio_count', 'site');
				$portfolio_all_count = $this->portfolio->getPortfolioCount($pid);

				$portfolio['pagination'] = $this->pagination_controller->index_ajax($portfolio_all_count, $portfolio_count, $page, 'portfolio_ajax', ','.$pid);
				
				$portfolio['list'] = $this->portfolio->getPortfolio($pid, $page-1, $portfolio_count);
				
				if(!empty($portfolio['list'])) {
					$img_w = 140;
					// $img_w = $this->config->get('img_width','portfolio');
					$img_h = 80;
					// $img_h = $this->config->get('img_height','portfolio');
					
					foreach($portfolio['list'] as $i => &$item) {
						// -- форматирование даты
						$item['date'] = $this->date->format2($item['date']);
						$item['url'] = $this->get_url($item['id']);
						// -- прикрепляем картинку
						
						// -- черезполосица
						if($info['template'] == 'layoutPortfolio') {
							$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
						} else {
							$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
						}
						
						//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
						if(file_exists($this->portfolio_path.$item['id'].'.jpg')) {
							$item['img'] = '<a href="'.$item['url'].'">
												<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/portfolio/'.$item['id'].'.jpg" />
											</a>';
						} else $item['li_class'] .= ' noimage';
						
						if (mb_strlen($item['note'], 'UTF-8') > 100) {
							$item['note'] = mb_substr($item['note'], 0, 97, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						if (mb_strlen($item['title'], 'UTF-8') > 55) {
							$item['list_title'] = mb_substr($item['title'], 0, 52, 'UTF-8');
							$item['list_title'] = mb_substr($item['list_title'], 0, mb_strrpos($item['list_title'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						else {
							$item['list_title'] = $item['title'];
						}
						// var_dump($item['list_title']);

						
						// -- первый элемент
						if($i == 0) {
							$item['li_class'] .= ' first';
						}
					}
					// die();
					
					// if (empty($info['text'])) {
						// $portfolio['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
					// }
					
					$portfolio['list'][ count($portfolio['list'])-1 ]['li_class'] .= ' last';
					$portfolio['list'][ count($portfolio['list'])-1 ]['last'] = 1;
				}
				$portfolio['our_partners'] = $this->partners->getAnotherPartners(0,4);
				if(!empty($portfolio['our_partners'])){
					foreach ($portfolio['our_partners'] as $j => &$value) {
						$value['url'] = $this->partners_controller->get_url($value['id']);
					}
				}

				$portfolio['portfolio_list'] = $this->html->render('portfolio/portfolio_list.html', $portfolio);
				
				// -- дополнительные модули
				$portfolio['galleryBlock'] = $this->all_controller->images($pid, 0);
				$portfolio['filesBlock']   = $this->all_controller->files($pid,  0);
			
				$portfolio['pid'] = $pid;
				/*хлебные крошки*/
				$this->GetBreadCrums($portfolio['pid'],array('url' =>"" ,'title' =>$portfolio['title'],'last_link' =>true ));
				$this->layout = 'portfolio';
				$this->html->render('portfolio/'.$info['template'].'.html', $portfolio, 'container');
			} else { // -- работаем с конкретной новостью по id
				$portfolio = $this->portfolio->getOneportfolio($id);
				if(empty($portfolio)) {
					$this->main_controller->page_404();
					return false;
				}
				
				$info_title   = $this->db->get_one('SELECT title FROM main WHERE id = '.(int)$portfolio['pid']);
				$this->title .= ' | '.$info_title.' | '.$portfolio['title'];
				
				// -- собираем данные в одну кучку
				$is_long_text = true;
				if(file_exists($this->portfolio_path.$id.'.txt')) {
					$portfolio['text'] = file_get_contents($this->portfolio_path.$id.'.txt');
					$is_long_text = (mb_strlen(strip_tags($portfolio['text']), 'UTF-8') > 300);
				}
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($id, $this->module_id, $this->title);
				// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($id, $this->module_id, $portfolio['title'], $is_long_text);
				
				if ($portfolio['is_show_date']) {
					$ts = (!is_numeric($portfolio['date'])) ? strtotime($portfolio['date']) : $portfolio['date'];
					$portfolio['date'] = $this->date->format3($ts);
				} else unset($portfolio['date']);
				
				$portfolio['galleryBlock'] = $this->all_controller->images($portfolio['id'], $this->module_id);
				$portfolio['filesBlock']   = $this->all_controller->files($portfolio['id'],  $this->module_id);
				
				//Строка новостей
				// $this->html->tpl_vars['portfolio_line'] = $this->make_portfolio_line();
				$portfolio['another_projects_list'] = $this->portfolio->getAnotherProjects($portfolio['id'],4);
				if(!empty($portfolio['another_projects_list'])){
					foreach ($portfolio['another_projects_list'] as $i => &$item) {
						$item['url'] = $this->get_url($item['id']);
					}
				}
				/*хлебные крошки*/
				$this->GetBreadCrums($portfolio['pid'],array('url' =>"" ,'title' =>$portfolio['title'],'last_link' =>true ));
				$this->layout='pages';
				$this->html->render('portfolio/item.html', $portfolio, 'container');
			}
		}
						
		public function portfolio_ajax($pid, $page) {
			$info = $this->portfolio->getPageInfo($pid);
			
			$portfolio_count = $this->config->get('portfolio_count', 'site');
			$portfolio_all_count = $this->portfolio->getPortfolioCount($pid);

			$portfolio['pagination'] = $this->pagination_controller->index_ajax($portfolio_all_count, $portfolio_count, $page, 'portfolio_ajax', ','.$pid);
			
			$portfolio['list'] = $this->portfolio->getPortfolio($pid, $page-1, $portfolio_count);

			if(!empty($portfolio['list'])) {
				$img_w = $this->config->get('img_width','portfolio');
				$img_h = $this->config->get('img_height','portfolio');
				
				foreach($portfolio['list'] as $i => &$item) {
					// -- форматирование даты
					$item['date'] = $this->date->format2($item['date']);
					
					// -- черезполосица
					if($info['template'] == 'layoutPortfolio') {
						$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
					} else {
						$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
					}
					
					// -- прикрепляем картинку
					//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
					if(file_exists($this->portfolio_path.$item['id'].'.jpg')) {
						$item['img'] = '<a href="/portfolio/'.$item['pid'].'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'">
											<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/portfolio/'.$item['id'].'.jpg"/>
										</a>';
					} else $item['li_class'] .= ' noimage';
					
					if (mb_strlen($item['note'], 'UTF-8') > 100)
							$item['note'] = mb_substr($item['note'], 0, 97, 'UTF-8').'...';
					
					// -- первый элемент
					if($i == 0) {
						$item['li_class'] .= ' first';
					}
				}
				
				// if (empty($info['text'])) {
					// $portfolio['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
				// }
				
				$portfolio['list'][ count($portfolio['list'])-1 ]['li_class'] .= ' last';
				$portfolio['list'][ count($portfolio['list'])-1 ]['last'] = 1;
			}
			$res=array();
			$res['list'] = $this->html->render('portfolio/portfolio_list.html', $portfolio);
			die(json_encode($res));
		}
		
		
		// -- RSS
		public function rss($pid) {
			function getXMLValidText($str) {
				$str = str_replace('«', '&laquo;', $str);
				$str = str_replace('»', '&raquo;', $str);
				$str = str_replace('&', '&amp;',   $str);
				$str = str_replace('&amp;laquo;', '«', $str);
				$str = str_replace('&amp;raquo;', '»', $str);
				return $str;
			}
			
			$info = $this->portfolio->getPageInfo($pid);
			$portfolio = $this->portfolio->getAllportfolio($pid);
			if(empty($info) || empty($portfolio)) {
				$this->main_controller->page_404();
				return false;
			}
			
			$domain = $this->config->get('domain', 'site');
			$org    = $this->config->get('org',    'site');
			
			header('Content-Type: text/xml; charset=utf-8');
			echo '<?xml version="1.0" encoding="utf-8"?>'."\n";
			echo '<rss version="2.0">'."\n";
			echo '	<channel>'."\n";
			echo '		<title>'.$org.' : '.$info['title'].'</title>'."\n";
			echo '		<link>http://'.$domain.$info['url'].'</link>'."\n";
			echo '		<description>'.getXMLValidText(strip_tags($info['note'])).'</description>'."\n";
			echo '		<lastBuildDate>'.date("D, d M Y H:i:s O").'</lastBuildDate>'."\n";
			echo '		<image></image>'."\n";
			
			foreach($portfolio as $i => &$item) {
				$ut = strtotime($item['date']);
				echo '		<item>'."\n";
				echo '			<title>'.getXMLValidText($item['title']).'</title>'."\n";
				echo '			<link>http://'.$domain.'/portfolio/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</link>'."\n";
				echo '			<description>'.getXMLValidText(strip_tags($item['note'])).'</description>'."\n";
				echo '			<pubDate>'.date("D, d M Y H:i:s O", $ut).'</pubDate>'."\n";
				echo '			<pubDateUT>'.$ut.'</pubDateUT>'."\n";
				echo '			<guid>http://'.$domain.'/portfolio/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</guid>'."\n";
				echo '		</item>'."\n";
			}
			
			echo '	</channel>'."\n";
			echo '</rss>'."\n";
			die();
		}

		// ЧПУ
		public function get_url($id){

			if($this->config->get('active','chpu') == 1){
				if(!empty($id)){
					$data = $this->portfolio->getUrl($id);
					$url = $this->application_controller->get_url($data['pid']);
					$url = $url.$data['alias'];
					return $url;
				}
				else{
					return false;
				}
			}
			else {
				if(!empty($id)){
					
					$data = $this->portfolio->getOneMY($id);
					$url = '/portfolio/'.$data['pid'].'/'.$data['year'].'/'.$data['month'].'/'.$data['id'];
					return $url;
				}
				else {
					return false;
				}
			}
		}

	}
?>
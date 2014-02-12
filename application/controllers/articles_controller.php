<?php
	class articles_controller extends application_controller {

		private $articles_path;
		private $module_id = 10;
		
		public function __construct() {
			parent::__construct();
			$this->articles_path = $this->config->get('articles', 'files');
		}
		
		// $year, $month - нигде не используются, служат как декорации к URL
		public function index($pid = false, $year = false, $month = false, $id = false) {	
			if(($this->config->get('active','chpu') == 1) && (!empty($pid)) && (!is_numeric($pid))){
				$id = $this->articles->getPagePidAlias($pid);
				$pid = $this->articles->getPaged($id);
			}	
			
			// -- получаем данные по текущему разделу
			$info = $this->articles->getPageInfo($pid, $this->module_id);

			if(empty($info)) {
				$this->layout = 'pages';
				$this->main_controller->page_404();
				return false;
			}

			// вызов ЧПУ
			$this->get_url($id);
			
			// -- помечаем активный раздел
			$this->active_main_id = $pid;
			
			if(empty($id)) { // -- если не указана конкретная новость, то выводим весь список	
				// -- SEO
				$this->html->tpl_vars['meta_description'] = $info['description'];
				$this->html->tpl_vars['meta_keywords']    = $info['keywords'];
				
				$this->title .= ' | '.$info['title'];
				
				$articles['pageTitle'] = $info['title_page'];
				
				// -- шаблон по-умолчанию
				if(empty($info['template'])) {
					$info['template'] = 'layoutarticles';
				}
				// -- проверка на наличие text для всего раздела
				$articles['text'] = '';
				if(file_exists($this->articles_path.$pid.'_volume.txt')) {
					$articles['text'] = file_get_contents($this->articles_path.$pid.'_volume.txt');
				}
				$is_long_text = (mb_strlen(strip_tags($articles['text']), 'UTF-8') > 300);
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($pid, 0, $this->title);
				$articles['text'] = '<div class="list_note">'.$articles['text'].'</div>';
				
				// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($pid, 0, $info['title'], $is_long_text);
				
				$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
				$articles_count = $this->config->get('articles_count', 'site');
				$articles_all_count = $this->articles->getArticlesCount($pid);
				$articles['pagination'] = $this->pagination_controller->index_ajax($articles_all_count, $articles_count, $page, 'articles_ajax', ','.$pid);
				
				$articles['list'] = $this->articles->getArticles($pid, $page-1, $articles_count);
				if(!empty($articles['list'])) {
					$img_w = $this->config->get('img_width','articles');
					$img_h = $this->config->get('img_height','articles');
					
					foreach($articles['list'] as $i => &$item) {
						// -- форматирование даты
						$item['date'] = $this->date->format2($item['date']);
						$item['url'] = $this->get_url($item['id']);
						// -- прикрепляем картинку
						
						// -- черезполосица
						if($info['template'] == 'layoutArticles') {
							$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
						} else {
							$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
						}
						
						//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
						if(file_exists($this->articles_path.$item['id'].'.jpg')) {
							$item['img'] = '<a href="'.$item['url'].'">
												<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/articles/'.$item['id'].'.jpg" />
											</a>';
						} else $item['li_class'] .= ' noimage';
						
						if (mb_strlen($item['note'], 'UTF-8') > 200) {
							$item['note'] = mb_substr($item['note'], 0, 170, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						
						// -- первый элемент
						if($i == 0) {
							$item['li_class'] .= ' first';
						}
					}
					
					// if (empty($info['text'])) {
						// $articles['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
					// }
					
					$articles['list'][ count($articles['list'])-1 ]['li_class'] .= ' last';
					$articles['list'][ count($articles['list'])-1 ]['last'] = 1;
				}
				$articles['articles_list'] = $this->html->render('articles/articles_list.html', $articles);
				
				// -- дополнительные модули
				$articles['galleryBlock'] = $this->all_controller->images($pid, 0);
				$articles['filesBlock']   = $this->all_controller->files($pid,  0);
				
				$articles['pid'] = $pid;
				/*хлебные крошки*/
				$this->GetBreadCrums($articles['pid'],array('url' =>"" ,'title' =>$articles['title'],'last_link' =>true ));
				$this->layout = 'pages';
				$this->html->render('articles/'.$info['template'].'.html', $articles, 'container');
			} else { // -- работаем с конкретной новостью по id
				$articles = $this->articles->getOnearticles($id);
				if(empty($articles)) {
					$this->main_controller->page_404();
					return false;
				}
				
				$info_title   = $this->db->get_one('SELECT title FROM main WHERE id = '.(int)$articles['pid']);
				$this->title .= ' | '.$info_title.' | '.$articles['title'];
				
				// -- собираем данные в одну кучку
				$is_long_text = true;
				if(file_exists($this->articles_path.$id.'.txt')) {
					$articles['text'] = file_get_contents($this->articles_path.$id.'.txt');
					$is_long_text = (mb_strlen(strip_tags($articles['text']), 'UTF-8') > 300);
				}
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($id, $this->module_id, $this->title);
				// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($id, $this->module_id, $articles['title'], $is_long_text);
				
				if ($articles['is_show_date']) {
					$ts = (!is_numeric($articles['date'])) ? strtotime($articles['date']) : $articles['date'];
					$articles['date'] = $this->date->format3($ts);
				} else unset($articles['date']);
				
				$articles['galleryBlock'] = $this->all_controller->images($articles['id'], $this->module_id);
				$articles['filesBlock']   = $this->all_controller->files($articles['id'],  $this->module_id);
				
				//Строка новостей
				// $this->html->tpl_vars['articles_line'] = $this->make_articles_line();

				/*хлебные крошки*/
				$this->GetBreadCrums($articles['pid'],array('url' =>"" ,'title' =>$articles['title'],'last_link' =>true ));
				
				$this->layout='pages';
				$this->html->render('articles/item.html', $articles, 'container');
			}
		}
						
		public function articles_ajax($pid, $page) {
			$info = $this->articles->getPageInfo($pid);
			
			$articles_count = $this->config->get('articles_count', 'site');
			$articles_all_count = $this->articles->getarticlesCount($pid);
			$articles['pagination'] = $this->pagination_controller->index_ajax($articles_all_count, $articles_count, $page, 'articles_ajax', ','.$pid);
			
			$articles['list'] = $this->articles->getarticles($pid, $page-1, $articles_count);
			if(!empty($articles['list'])) {
				$img_w = $this->config->get('img_width','articles');
				$img_h = $this->config->get('img_height','articles');
				
				foreach($articles['list'] as $i => &$item) {
					// -- форматирование даты
					$item['date'] = $this->date->format2($item['date']);
					
					// -- черезполосица
					if($info['template'] == 'layoutArticles') {
						$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
					} else {
						$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
					}
					
					// -- прикрепляем картинку
					//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
					if(file_exists($this->articles_path.$item['id'].'.jpg')) {
						$item['img'] = '<a href="/articles/'.$item['pid'].'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'">
											<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/articles/'.$item['id'].'.jpg"/>
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
					// $articles['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
				// }
				
				$articles['list'][ count($articles['list'])-1 ]['li_class'] .= ' last';
				$articles['list'][ count($articles['list'])-1 ]['last'] = 1;
			}
			$res=array();
			$res['list'] = $this->html->render('articles/articles_list.html', $articles);
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
			
			$info = $this->articles->getPageInfo($pid);
			$articles = $this->articles->getAllArticles($pid);
			if(empty($info) || empty($articles)) {
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
			
			foreach($articles as $i => &$item) {
				$ut = strtotime($item['date']);
				echo '		<item>'."\n";
				echo '			<title>'.getXMLValidText($item['title']).'</title>'."\n";
				echo '			<link>http://'.$domain.'/articles/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</link>'."\n";
				echo '			<description>'.getXMLValidText(strip_tags($item['note'])).'</description>'."\n";
				echo '			<pubDate>'.date("D, d M Y H:i:s O", $ut).'</pubDate>'."\n";
				echo '			<pubDateUT>'.$ut.'</pubDateUT>'."\n";
				echo '			<guid>http://'.$domain.'/articles/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</guid>'."\n";
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
					$data = $this->articles->getUrl($id);
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
					$data = $this->articles->getOneMY($id);
					$url = '/articles/'.$data['pid'].'/'.$data['year'].'/'.$data['month'].'/'.$data['id'];
					return $url;
				}
				else {
					return false;
				}
			}
		}

	}
?>
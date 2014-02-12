<?php
	class news_controller extends application_controller {

		private $news_path;
		private $module_id = 2;
		
		public function __construct() {
			parent::__construct();
			$this->news_path = $this->config->get('news', 'files');
		}
		
		// $year, $month - нигде не используются, служат как декорации к URL
		public function index($pid, $year = false, $month = false, $id = false) {		
			if(($this->config->get('active','chpu') == 1) && (!empty($pid)) && (!is_numeric($pid))){
				$id = $this->news->getPagePidAlias($pid);
				$pid = $this->news->getPaged($id);
			}	

			// -- получаем данные по текущему разделу
			$info = $this->news->getPageInfo($pid, $this->module_id);
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
				
				$news['pageTitle'] = $info['title_page'];
				
				// -- шаблон по-умолчанию
				if(empty($info['template'])) {
					$info['template'] = 'layoutNews';
				}
				
				// -- проверка на наличие text для всего раздела
				$news['text'] = '';
				if(file_exists($this->news_path.$pid.'_volume.txt')) {
					$news['text'] = file_get_contents($this->news_path.$pid.'_volume.txt');
				}
				$is_long_text = (mb_strlen(strip_tags($news['text']), 'UTF-8') > 300);
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($pid, 0, $this->title);
				$news['text'] = '<div class="list_note">'.$news['text'].'</div>';
				
				// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($pid, 0, $info['title'], $is_long_text);
				
				$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
				$news_count = $this->config->get('news_count', 'site');
				$news_all_count = $this->news->getNewsCount($pid);
				$news['pagination'] = $this->pagination_controller->index_ajax($news_all_count, $news_count, $page, 'news_ajax', ','.$pid);
				
				$news['list'] = $this->news->getNews($pid, $page-1, $news_count);
				if(!empty($news['list'])) {
					$img_w = $this->config->get('img_width','news');
					$img_h = $this->config->get('img_height','news');
					
					foreach($news['list'] as $i => &$item) {
						// -- форматирование даты
						$item['date'] = $this->date->format2($item['date']);
						$item['url'] = $this->get_url($item['id']);
						// -- прикрепляем картинку
						
						// -- черезполосица
						if($info['template'] == 'layoutNews') {
							$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
						} else {
							$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
						}
						
						//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
						if(file_exists($this->news_path.$item['id'].'.jpg')) {
							$item['img'] = '<a href="/news/'.$item['pid'].'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'">
												<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/news/'.$item['id'].'.jpg" />
											</a>';
						} else $item['li_class'] .= ' noimage';
						
						if (mb_strlen($item['note'], 'UTF-8') > 500) {
							$item['note'] = mb_substr($item['note'], 0, 497, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						
						// -- первый элемент
						if($i == 0) {
							$item['li_class'] .= ' first';
						}
					}
					
					// if (empty($info['text'])) {
						// $news['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
					// }
					
					$news['list'][ count($news['list'])-1 ]['li_class'] .= ' last';
					$news['list'][ count($news['list'])-1 ]['last'] = 1;
				}
				$news['news_list'] = $this->html->render('news/news_list.html', $news);
				
				// -- дополнительные модули
				$news['galleryBlock'] = $this->all_controller->images($pid, 0);
				$news['filesBlock']   = $this->all_controller->files($pid,  0);
				
				$news['pid'] = $pid;
				$this->layout = 'pages';
				$this->html->render('news/'.$info['template'].'.html', $news, 'content');
			} else { // -- работаем с конкретной новостью по id
				$news = $this->news->getOneNews($id);
				if(empty($news)) {
					$this->main_controller->page_404();
					return false;
				}
				
				$info_title   = $this->db->get_one('SELECT title FROM main WHERE id = '.(int)$news['pid']);
				$this->title .= ' | '.$info_title.' | '.$news['title'];
				
				// -- собираем данные в одну кучку
				$is_long_text = true;
				if(file_exists($this->news_path.$id.'.txt')) {
					$news['text'] = file_get_contents($this->news_path.$id.'.txt');
					$is_long_text = (mb_strlen(strip_tags($news['text']), 'UTF-8') > 300);
				}
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($id, $this->module_id, $this->title);
				// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($id, $this->module_id, $news['title'], $is_long_text);
				
				if ($news['is_show_date']) {
					$ts = (!is_numeric($news['date'])) ? strtotime($news['date']) : $news['date'];
					$news['date'] = $this->date->format3($ts);
				} else unset($news['date']);
				
				$news['galleryBlock'] = $this->all_controller->images($news['id'], $this->module_id);
				$news['filesBlock']   = $this->all_controller->files($news['id'],  $this->module_id);
				
				//Строка новостей
				$this->html->tpl_vars['news_line'] = $this->make_news_line();
				$this->layout = 'pages';
				$this->html->render('news/item.html', $news, 'content');
			}
		}


		public function news_ajax() {
			$pid = $_POST['pid'];
			$page = $_POST['page'];
			// $info = $this->news->getPageInfo($pid);
			
			$news_count = $this->config->get('news_count', 'site');
			// var_dump($news_count);
			// die();
			$news_all_count = $this->news->getNewsCount($pid);
			$news['pagination'] = $this->pagination_controller->index_ajax($news_all_count, $news_count, $page, 'news_ajax', ','.$pid);
			$news['list'] = $this->news->getNews($pid, $page-1, $news_count);
			if(!empty($news['list'])){
				foreach ($news['list'] as $i => &$item) {
					$item['date'] = $this->date->format2($item['date']);
					$item['url'] = $this->news_controller->get_url($item['id']);

					if(file_exists(INCLUDES.'news/'.$item['id'].'.txt')){
						$item['text'] = htmlspecialchars_decode(file_get_contents(INCLUDES.'news/'.$item['id'].'.txt'));
						if ((mb_strlen($item['text'], 'UTF-8') > 700) && (!empty($item['text']))) {
							$item['text'] = mb_substr($item['text'], 0, 697, 'UTF-8');
							$item['text'] = mb_substr($item['text'], 0, mb_strrpos($item['text'],' ', 'UTF-8'), 'UTF-8').'...';
						}
					}

					if (mb_strlen($item['title'], 'UTF-8') > 45) {
						$item['list_title'] = mb_substr($item['title'], 0, 43, 'UTF-8');
						$item['list_title'] = mb_substr($item['list_title'], 0, mb_strrpos($item['list_title'],' ', 'UTF-8'), 'UTF-8').'...';
					}
					else {
						$item['list_title'] = $item['title'];
					}
					if (mb_strlen($item['note'], 'UTF-8') > 500) {
						$item['note'] = mb_substr($item['note'], 0, 497, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
					}

				}
			}
			
			echo json_encode($this->html->render('news/news_list.html', $news));
			die();
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
			
			$info = $this->news->getPageInfo($pid);
			$news = $this->news->getAllNews($pid);
			if(empty($info) || empty($news)) {
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
			
			foreach($news as $i => &$item) {
				$ut = strtotime($item['date']);
				echo '		<item>'."\n";
				echo '			<title>'.getXMLValidText($item['title']).'</title>'."\n";
				echo '			<link>http://'.$domain.'/news/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</link>'."\n";
				echo '			<description>'.getXMLValidText(strip_tags($item['note'])).'</description>'."\n";
				echo '			<pubDate>'.date("D, d M Y H:i:s O", $ut).'</pubDate>'."\n";
				echo '			<pubDateUT>'.$ut.'</pubDateUT>'."\n";
				echo '			<guid>http://'.$domain.'/news/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</guid>'."\n";
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
					$data = $this->news->getUrl($id);
					$url = $this->news->get_url($data['pid']);
					$url = $url.$data['alias'];
					return $url;
				}
				else{
					return false;
				}
			}
			else {
				if(!empty($id)){
					$data = $this->news->getOneMY($id);
					$url = '/news/'.$data['pid'].'/'.$data['year'].'/'.$data['month'].'/'.$data['id'];
					return $url;
				}
				else {
					return false;
				}
			}
		}

	}
?>
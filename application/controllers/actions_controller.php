<?php
	class actions_controller extends application_controller {

		private $actions_path;
		private $module_id = 12;
		
		public function __construct() {
			parent::__construct();
			$this->actions_path = $this->config->get('actions', 'files');
		}
		
		// $year, $month - нигде не используются, служат как декорации к URL
		public function index($pid = false, $year = false, $month = false, $id = false) {			
			if(($this->config->get('active','chpu') == 1) && (!empty($pid)) && (!is_numeric($pid))){
				$id = $this->actions->getPagePidAlias($pid);
				$pid = $this->actions->getPaged($id);
			}	
			
			// -- получаем данные по текущему разделу
			$info = $this->actions->getPageInfo($pid, $this->module_id);

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
				
				$actions['pageTitle'] = $info['title_page'];
				
				// -- шаблон по-умолчанию
				if(empty($info['template'])) {
					$info['template'] = 'layoutactions';
				}
				
				// -- проверка на наличие text для всего раздела
				$actions['text'] = '';
				if(file_exists($this->actions_path.$pid.'_volume.txt')) {
					$actions['text'] = file_get_contents($this->actions_path.$pid.'_volume.txt');
				}
				$is_long_text = (mb_strlen(strip_tags($actions['text']), 'UTF-8') > 300);
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($pid, 0, $this->title);
				$actions['text'] = '<div class="list_note">'.$actions['text'].'</div>';
				
				// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($pid, 0, $info['title'], $is_long_text);
				
				$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
				$actions_count = $this->config->get('actions_count', 'site');

				$actions_all_count = $this->actions->getActionsCount($pid);
				$actions['pagination'] = $this->pagination_controller->index_ajax($actions_all_count, $actions_count, $page, 'actions_ajax', ','.$pid);
				
				$actions['list'] = $this->actions->getActions($pid, $page-1, $actions_count);
				$actions['pid'] = $pid;
				if(!empty($actions['list'])) {
					$img_w = $this->config->get('img_width','actions');
					$img_h = $this->config->get('img_height','actions');
					
					foreach($actions['list'] as $i => &$item) {
						// -- форматирование даты
						$item['date'] = $this->date->format2($item['date']);
						$item['url'] = $this->get_url($item['id']);
						// -- прикрепляем картинку
						
						// -- черезполосица
						if($info['template'] == 'layoutactions') {
							$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
						} else {
							$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
						}
						
						//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
						if(file_exists($this->actions_path.$item['id'].'.jpg')) {
							$item['img'] = '<a href="'.$item['url'].'">
												<img width="140" height="80" src="/application/includes/actions/'.$item['id'].'.jpg" />
											</a>';
						} else $item['li_class'] .= ' noimage';
						
						if (mb_strlen($item['note'], 'UTF-8') > 500) {
							$item['note'] = mb_substr($item['note'], 0, 497, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						$item['proceed'] = ($item['proceed'] == 1) ? true : false;
						// -- первый элемент
						if($i == 0) {
							$item['li_class'] .= ' first';
						}
					}
					// if (empty($info['text'])) {
						// $actions['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
					// }
					
					$actions['list'][ count($actions['list'])-1 ]['li_class'] .= ' last';
					$actions['list'][ count($actions['list'])-1 ]['last'] = 1;
				}
				$actions_all_count = $this->actions->getActionsCount($pid);
				$actions['pagination'] = $this->pagination_controller->index_ajax($actions_all_count, $actions_count, $page, 'actions_ajax', ','.$pid);

				$actions['actions_list'] = $this->html->render('actions/actions_list.html', $actions);


				
				// -- дополнительные модули
				$actions['galleryBlock'] = $this->all_controller->images($pid, 0);
				$actions['filesBlock']   = $this->all_controller->files($pid,  0);
				
				
				/*хлебные крошки*/
				// $this->GetBreadCrums($actions['pid'],array('url' =>"" ,'title' =>$actions['title'],'last_link' =>true ));
				$this->layout = 'pages';
				$this->html->render('actions/'.$info['template'].'.html', $actions, 'content');
			} else { // -- работаем с конкретной новостью по id
				$actions = $this->actions->getOneactions($id);
				if(empty($actions)) {
					$this->main_controller->page_404();
					return false;
				}
				
				$info_title   = $this->db->get_one('SELECT title FROM main WHERE id = '.(int)$actions['pid']);
				$this->title .= ' | '.$info_title.' | '.$actions['title'];
				
				// -- собираем данные в одну кучку
				$is_long_text = true;
				if(file_exists($this->actions_path.$id.'.txt')) {
					$actions['text'] = file_get_contents($this->actions_path.$id.'.txt');
					$is_long_text = (mb_strlen(strip_tags($actions['text']), 'UTF-8') > 300);
				}
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($id, $this->module_id, $this->title);
				// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($id, $this->module_id, $actions['title'], $is_long_text);
				
				if ($actions['is_show_date']) {
					$ts = (!is_numeric($actions['date'])) ? strtotime($actions['date']) : $actions['date'];
					$actions['date'] = $this->date->format3($ts);
				} else unset($actions['date']);
				
				$actions['galleryBlock'] = $this->all_controller->images($actions['id'], $this->module_id);
				$actions['filesBlock']   = $this->all_controller->files($actions['id'],  $this->module_id);
				
				//Строка новостей
				// $this->html->tpl_vars['actions_line'] = $this->make_actions_line();
				/*хлебные крошки*/
				$this->GetBreadCrums($actions['pid'],array('url' =>"" ,'title' =>$actions['title'],'last_link' =>true ));
				$this->layout='pages';
				$this->html->render('actions/item.html', $actions, 'content');
			}
		}
						
		public function actions_ajax() {
			$pid = $_POST['pid'];
			$page = $_POST['page'];
			// $info = $this->actions->getPageInfo($pid);

			$actions_count = $this->config->get('actions_count', 'site');
			$actions_all_count = $this->actions->getActionsCount($pid);

			$actions['pagination'] = $this->pagination_controller->index_ajax($actions_all_count, $actions_count, $page, 'actions_ajax', ','.$pid);
			$actions['list'] = $this->actions->getActions($pid, $page-1, $actions_count);
			if(!empty($actions['list'])){
				foreach ($actions['list'] as $i => &$item) {
					$item['date'] = $this->date->format2($item['date']);
					$item['url'] = $this->actions_controller->get_url($item['id']);

					if(file_exists(INCLUDES.'actions/'.$item['id'].'.txt')){
						$item['text'] = htmlspecialchars_decode(file_get_contents(INCLUDES.'actions/'.$item['id'].'.txt'));
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
					if (mb_strlen($item['note'], 'UTF-8') > 120) {
						$item['note'] = mb_substr($item['note'], 0, 117, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
					}

				}
			}
			
			echo json_encode($this->html->render('actions/actions_list.html', $actions));
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
			
			$info = $this->actions->getPageInfo($pid);
			$actions = $this->actions->getAllactions($pid);
			if(empty($info) || empty($actions)) {
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
			
			foreach($actions as $i => &$item) {
				$ut = strtotime($item['date']);
				echo '		<item>'."\n";
				echo '			<title>'.getXMLValidText($item['title']).'</title>'."\n";
				echo '			<link>http://'.$domain.'/actions/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</link>'."\n";
				echo '			<description>'.getXMLValidText(strip_tags($item['note'])).'</description>'."\n";
				echo '			<pubDate>'.date("D, d M Y H:i:s O", $ut).'</pubDate>'."\n";
				echo '			<pubDateUT>'.$ut.'</pubDateUT>'."\n";
				echo '			<guid>http://'.$domain.'/actions/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</guid>'."\n";
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
					$data = $this->actions->getUrl($id);
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
					$data = $this->actions->getOneMY($id);
					$url = '/actions/'.$data['pid'].'/'.$data['year'].'/'.$data['month'].'/'.$data['id'];
					return $url;
				}
				else {
					return false;
				}
			}
		}

	}
?>
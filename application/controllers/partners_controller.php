<?php
	class partners_controller extends application_controller {

		private $partners_path;
		private $module_id = 13;
		
		public function __construct() {
			parent::__construct();
			$this->partners_path = $this->config->get('partners', 'files');
		}
		
		// $year, $month - нигде не используются, служат как декорации к URL
		public function index($pid = false, $year = false, $month = false, $id = false) {	
			session_start();		
			if(($this->config->get('active','chpu') == 1) && (!empty($pid)) && (!is_numeric($pid))){
				$id = $this->partners->getPagePidAlias($pid);
				$pid = $this->partners->getPaged($id);
			}

			// -- получаем данные по текущему разделу
			// var_dump($pid);
			// var_dump($this->module_id);
			// die();
			$info = $this->partners->getPageInfo($pid, $this->module_id);


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
				
				$partners['pageTitle'] = $info['title_page'];
				
				// -- шаблон по-умолчанию
				if(empty($info['template'])) {
					$info['template'] = 'layoutpartners';
				}
				
				// -- проверка на наличие text для всего раздела
				$partners['text'] = '';
				if(file_exists($this->partners_path.$pid.'_volume.txt')) {
					$partners['text'] = file_get_contents($this->partners_path.$pid.'_volume.txt');
				}
				$is_long_text = (mb_strlen(strip_tags($partners['text']), 'UTF-8') > 300);
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($pid, 0, $this->title);
				$partners['text'] = '<div class="list_note">'.$partners['text'].'</div>';
				
				// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($pid, 0, $info['title'], $is_long_text);
				
				$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
				$partners_count = $this->config->get('partners_count', 'site');
				$partners_all_count = $this->partners->getpartnersCount($pid);

				$partners['pagination'] = $this->pagination_controller->index_ajax($partners_all_count, $partners_count, $page, 'partners_ajax', ','.$pid);
				
				$partners['list'] = $this->partners->getpartners($pid, $page-1, $partners_count);
				
				if(!empty($partners['list'])) {
					$img_w = $this->config->get('img_width','partners');
					$img_h = $this->config->get('img_height','partners');
					
					foreach($partners['list'] as $i => &$item) {
						// -- форматирование даты
						$item['date'] = $this->date->format2($item['date']);
						$item['url'] = $this->get_url($item['id']);
						// -- прикрепляем картинку
						
						// -- черезполосица
						if($info['template'] == 'layoutpartners') {
							$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
						} else {
							$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
						}
						
						//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
						if(file_exists($this->partners_path.$item['id'].'.jpg')) {
							$item['img'] = '<a href="'.$item['url'].'">
												<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/partners/'.$item['id'].'.jpg" />
											</a>';
						} else $item['li_class'] .= ' noimage';
						
						if (mb_strlen($item['note'], 'UTF-8') > 100) {
							$item['note'] = mb_substr($item['note'], 0, 97, 'UTF-8');
							$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
						}
						
						// -- первый элемент
						if($i == 0) {
							$item['li_class'] .= ' first';
						}
					}
					
					// if (empty($info['text'])) {
						// $partners['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
					// }
					
					$partners['list'][ count($partners['list'])-1 ]['li_class'] .= ' last';
					$partners['list'][ count($partners['list'])-1 ]['last'] = 1;
				}
				$partners['partners_list'] = $this->html->render('partners/partners_list.html', $partners);
				
				// -- дополнительные модули
				$partners['galleryBlock'] = $this->all_controller->images($pid, 0);
				$partners['filesBlock']   = $this->all_controller->files($pid,  0);
				
				$partners['pid'] = $pid;
				$this->GetBreadCrums($partners['pid'],array('url' =>"" ,'title' =>$partners['title'],'last_link' =>true ));
				$this->layout = 'pages';

				$this->html->render('partners/'.$info['template'].'.html', $partners, 'container');
			} else { // -- работаем с конкретной новостью по id
				$partners = $this->partners->getOnepartners($id);
				if(empty($partners)) {
					$this->main_controller->page_404();
					return false;
				}
				
				$info_title   = $this->db->get_one('SELECT title FROM main WHERE id = '.(int)$partners['pid']);
				$this->title .= ' | '.$info_title.' | '.$partners['title'];
				
				// -- собираем данные в одну кучку
				$is_long_text = true;
				if(file_exists($this->partners_path.$id.'.txt')) {
					$partners['text'] = file_get_contents($this->partners_path.$id.'.txt');
					$is_long_text = (mb_strlen(strip_tags($partners['text']), 'UTF-8') > 300);
				}
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($id, $this->module_id, $this->title);
				// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($id, $this->module_id, $partners['title'], $is_long_text);
				
				if ($partners['is_show_date']) {
					$ts = (!is_numeric($partners['date'])) ? strtotime($partners['date']) : $partners['date'];
					$partners['date'] = $this->date->format3($ts);
				} else unset($partners['date']);
				
				$partners['galleryBlock'] = $this->all_controller->images($partners['id'], $this->module_id);
				$partners['filesBlock']   = $this->all_controller->files($partners['id'],  $this->module_id);
				
				//Строка новостей
				// $this->html->tpl_vars['partners_line'] = $this->make_partners_line();
				// $partners['another_projects_list'] = $this->partners->getAnotherProjects($partners['id'],4);
				$this->GetBreadCrums($partners['pid'],array('url' =>"" ,'title' =>$partners['title'],'last_link' =>true ));
				$this->layout='pages';
				$this->html->render('partners/item.html', $partners, 'container');
			}
		}
						
		public function partners_ajax($pid, $page) {
			$info = $this->partners->getPageInfo($pid);
			
			$partners_count = $this->config->get('partners_count', 'site');
			$partners_all_count = $this->partners->getpartnersCount($pid);

			$partners['pagination'] = $this->pagination_controller->index_ajax($partners_all_count, $partners_count, $page, 'partners_ajax', ','.$pid);
			
			$partners['list'] = $this->partners->getpartners($pid, $page-1, $partners_count);

			if(!empty($partners['list'])) {
				$img_w = $this->config->get('img_width','partners');
				$img_h = $this->config->get('img_height','partners');
				
				foreach($partners['list'] as $i => &$item) {
					// -- форматирование даты
					$item['date'] = $this->date->format2($item['date']);
					
					// -- черезполосица
					if($info['template'] == 'layoutpartners') {
						$item['li_class'] = ($i % 4 == 0 || ($i+1) % 4 == 0) ? 'even' : 'odd';
					} else {
						$item['li_class'] = ($i % 2 != 0) ? 'even' : 'odd';
					}
					
					// -- прикрепляем картинку
					//$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo.gif" />';
					if(file_exists($this->partners_path.$item['id'].'.jpg')) {
						$item['img'] = '<a href="/partners/'.$item['pid'].'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'">
											<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/partners/'.$item['id'].'.jpg"/>
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
					// $partners['list'][0]['style'] = 'border-top: 2px solid #7E7E7E; padding-top: 8px;';
				// }
				
				$partners['list'][ count($partners['list'])-1 ]['li_class'] .= ' last';
				$partners['list'][ count($partners['list'])-1 ]['last'] = 1;
			}
			$res=array();

			$res['list'] = $this->html->render('partners/partners_list.html', $partners);
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
			
			$info = $this->partners->getPageInfo($pid);
			$partners = $this->partners->getAllpartners($pid);
			if(empty($info) || empty($partners)) {
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
			
			foreach($partners as $i => &$item) {
				$ut = strtotime($item['date']);
				echo '		<item>'."\n";
				echo '			<title>'.getXMLValidText($item['title']).'</title>'."\n";
				echo '			<link>http://'.$domain.'/partners/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</link>'."\n";
				echo '			<description>'.getXMLValidText(strip_tags($item['note'])).'</description>'."\n";
				echo '			<pubDate>'.date("D, d M Y H:i:s O", $ut).'</pubDate>'."\n";
				echo '			<pubDateUT>'.$ut.'</pubDateUT>'."\n";
				echo '			<guid>http://'.$domain.'/partners/'.$pid.'/'.$item['year'].'/'.$item['month'].'/'.$item['id'].'/</guid>'."\n";
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
					$data = $this->partners->getUrl($id);
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
					$data = $this->partners->getOneMY($id);
					$url = '/partners/'.$data['pid'].'/'.$data['year'].'/'.$data['month'].'/'.$data['id'];
					return $url;
				}
				else {
					return false;
				}
			}
		}

	}
?>
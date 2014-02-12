<?php
	class pages_controller extends application_controller {
		
		private   $texts_path = null;
		protected $module_id = 1;
		
		public function __construct() {
			parent::__construct();
			$this->texts_path = $this->config->get('texts','files');
		}

		// Desc: Вывод контента страницы, с основными блоками
		// Return: Отрендеренный шаблон, который сохраняется в глобальном массиве tpl_vars - tpl_vars['content']
		public function index() {
			// -- получаем данные для раздела


			
			$page = $this->get_url(false);
			if(empty($page)) {
				$this->main_controller->page_404();
				return false;
			}
			$id = $page['id'];

			// // вызов ЧПУ
			// $this->application_controller->get_url($id);
			
			// -- теги-META
			$this->html->tpl_vars['meta_keywords']    = (empty($page['keywords']))    ? '' : trim($page['keywords']);
			$this->html->tpl_vars['meta_description'] = (empty($page['description'])) ? '' : trim($page['description']);
			
			$this->title .= ' | '.$page['title_page'];
			
			// -- помечаем активный раздел
			$this->active_main_id = $id;
			
			// -- получаем текст страницы
			if(file_exists($this->texts_path.$id.'.txt')) {
				$page['text'] = file_get_contents($this->texts_path.$id.'.txt');
			} else $page['text'] = '';
			$is_long_text = (mb_strlen(strip_tags($page['text']), 'UTF-8') > 300);
			
			// -- добавляем Яндекс.Карты, если нужно
			$this->all_controller->buildYandexMaps($page['id'], $this->module_id, $this->title);

			if(!empty($_POST['hid_model_id'])){
				$page['hid_model_id'] = $_POST['hid_model_id'];
			}
			else {
				$page['hid_model_id'] = false;
			}

			// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
			$this->all_controller->buildModulesBlock($page['id'], $this->module_id, $page['title'], $is_long_text, $page['hid_model_id']);
			
			if ($page['id'] == '243'){
				$page['kontakty'] = true;
			}

			if ($page['is_show_date']) {
				$ts = (!is_numeric($page['date'])) ? strtotime($page['date']) : $page['date'];
				$page['date'] = $this->date->format3($ts);
			} else unset($page['date']);
			
			
			// -- добавляем "фотогалерею", "файлы", "видео", если они есть
			$page_num = (empty($_GET['page'])) ? 1 : (int)$_GET['page'];
			$page['galleryBlock'] = $this->all_controller->images($id, $this->module_id, $page_num);
			$page['filesBlock']   = $this->all_controller->files($id,  $this->module_id);
			$page['videoBlock']   = $this->all_controller->video($id,  $this->module_id);
			
			//Строка новостей
			$this->html->tpl_vars['news_line'] = $this->make_news_line();
			// $this->html->tpl_vars['footer_text'] = file_get_contents('http://trios.ru/application/includes/text/'.$page['id'].'.txt');

			/*хлебные крошки*/
			$this->GetBreadCrums($page['pid'],array('url' =>"" ,'title' =>$page['title'],'last_link' =>true ));

			// -- рендерим основной шаблон
			$this->layout = 'pages';
			$this->html->render('pages/pages.html', $page, 'content');
		}

		// -- уникальный для tgic.ru метод - отдает xml для флешки
		public function gallery_xml($id) {
			$data = $this->db->get_all_one('SELECT id FROM images WHERE pid = '.(int)$id.' AND module_id = 1 ORDER BY sort LIMIT 10');
			$url  = $this->db->get_one('SELECT url FROM main WHERE id = '.(int)$id);
			header('Content-Type: text/xml; charset=cp-1251');
			if(!empty($data)) {
				echo '<aaa>'."\n";
				foreach($data as $i => $id) {
					echo '	<bbb>'."\n";
					echo '		<link>'.$url.'?show='.$id.'</link>'."\n";
					echo '		<img>/application/includes/img/l/'.$id.'.jpg</img>'."\n";
					echo '	</bbb>'."\n";
				}
				echo '</aaa>'."\n";
			}
			die();
		}
		public function get_url($id = false){
			if($this->config->get('active','chpu') == 1){
				$tmp = explode('/', $_SERVER['REQUEST_URI']);
				$tmp = array_reverse($tmp, true);
				foreach ($tmp as $i => &$item) {
					if(!empty($item)){
						$page = $this->page->getPageAlias($item);
						break;
					}
				}
			}
			else{
				$page = $this->page->getPage($_SERVER['REQUEST_URI']);
			}
			return $page;
		}
	}
?>
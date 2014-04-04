<?php	
	// Класс которым расширяются остальные классы, по сути он инклюдится везде, поэтому целесообразно 
	// размещать здесь то, что будет и должно отображаться на всех страницах

	class application_controller extends libs_controller {

		static protected $user_id = 0;       // -- id если мы аутентифицированы
	
		protected $active_main_id = 0;       // -- "main.id" активного раздела
		protected $active_catalog_id = 0;       // -- "catalog.id" активного раздела
		protected $active_path_id = array(); // -- массив активных путей
		protected $data           = null;    // -- $_POST['data'] для сохранения в моделях
		protected $title          = '';      // -- содержимое тега <title>
		protected $layout         = 'default';
		protected $BreadCrums     =  array();

		private $time_to_block = 7200; // 2 часа
		private $user_ip = null;
	
		public function __construct() {
			// -- empty
		}
		
		public function __before() {
			if(!empty($_SESSION)){
				session_start();
			}

			$_SESSION['id'] =$this->profile_controller->detectUser();// збс
			// -- определение аутентификации			
			$this->profile_controller->detectUser();
			
			if (!empty(self::$user_id)) {
				$user = $this->profile->getUser(self::$user_id);
				$this->html->tpl_vars['auth_title'] = $user['login'];
				$this->html->tpl_vars['auth'] 		= " exist";
			}

			$this->title = htmlspecialchars_decode($this->config->get('title_browser','site'));
			if($_SERVER['SERVER_NAME']=='midpo.ru'){
				$this->html->tpl_vars['main_domain'] = true;
			}

			$this->html->tpl_vars['title_h1'] 	   = $this->config->get('title_h1','site');
			$this->html->tpl_vars['contact_email'] = $this->config->get('contact_email','site');
			$this->html->tpl_vars['contact_email1'] = $this->config->get('contact_email1','site');

			/* HEADER BLOCK BEGIN */
			$this->html->tpl_vars['logo_text'] = $this->config->get('logo_text','site');
			$this->html->tpl_vars['header'] = $this->html->render('layouts/header.html');
			/* HEADER BLOCK END */

			
			// -- счетчик
			$this->html->tpl_vars['left_banner'] = $this->visban_controller->show(6);
			
			// графические ссылки
			$this->html->tpl_vars['reserve_link'] = $this->config->get('reserve_link', 'site');			

			//$this->comments_controller->getLastComments();
		}
		
		// -- выводим каптчу напрямую в браузер (MIME: image/jpeg)
		public function captcha($name) {
			$this->captcha->show($name);
			die();
		}
		
		// -- этот метод вызывается автоматически самым последним
		public function __after() {
			

			// -- строим массив активных путей
			$this->buildActivePath();
			
			/*времменный блок!!!!*/

			$this->html->tpl_vars['phone'] = $this->config->get('phone','site');
			$this->html->tpl_vars['mail'] = $this->config->get('mail','site');
			$this->html->tpl_vars['copy'] = htmlspecialchars_decode($this->config->get('copy','site'));
			$this->html->tpl_vars['copy2'] = $this->config->get('copy_2','site');

			/*конец временного блока*/
			
			$this->makeMenu();
			unset($data);


			// -- если <head> не отрендерили ранее, то рендерим <head> по умолчанию			
			if(empty($this->html->tpl_vars['head'])) {
				$this->html->render('head/head_default.html', array('site_title' => $this->title), 'head');
			}			

			/* QUESTION BLOCK BEGIN */

			$data = $this->question->getQuestion((!empty($_SERVER['HTTP_X_FORWARED_FOR'])) ? $_SERVER['HTTP_X_FORWARED_FOR'] : $_SERVER['REMOTE_ADDR'], $this->time_to_block);
			// -- если нет неотвеченных опросов, показываем результаты последнего
			if(empty($data)) {
				$data = $this->question->getResultLastQuestion();
				if(!empty($data['answer_list'])) {
					foreach($data['answer_list'] as $i => &$item) {
						$item['prc'] = round($item['prc']).'%';
					}
					$data['title'] = $this->config->get('quest_title_block', 'site');
					$this->html->tpl_vars['question_block'] = $this->html->render('question/block_result.html', $data);
				}
				// return true;
			}
			else {
				$data['title'] = $this->config->get('quest_title_block', 'site');
				$this->html->tpl_vars['question_block'] = $this->html->render('question/block_ask.html', $data);
			}

			/* QUESTION BLOCK END */

			/* FOOTER BLOCK BEGIN */

			$this->html->tpl_vars['footer_text'] = $this->config->get('footer_text','site');
			if(empty($this->html->tpl_vars['footer'])) {
		 	 	// $data = $this->makeFooter();
		 	 	for ($i=1; $i <= 5; $i++) { 
		 	 		$data['footer_item'.$i] = $this->config->get('footer_item'.$i,'site');
		 	 		$data['footer_link_item'.$i] = $this->config->get('footer_link_item'.$i,'site');
		 	 	}
		  		$this->html->render('footer/footer_default.html', $data , 'footer');
		  	}
			/* FOOTER BLOCK END */

			if($alert = $this->session->get('alert')) {
				$this->session->del('alert');
				$this->html->tpl_vars['alert'] = $alert;
			}
			
			// -- выводим всё на экран
			echo $this->html->render('layouts/layout_'.$this->layout.'.html');
			die();
		}

		// ЧПУ
		public function get_url($id){
			if(!empty($id)){
				if($this->config->get('active','chpu') == 1){
					$data = $this->all->getMainUrl($id);
					$url = '/'.$data['alias'];
					while(!empty($data['pid'])){
						$data = $this->all->getMainUrl($data['pid']);
						$url = '/'.$data['alias'].$url;
					}
					$url = $url.'/';
					return $url;
				}
				else{
					$url = $this->all->getStandartUrl($id);
					return $url;
				}
				
			}
			else{
				return false;
			}
		}

		public function makeFooter(){
			$menu = $this->menu->getMenu(0, 1);
			return $menu;
		}
		// -- формируем и рендерим шаблон меню
		private function makeMenu() {
					$menu = $this->menu->getMenu(0, 4); // вершина - 0, с 1 по 3 уровень

					foreach ($menu['list'] as &$item) {
						if (!empty($item['id'])){
							if ($this->active_main_id == $item['id']){
								$item['active'] = 'activeMenu';
							}
						}
					}
					$this->makeMenuReq($menu);
					if(!empty($menu)){
						foreach ($menu['list'] as $i => &$item) {
							if(stripos( $item['childs'], 'class="active')!== false){
								$item['is_active'] = 'preactive';
							}
						}
					}
					$this->html->render('menu/menu.html', $menu, 'menu_block');
					// $this->html->render('menu/foot_menu.html', $menu, 'foot_menu_block');
				}
				
		private function makeMenuReq(&$node, $curmain = false) {
	
					if(empty($node['list'])) return false;
					foreach($node['list'] as $i => &$item) {
						$item['title'] = htmlspecialchars($item['title']);				
						// -- если это модуль-ссылка
						if(!empty($item['link'])) {
							$item['url'] = $item['link'];
						}
						// -- проверка на активность раздела
							// var_dump($this->active_main_id);
						
						if(!empty($item['id']) && $item['id'] == $this->active_main_id) {
							$item['is_active'] = 'active';
 						} elseif(!empty($item['id']) && in_array($item['id'], $this->active_path_id)) {
							$item['is_active'] = 'preactive';
							// var_dump($item['id']);
						}

						if(!empty($item['lid']) && $item['lid'] == $this->active_catalog_id) {
							$item['is_active'] = 'active';
 						}
							// die();


						if(!empty($item['module']) && $item['module'] == 8){
							if(!empty($item['childs']['list'])){
								$item['is_parent'] = true;
								// foreach ($item['childs']['list'] as $j => &$value) {
								// 	// $value['url'] = '/popup/catalog/product/'.$value['cid'];
								// }
							}
						}
						// -- проверка на первый и последний элемент
						$item['class'] = '';
						if($i == 0) $item['class'] = 'first';
						if(count($node['list']) == $i+1) $item['class'] .= ($item['class'] == '' ? '' : ' ').'last';
						if($i == 4) $item['width'] = 'width:122px;';
						// $item['width'] = 'width:120px;';

						$item['last_item'] = ($i+1 == count($node['list'])) ? true : false;
						$item['first_item'] = ($i+1 == 1) ? true : false;
						
						if ($item['pid'] == 0)
							$curmain = $item;
							
						// -- формируем меню следующего уровня
						if(!empty($item['childs']['list'])) {
							$item['is_parent'] = true;
							$this->makeMenuReq($item['childs'],$curmain);
							$item['childs'] = $this->html->render('menu/sub_menu.html', $item['childs']);					
							$item['class'] .= ' childs';
						} else {
							$item['childs'] = false;
						}

						$item['last_item'] = ($i+1 == count($node['list'])) ? true : false;
							
						// if((array_key_exists('is_active',$item)) && ($item['is_active'] == 'active')){					
						// 	if(!empty($item['childs']))
						// 		$this->html->tpl_vars['left_part'] = $item['childs'];
						// 	else if(!empty($curmain['childs']))
						// 		$this->html->tpl_vars['left_part'] = $this->html->render('menu/sub_menu.html',$curmain['childs']);
						// }
					}
				}

		public function GetBreadCrums($pid=0, $last_url=false)
		{

			if ($pid!=0) {
				$data= $this->all->breadCrums($pid);
				$this->BreadCrums[]=$data;
				$this->GetBreadCrums($data['pid'],$last_url);
			}
			else{
				$this->BreadCrums=array_reverse($this->BreadCrums);
				if($this->config->get('active','chpu') == 1){
					foreach ($this->BreadCrums as $i => &$item) {
						$item['url'] = $this->application_controller->get_url($item['id']);
						// var_dump($item['url']);
					}
				}
				if(!empty($last_url)){
					if($last_url==1){
						 $this->BreadCrums[count($this->BreadCrums)-1]['last_link'] =  true;
						
					}
					else{
						 $this->BreadCrums[] =  $last_url;
					}
					
				}

				$this->html->tpl_vars['BreadCrums'] = $this->BreadCrums;
			}
		}
	

		private function buildActivePath() {
			if(empty($this->active_main_id)) return false;
			$id = $this->active_main_id;
			while($pid = (int)$this->db->get_one('SELECT pid FROM main WHERE id = '.$id)) {
				$this->active_path_id[] = $pid;
				$id = $pid;
			}
		}

		function sort_by_date($a, $b) { 
			if ($a['utc'] === $b['utc']) return 0; 
			return $a['utc'] < $b['utc'] ? 1 : -1; 
		} 
		
		
		//Блок новостей на главной
		protected function make_news_line() {
			$news['list'] = $this->news->getLast(54, 4);
			if (!empty($news['list'])) {
				$img_w = $this->config->get('img_width','news');
				$img_h = $this->config->get('img_height','news');
				$news_path = $this->config->get('news', 'files');
				
				foreach($news['list'] as $i => &$item) {
					$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/images/no_photo_big.gif" />';
					if(file_exists($news_path.$item['id'].'.jpg')) {
						$item['img'] = '<img width="'.$img_w.'" height="'.$img_h.'" src="/application/includes/news/'.$item['id'].'.jpg" />';
					} else $item['li_class'] = ' noimage';
				}
			}
			
			return $this->html->render('news/news_line.html', $news);
		}
	}
?>
<?php	
	// Класс которым расширяются остальные классы, по сути он инклюдится везде, поэтому целесообразно 
	// размещать здесь то, что будет и должно отображаться на всех страницах

	class application_controller extends libs_controller {

		static protected $user_id = 0;       // -- id если мы аутентифицированы
	
		protected $active_main_id = 0;       // -- "main.id" активного раздела
		protected $active_path_id = array(); // -- массив активных путей
		protected $data           = null;    // -- $_POST['data'] для сохранения в моделях
		protected $title          = '';      // -- содержимое тега <title>
		protected $layout         = 'default';
		protected $BreadCrums     =  array();
	
		public function __construct() {
			// -- empty
		}
		
		public function __before() {
			// -- определение аутентификации
		

			
			
			if(!empty($_SESSION)){
				session_start();
			}
			$this->title = htmlspecialchars_decode($this->config->get('title_browser','site'));
			if($_SERVER['SERVER_NAME']=='glonass.ru'){
				$this->html->tpl_vars['main_domain'] = true;
			}

			$this->html->tpl_vars['title_h1'] 	   = $this->config->get('title_h1','site');
			$this->html->tpl_vars['contact_email'] = $this->config->get('contact_email','site');
			$this->html->tpl_vars['contact_email1'] = $this->config->get('contact_email1','site');

			/* HEADER BLOCK BEGIN */
			$this->html->tpl_vars['header_phone'] = htmlspecialchars_decode($this->config->get('header_phone','site'));
			$this->html->tpl_vars['header_mail'] = $this->config->get('header_mail','site');
			$this->html->tpl_vars['header'] = $this->html->render('layouts/header.html');
			/* HEADER BLOCK END */

			// -- счетчик
		
			/* POPULAR PRODUCT BEGIN */ 

			$cat_pid = 301;
			$data['catalog_url'] = $this->application_controller->get_url($cat_pid); 

			// По порядку: новинки, лидер и 2 популярных товара
			$data['product_list'] = $this->catalog->getNewestList($cat_pid);
			array_push($data['product_list'], $this->catalog->getLeadList($cat_pid));
			$tmp = $this->catalog->getMostPopularList($cat_pid, 4);
			if(!empty($tmp)){
				foreach ($tmp as $i => &$item) {
					array_push($data['product_list'], $item);
				}
			}

			if(!empty($data['product_list'])){
				foreach ($data['product_list'] as $i => &$item) {
					$item['tchars'] = $this->catalog->getTechChars($item['pid'],$item['id']);
					if($this->config->get('active','chpu') == 1){
						$item['mid'] = $this->catalog->getMainIdProduct($item['id']);
						$item['url'] = $this->application_controller->get_url($item['mid']);
					}
					if (!empty($item['tchars'])){
						foreach ($item['tchars'] as $j => &$value) {
							$item['product_price'] = ($value['techchar_title'] == 'Цена') ? $value['techchar_value'] : false;
						}
					}
					$item['is_new'] = $item['is_new'] == 1 ? true : false;
					$item['is_leader'] = $item['is_leader'] == 1 ? true : false;
				}
			}
			$data['bubble_text'] = htmlspecialchars_decode($this->config->get('bubble_text','site'));
			$data['bubble_undertext'] = htmlspecialchars_decode($this->config->get('bubble_undertext','site'));
			$this->html->tpl_vars['pop_products'] = $this->html->render('catalog/pop_product.html', $data);
			
			/* POPULAR PRODUCT END */ 
	
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

			/* ACTIONS ADDITIONAL BLOCK BEGIN */
			$data['actions_list'] = $this->actions->getLast(289,4);
			if(!empty($data['actions_list'])){
				$this->main_controller->format_to_page($data, 'actions_list', 'actions_controller');
			}

			$this->html->render('actions/bottom_action_block.html', $data, 'bottom_action_block');
			/* ACTIONS ADDITIONAL BLOCK END*/

			/* NEWS-ACTIONS ADDITIONAL BLOCK BEGIN */
			$data['actions_list'] = $this->actions->getLast(289,2);
			$data['news_list'] = $this->news->getLast(288,3);
			if(!empty($data['actions_list'])){
				$this->main_controller->format_to_page($data, 'actions_list', 'actions_controller');
			}
			if(!empty($data['news_list'])){
				$this->main_controller->format_to_page($data, 'news_list', 'news_controller');
			}

			$this->html->render('pages/bottom_newsaction_block.html', $data, 'bottom_newsaction_block');
			/* NEWS-ACTIONS ADDITIONAL BLOCK END*/
			

			/* FOOTER BLOCK BEGIN */

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
						
						if($item['id'] == $this->active_main_id) {
							$item['is_active'] = 'active';
						} elseif(in_array($item['id'], $this->active_path_id)) {
							$item['is_active'] = 'preactive';
							// var_dump($item['id']);
						}
							// die();

						if(!empty($item['id']) && $item['id'] == $this->active_main_id) {
							$item['is_active'] = 'active';

 						} 				
						if($item['module'] == 8){
							if(!empty($item['childs']['list'])){
								$item['is_parent'] = true;
								foreach ($item['childs']['list'] as $j => &$value) {
									// $value['url'] = '/popup/catalog/product/'.$value['cid'];
								}
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
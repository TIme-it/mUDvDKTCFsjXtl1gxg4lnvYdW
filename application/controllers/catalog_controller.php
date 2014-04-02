<?php
	class catalog_controller extends application_controller {

		protected $path;
		protected $module_id = 8;
			
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('catalog', 'files');
		}
		
		// -- главная страница раздела
		public function index($cid = 0) {
			if(empty($cid))     { $this->main_controller->page_404(); return false; }
			$catalog = $this->catalog->getInfo($cid);
			if(empty($catalog)) { $this->main_controller->page_404(); return false; }
			
			// -- текст страницы
			if(file_exists($this->path.$cid.'_volume.txt')) {
				$catalog['text'] = file_get_contents($this->path.$cid.'_volume.txt');
			}
			$this->html->tpl_vars['footer_text'] = file_get_contents('http://trios.ru/application/includes/text/'.$cid.'.txt');
			// -- помечаем активный раздел
			$this->active_main_id = $cid;
			
			// -- SEO
			$this->html->tpl_vars['meta_description'] = $catalog['description'];
			$this->html->tpl_vars['meta_keywords']    = $catalog['keywords'];
			
			$this->title .= ' | '.$catalog['main_title'];
			
			// -- проверка на наличие text для всего раздела
			$catalog['text'] = '';
			if(file_exists($this->path.$cid.'_volume.txt')) {
				$catalog['text'] = file_get_contents($this->path.$cid.'_volume.txt');
			}
			$is_long_text = (mb_strlen(strip_tags($catalog['text']), 'UTF-8') > 300);
			
			// -- добавляем Яндекс.Карты, если нужно
			$this->all_controller->buildYandexMaps($cid, 0);
			
			// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
			$this->all_controller->buildModulesBlock($cid, 0, $catalog['main_title'], $is_long_text);
			
			// -- дополнительные модули
			$catalog['galleryBlock'] = $this->all_controller->images($cid, 0);
			$catalog['filesBlock']   = $this->all_controller->files($cid,  0);
			
			// -- категории и товары
			$catalog['categoryBlock'] = $this->categoryBlock($cid, 0);
		
			$page = (empty($_GET['page'])) ? 1 : (int)$_GET['page'];
				
			$catalog['productBlock']  = $this->productBlock($cid,  0, $page);

			/* ACTIONS BLOCK BEGIN */

			$this->html->tpl_vars['actions_list'] = $this->actions->getLast(42, 1);
			
			/* ACTIONS BLOCK END*/

			// основные категории курсов
			$catalog['nav_list'] = $this->catalog->getMainCategory($cid);
			if(!empty($catalog['nav_list'])){
				foreach ($catalog['nav_list'] as $i => &$item) {
					$item['url'] = $this->get_url($item['lid']);
					$item['courses_count'] = $this->catalog->getCountCategoryProduct($cid, $item['id']);
					if($_SERVER['REQUEST_URI'] == $item['url']){
						$item['active'] = true;
					}
				}
			}

			$this->html->tpl_vars['courses_nav'] = $this->html->render('catalog/nav.html', $catalog);
					
			$this->layout = 'catalog';
			$this->html->render('catalog/index.html', $catalog, 'content');
		}
		
		// -- страница товара
		public function product($id = 0) {
			// if(($this->config->get('active','chpu') == 1) && (!is_numeric($id))){
			// 	$id = $this->catalog->getPageIdAlias($id, 1);
			// }	

			$product  = $this->catalog->getProduct($id);
			$this->active_catalog_id = $product['lid'];
			// $mid = $this->catalog->getMidCategories($product['pid']);
			// $product['parent_url'] = $this->application_controller->get_url($mid);

			if(empty($product))  { $this->main_controller->page_404(); return false; }
			$main     = $this->catalog->getInfo($product['cid']);
			if(empty($main))     { $this->main_controller->page_404(); return false; }
						$this->title=$product['taitle']; 
									// -- текст страницы
			$product['text'] = '';
			if(file_exists($this->path.'catalog_product/'.$id.'_product.txt')) {
				$product['text'] = file_get_contents($this->path.'catalog_product/'.$id.'_product.txt');
			}
			$this->html->tpl_vars['meta_keywords']    = (empty($product['keywords']))    ? '' : trim($product['keywords']);
			$this->html->tpl_vars['meta_description'] = (empty($product['description'])) ? '' : trim($product['description']);	
			if ($product['taitle']!='') $this->title=$product['taitle'];
				else	$this->title=$product['title'];	
			// -- большое изображение товара
			$product['img_src'] = '/application/includes/images/no_product.gif';
			if(file_exists($this->path.'b/'.$id.'.png')) {
				$product['img_src'] = '/application/includes/catalog/b/'.$id.'.png';
			}
			
			// -- технические характеристики			
			// $tchars['list'] = $this->catalog->getTechChars($product['pid'],$product['id']);
			$product['tchars'] = $this->catalog->getTechChars($product['pid'],$product['id']);
			if (!empty($product['tchars'])){
				foreach ($product['tchars'] as $i => &$item) {
					$item['model_type'] = ($item['techchar_title'] == 'Тип') ? $item['techchar_value'] : false;
					$item['model_price'] = ($item['techchar_title'] == 'Цена') ? $item['techchar_value'] : false;
				}
			}
			
			// if(!empty($tchars['list'])) {
				// $this->html->render('catalog/product_techchars.html', $tchars, 'techchars');
			// }
			
			// -- крошки
			$breadcrumbs   = array();
			$breadcrumbs[] = array('/product/'.$product['id'].'/', $product['title']);
			$this->getCategoryPath($product['cid'], $product['pid'], $breadcrumbs);
			$breadcrumbs[] = array('/catalog/'.$main['main_id'].'/', $main['main_title']);
			if(!empty($product['type'])){
				$product['type'.$product['type']] = true;
			}			
			if(count(explode(',',$product['fin_type'])) == 2){
				$product['fin_type1'] = true;
				$product['fin_type2'] = true;
			} else if (count($product['fin_type']) == 1){
				$product['fin_type'.$product['fin_type'][0]] = true;
			}
			$product['breadcrumbs'] = $this->renderBreadcrumbs($breadcrumbs);
			
			// -- добавляем "фотогалерею", "файлы", "видео", если они есть
			$product['galleryBlock'] = $this->all_controller->images($id, $this->module_id);
			$product['filesBlock']   = $this->all_controller->files($id,  $this->module_id);
			$product['videoBlock']   = $this->all_controller->video($id,  $this->module_id);
			$product['date'] = $this->date->format2($product['date']);
			switch($product['geo']){
				case '1':
					$product['geo'] = 'по Автозаводскому району';
					break;
				case '2':
					$product['geo'] = 'по Центральному району';
					break;
				case '3':
					$product['geo'] = 'по Комсомольскому району';
					break;
				case '4':
					$product['geo'] = 'по городу';
					break;
				case '5':
					$product['geo'] = 'по области';
					break;
			}
			//$this->html->tpl_vars['footer_text'] = file_get_contents('http://trios.ru/application/includes/text/'.$main['id'].'.txt');
			// -- основной рендер
		//	$this->html->tpl_vars['footer_text'] = file_get_contents('http://trios.ru/application/includes/text/'.$product['id'].'.txt');

			// Подгрузка мини-галереи
			$product['minigallery'] = $this->minigallery->getAllImg($id);

			// Подгрузка конфигурации, характеристик и описания
			if(file_exists($this->path.'descript/'.$id.'.txt')) {
				$product['configuration'] = htmlspecialchars_decode(file_get_contents($this->path.'configuration/'.$id.'.txt'));
			}

			$this->layout = 'catalog';
			$nav = array(
				'product' => true,
				'subcategory_title' => mb_strtolower($breadcrumbs[1][1], 'UTF-8'),
				'category_title' =>  mb_strtolower($breadcrumbs[2][1], 'UTF-8'),
				'catalog_url' =>  $this->application_controller->get_url($product['cid']),
				'hours' =>  $product['hours'],
				'course_title' => $product['title'],
				);
			$this->html->tpl_vars['is_product'] = true;

			/* ACTIONS BLOCK BEGIN */

			$this->html->tpl_vars['actions_list'] = $this->actions->getLast(42, 1);
			
			/* ACTIONS BLOCK END*/

			$this->html->tpl_vars['courses_nav'] = $this->html->render('popup/sign_up.html', $nav);
			$this->html->render('catalog/product.html', $product, 'content');
		}

		public function add_product(){
			$product = array();
			$product['categorys'] = $this->catalog->getCategoryList(77, 0);
			$this->html->render('catalog/add_product.html', $product, 'content');
		}

		public function get_tchars(){
			$pid = (int)$_POST['pid'];
			if(!empty($_POST['id'])){
					$id = (int)$_POST['id'];
			}
			else{
					$id = 0;
			}
		
			$product['tchars'] = $this->catalog->getTechChars($pid,$id);
			echo $this->html->render('catalog/tchars.html', $product);
			die();
		}

		public function save_product() {
		if($_POST['captcha'] !== $this->session->get('captcha_catalog')) {
			$this->session->set('alert', 'Вы не верно ввели код с картинки');
			$this->url->redirect('::referer');
		}					
			$cid = 77;
			$pid     = (int)$_POST['tech_type'];
			$product = array(
				'pid'         => (int)$pid,
				'cid'         => (int)$cid,
				'title'       => trim($_POST['title']),
				'note'        => trim($_POST['note']),
				'price_hour'       => (float)$_POST['price_hour'],
				'price_turn'       => (float)$_POST['price_turn'],
				'geo'		=> (int)$_POST['geo'],
				'type'		=> (int)$_POST['type'],
				'author'		=> trim($_POST['author']),
				'phone'		=> trim($_POST['phone']),
				'fin_type' => !empty($_POST['fin_type']) ? join(',', $_POST['fin_type']) : 0,
				'taitle'    => trim($_POST['taitle']),
				'description' => trim($_POST['description']),
				'keywords'    => trim($_POST['keywords']),
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)(!empty($_POST['feedback'])),
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
				'date'		  => time(),
				'active'	  => 0,
			);
			if(!empty($id)) {				
				$this->db->update('catalog', $product, $id);
			} else {
				$id = $this->db->insert('catalog', $product);
			}
			
			// -- сохраняем технические характеристики
			if(!empty($_POST['techchar_id'])) {
				$this->db->delete('catalog_techchars_links', array('catalog_id' => $id));
				foreach($_POST['techchar_id'] as $key => $item) {					
					$tchar = array(
						'catalog_id' => $id,
						'cat_cat_techchars_id' => $_POST['techchar_id'][$key],
						'value' => $_POST['techchar_value'][$key]
					);
					$this->db->insert('catalog_techchars_links',$tchar);
				}
			}
						
			// -- характеризующая картинка
			if(isset($_FILES['main_img']['tmp_name']) && file_exists($_FILES['main_img']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['main_img']['name']);
				if(!empty($file) && is_uploaded_file($_FILES['main_img']['tmp_name'])) {					
					$image = $this->image->analyze($_FILES['main_img']['tmp_name']);
					if(!empty($image)) {
						$img_b_w = (int)$this->config->get('product_big_w',   'images');
						$img_b_h = (int)$this->config->get('product_big_h',   'images');
						$img_s_w = (int)$this->config->get('product_small_w', 'images');
						$img_s_h = (int)$this->config->get('product_small_h', 'images');
						$this->image->resize($img_b_w, $img_b_h);
						$this->image->toFile(INCLUDES.'/catalog/b/'.$id.'.jpg');
						$this->image->resize($img_s_w, $img_s_h);
						$this->image->toFile(INCLUDES.'/catalog/s/'.$id.'.jpg');

						// $this->image->toFile($this->path.'b/'.$id.'.jpg', 80, $img_b_w, $img_b_h);
						// $this->image->toFile($this->path.'s/'.$id.'.jpg', 80, $img_s_w, $img_s_h);
					}
				}
			}
			
			$this->session->set('alert', 'Объявление добавлено успешно и будет размещено на сайте после одобрения модератором.');			
			$this->url->redirect('/');
		}
		
		// -- страница подкатегории
		public function category($id = 0) {
			// if(($this->config->get('active','chpu') == 1) && (!is_numeric($id))){
			// 	$id = $this->catalog->getPageIdAlias($id, 0);
			// }	

			$category = $this->catalog->getCategory($id);

			if($category['pid'] == 0){
				$this->active_catalog_id = $category['lid'];
			}
			else {
				$this->active_catalog_id = $this->catalog->getParentLid($category['cid'], $category['pid']);
			}


			$category['tchars'] = $this->catalog->getTechChars($id);
			if(empty($category)) { $this->main_controller->page_404(); return false; }
			if(!empty($category['cid'])){
				$main     = $this->catalog->getInfo($category['cid']);
			}
			if(empty($main))     { $this->main_controller->page_404(); return false; }
			if ($category['taitle']!='') $this->title=$category['taitle'];
				else	$this->title=$category['title'];		
									// -- текст страницы
			if(file_exists($this->path.$id.'_category.txt')) {
				$category['text'] = file_get_contents($this->path.$id.'_category.txt');
			}
			
			// -- категории и товары
			$category['categoryBlock'] = $this->categoryBlock($category['cid'], $id);
			$this->html->tpl_vars['meta_keywords']    = (empty($category['keywords']))    ? '' : trim($category['keywords']);
			$this->html->tpl_vars['meta_description'] = (empty($category['description'])) ? '' : trim($category['description']);	
			// $category['productBlock']  = $this->productBlock($category['cid'], $id);
			// -- крошки
			$breadcrumbs = array();
			$this->getCategoryPath($category['cid'], $category['id'], $breadcrumbs);
			$breadcrumbs[] = array('/catalog/'.$main['main_id'].'/', $main['main_title']);
			// $category['breadcrumbs'] = $this->renderBreadcrumbs($breadcrumbs);
			// $this->html->tpl_vars['footer_text'] = file_get_contents('http://trios.ru/application/includes/text/'.$category['id'].'.txt');
			// -- основной рендер
			$this->layout = 'catalog';

			// основные категории курсов
			$category['nav_list'] = $this->catalog->getMainCategory($category['cid']);
			if(!empty($category['nav_list'])){
				foreach ($category['nav_list'] as $i => &$item) {
					$item['url'] = $this->get_url($item['lid']);
					$item['courses_count'] = $this->catalog->getCountCategoryProduct($category['cid'], $item['id']);
					if($_SERVER['REQUEST_URI'] == $item['url']){
						$category['purl'] = $item['url'];
						$item['active'] = true;
					}
					if(stristr($_SERVER['REQUEST_URI'], $item['url']) !== FALSE){
						$category['purl'] = $item['url'];
						$item['active'] = true;
					}
				}
			}

			// подкатегории курсов
			if($category['pid'] == 0){
				$category['subtype'] = $this->catalog->getSubType($category['cid'], $id);
			}
			else {
				$category['subtype'] = $this->catalog->getSubType($category['cid'], $category['pid']);
			}
			if(!empty($category['subtype'])){
				foreach ($category['subtype'] as $i => &$item) {
					$item['url'] = $this->get_url($item['lid']);
					if($_SERVER['REQUEST_URI'] == $item['url']) {
						$item['selected'] = 'selected=selected';
					}
				}
			}

			// продукты категории
			if($category['pid'] == 0){
				$category['product'] = $this->catalog->getCategoryProduct($category['cid'], $id);
			}
			// продукты подкатегории
			else { 
				$category['product'] = $this->catalog->getSubCategoryProduct($category['cid'], $id);
			}
			if(!empty($category['product'])){
				foreach ($category['product'] as $i => &$item) {
					$item['url'] = $this->get_url($item['lid']);
				}
			}

			/* ACTIONS BLOCK BEGIN */

			$this->html->tpl_vars['actions_list'] = $this->actions->getLast(42, 1);
			
			/* ACTIONS BLOCK END*/

			$this->html->tpl_vars['courses_nav'] = $this->html->render('catalog/nav.html', $category);
			$this->html->render('catalog/category.html', $category, 'content');
		}
		
		// -- генерируем блок товаров
		private function productBlock($cid, $pid, $page = 1) {	
			$product_count = $this->config->get('product_count', 'site');
			$product_all_count = $this->catalog->getProductCount($cid, $pid);
		
		
			$product['pagination'] = $this->pagination_controller->index_ajax($product_all_count, $product_count, $page, 'productBlock_ajax', ', '.$pid.', '.$cid);
			
			$product['list'] = $this->catalog->getSimpleProductList($cid, $pid, $page-1);
			$product['tchars'] = $this->catalog->getTechChars($pid);
			
			if(!empty($product['list'])) {
				foreach($product['list'] as $i => &$item) {
					// -- по-умолчанию ставится заглушка
					
					$item['img_src'] = '/application/includes/images/no_photo.gif';
					if(file_exists($this->path.'b/'.$item['id'].'.png')) {
						$item['img_src'] = '/application/includes/catalog/b/'.$item['id'].'.png';
					}

					if (mb_strlen($item['note'], 'UTF-8') > 420) {
						$item['note'] = mb_substr($item['note'], 0, 417, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...</p>';
					}

					if($this->config->get('active','chpu') == 1){
						$item['mid'] = $this->catalog->getMainIdProduct($item['id']);
						$item['url'] = $this->get_url($item['lid']);
					}
					
					$item['title_hsc'] = htmlspecialchars($item['title']);
					
					// $item['price']     = $this->catalog->getPrice($item['price']);
					
					$item['tchars'] = $this->catalog->getTechChars($pid,$item['id']);
					if (!empty($item['tchars'])){
						foreach ($item['tchars'] as $j => &$value) {
							$value['model_type'] = ($value['techchar_title'] == 'Тип') ? $value['techchar_value'] : false;
							$value['model_price'] = ($value['techchar_title'] == 'Цена') ? $value['techchar_value'] : false;
						}
					}
				}
				// -- размеры изображений
				$product['img_size_w'] = $this->config->get('product_small_w', 'images');
				$product['img_size_h'] = $this->config->get('product_small_h', 'images');
				$product['first_name_column']=$this->catalog->getCategoryFirstName($cid, $pid);
				$this->layout = 'catalog';

					return $this->html->render('catalog/product_block.html', $product);
					
			}
			return '';
		}
		
		// -- генерируем блок товаров
		public function productBlock_ajax() {	
			$pid = $_POST['pid'];
			$cid = $_POST['cid'];
			$page = $_POST['page'];	
			$product_count = $this->config->get('product_count', 'site');
			$product_all_count = $this->catalog->getProductCount($cid, $pid);
		
		
			$product['pagination'] = $this->pagination_controller->index_ajax($product_all_count, $product_count, $page, 'productBlock_ajax', ','.$pid.', '.$cid);
			
			$product['list'] = $this->catalog->getSimpleProductList($cid, $pid, $page-1);
			$product['tchars'] = $this->catalog->getTechChars($pid);
			
			if(!empty($product['list'])) {
				foreach($product['list'] as $i => &$item) {
					// -- по-умолчанию ставится заглушка
					
					$item['img_src'] = '/application/includes/images/no_photo.gif';
					if(file_exists($this->path.'b/'.$item['id'].'.png')) {
						$item['img_src'] = '/application/includes/catalog/b/'.$item['id'].'.png';
					}

					if (mb_strlen($item['note'], 'UTF-8') > 420) {
						$item['note'] = mb_substr($item['note'], 0, 417, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...</p>';
					}

					if($this->config->get('active','chpu') == 1){
						$item['mid'] = $this->catalog->getMainIdProduct($item['id']);
						$item['url'] = $this->application_controller->get_url($item['mid']);
					}
					
					$item['title_hsc'] = htmlspecialchars($item['title']);
					
					// $item['price']     = $this->catalog->getPrice($item['price']);
					
					$item['tchars'] = $this->catalog->getTechChars($pid,$item['id']);
					if (!empty($item['tchars'])){
						foreach ($item['tchars'] as $j => &$value) {
							$value['model_type'] = ($value['techchar_title'] == 'Тип') ? $value['techchar_value'] : false;
							$value['model_price'] = ($value['techchar_title'] == 'Цена') ? $value['techchar_value'] : false;
						}
					}
				}
				// -- размеры изображений
				$product['img_size_w'] = $this->config->get('product_small_w', 'images');
				$product['img_size_h'] = $this->config->get('product_small_h', 'images');
				$product['first_name_column']=$this->catalog->getCategoryFirstName($cid, $pid);
				$this->layout = 'catalog';
					echo json_encode($this->html->render('catalog/product_block.html', $product));
					die();
					
			}
		}
		
		// -- генерируем блок подкатегорий
		public function categoryBlock($cid, $pid, $limit = 0) {
			$category['list'] = $this->catalog->getCategoryList($cid, $pid, $limit);
			if(!empty($pid)){
				$category['tchars'] = $this->catalog->getTechChars($pid);
			}
			if(!empty($category['list'])) {
				foreach($category['list'] as $i => &$item) {
					$item['title_hsc'] = htmlspecialchars($item['title']);
					$item['url'] = $this->get_url($item['lid']);
					if(!file_exists(INCLUDES.'catalog/catalog_category/type/'.$item['id'].'.png')){
						$item['no_img'] = true;
					}
					if (mb_strlen($item['note'], 'UTF-8') > 50) {
						$item['note'] = mb_substr($item['note'], 0, 47, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
					}
				}
				$this->layout = 'catalog';
				return $this->html->render('catalog/category_block.html', $category);
			}
			return '';
		}
		
		// -- получаем путь категорий для постоения крошек
		private function getCategoryPath($cid, $id, &$data) {
			if(!$id) return false;
			$sql  = 'SELECT title, pid FROM catalog_categories '.
					'WHERE cid = '.(int)$cid.' AND id = '.(int)$id;
			$category = $this->db->get_row($sql);
			if(!empty($category)) {
				$pid    = $category['pid'];
				$data[] = array('/catalog/category/'.$id.'/', $category['title']);
				$this->getCategoryPath($cid, $pid, $data);
			}
		}
	public function catalog_ajax($id, $page) {
		$category = $this->catalog->getCategory($id);
		$res=array();
		$res = $this->productBlock($category['cid'], $id, $page );
		$res =$res.$category['cid'].','.$id.','.$page ;
		die(json_encode($res));
	}
		// -- рендерим крошки для каталога
		private function renderBreadcrumbs($bcrumbs) {
			$html = '<h2>Навигация по каталогу:</h2>'.
					'<ul id="catalog_breadcrumbs">';
			$bcrumbs = array_reverse($bcrumbs);
			foreach($bcrumbs as $i => $data) {
				if($i > 0) $html .= '<li> &gt; </li>';
				if($i < count($bcrumbs)-1) {
					$html .= '<li><a href="'.$data[0].'">'.$data[1].'</a></li>';
				} else {
					$html .= '<li>'.$data[1].'</li>';
				}
			}
			$html .= '</ul><br clear="all" />';
			return $html;
		}

	public function filter(){
			$filter = array(
				'pid' => (int)$_POST['tech_type'],
				'fin_type' => $_POST['fin_type'],
				'type' => (int)$_POST['type'],
				'geo' => (int)$_POST['geo'],
				'price_from' => (int)$_POST['price_from'],
				'price_to' => (int)$_POST['price_to'],
				'price_from_hour' => (int)$_POST['price_from_hour'],
				'price_to_hour' => (int)$_POST['price_to_hour'],

			);
			
			$category = $this->catalog->getCategory($filter['pid']);
			$category['tchars'] = $this->catalog->getTechChars($filter['pid']);

			// // -- категории и товары
			$category['categoryBlock'] = $this->categoryBlock($category['cid'], $category['id']);
			$this->html->tpl_vars['meta_keywords']    = (empty($category['keywords']))    ? '' : trim($category['keywords']);
			$this->html->tpl_vars['meta_description'] = (empty($category['description'])) ? '' : trim($category['description']);	

			$cid = $category['cid'];
			$pid = $category['id'];
			$page = 1;
			$product_count = $this->config->get('product_count', 'site');
			$product_all_count = $this->catalog->getProductCount($cid, $pid);
		
		
			$product['pagination'] = $this->pagination_controller->index($product_all_count, $product_count,$pid,$page);
			
			$product['list'] = $this->catalog->getProductList($cid, $pid, $page-1, $filter);
			// $product['tchars'] = $this->catalog->getTechChars($pid);
			
			if(!empty($product['list'])) {
				foreach($product['list'] as $i => &$item) {
					// -- по-умолчанию ставится заглушка
					$fl = 0;
					if(!empty($_POST['techchar_value']))
					foreach ($_POST['techchar_value'] as $key => &$value) {
						if(!empty($item['techchars'])){
							

							foreach ($item['techchars'] as $j => &$tch) {
								if($_POST['techchar_id'][$key]==$tch['techchar_id']){
									if($tch['techchar_value']<$value){
										unset($product['list'][$i]);
										$fl=1;
										break;
									}
								}
								else{
									continue;
								}
							}
							if($fl){
								break;
							}
						}
						
					}
					$item['img_src'] = '/application/includes/images/no_photo_product.gif';
					if(file_exists($this->path.'b/'.$item['id'].'.png')) {
						$item['img_src'] = '/application/includes/catalog/b/'.$item['id'].'.png';
					}
					
					$item['title_hsc'] = htmlspecialchars($item['title']);
					
					// $item['price']     = $this->catalog->getPrice($item['price']);
					
					// $item['tchars'] = $this->catalog->getTechChars($pid,$item['id']);

					
				}
					
				// -- размеры изображений
				$product['img_size_w'] = $this->config->get('product_small_w', 'images');
				$product['img_size_h'] = $this->config->get('product_small_h', 'images');
				$product['first_name_column']=$this->catalog->getCategoryFirstName($cid, $pid);
				
				$category['productBlock'] = $this->html->render('catalog/product_block.html', $product);
					
			}
			
			// -- основной рендер
			$this->html->render('catalog/category.html', $category, 'content');
	}

	// Заказ товара
	public function order(){
		echo $this->html->render('catalog/order.html', array());
		die();
	}

	// Отправка письма для формы "Запись на консультацию"
	public function order_send(){
		session_start();
		if ((!empty($_POST['capcha'])) && ($_POST['capcha'] != $_SESSION['captcha_cap'])) {
			$message = 'Вы ввели неверный код';
			$this->session->set('alert', $message);
		}

		elseif(!empty($_POST)){
			$quest = array(
				'fio'       => strip_tags($_POST['fio']),
				'phone'  => strip_tags($_POST['phone']),
				'email' => strip_tags($_POST['email']),
				'question' => strip_tags($_POST['question']),
				'id' => strip_tags($_POST['id'])
			);

			$quest['mid'] = $this->catalog->getMainIdProduct($quest['id']);
			$quest['domain'] = $this->config->get('domain', 'site');
			$quest['url'] = $this->application_controller->get_url($quest['mid']);

			$letter  = $this->html->render('catalog/order_send.html', $quest);
			$subject = 'Заказ товара';
			$to      = $this->config->get('contact_email', 'site');

			$this->mail->send_mail($to, $letter, $subject);

			$message = 'Ваш заказ успешно принят!';
			$this->session->set('alert', $message);
		}
		$this->url->redirect('::referer');
	}

	// ЧПУ
	public function get_url($lid){
		if(!empty($lid)){
			if($this->config->get('active','chpu') == 1){
				$data = $this->catalog->getCatalogUrl($lid);
				$url = '/'.$data['alias'];
				while(!empty($data['pid'])){
					$data = $this->catalog->getCatalogUrl($data['pid']);
					$url = '/'.$data['alias'].$url;
				}
				$url = '/'.$data['root_alias'].$url.'/';
				return $url;
			}				
		}
		else{
			return false;
		}
	}
}


?>
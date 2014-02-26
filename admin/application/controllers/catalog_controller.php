<?php
	class catalog_controller extends application_controller {

		protected $path;
		protected $module_id = 8;

		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('catalog','files');
		}

		public function index() {
			$this->url->redirect('::referer');
		}
		
		// -- обзор основной страницы модуля
		public function view($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$catalog = $this->catalog->getInfo($id);
			if(!empty($catalog)) {
				$catalog['main_title'] = htmlspecialchars($catalog['main_title']);
				$catalog['title_page'] = htmlspecialchars($catalog['title_page']);
			}
			
			// -- ссылка на удаление раздела
			$catalog['delete_button'] = '<a href="/admin/module/delete/'.$id.'/" onclick=\'if(!confirm("Вы действительно хотите удалить?")) return false;\' class="trash_button">Удалить раздел</a>';
			
			// -- описание раздела
			if(file_exists($this->path.$id.'_volume.txt')) {
				$catalog['text'] = file_get_contents($this->path.$id.'_volume.txt');
			}
			if (isset($catalog['text']))
			{
				$catalog['image']=$catalog['text'];
		
			
			$catalog['image']=preg_replace('#(src.{7}http://www.trios.ru/Pages/)#','src="/application/includes/pagesImg/',$catalog['text']);
			//preg_match_all('/(.{4}a href.{7}http://www.trios.ru/Pages/(.*)a)/', $page['text'], $buf);
			preg_match_all('/.{4}a.href.{10,30}trios(.*).lt.\/a.{4}/Uix', $catalog['text'], $buf);
		
			$catalog['timage']='';

				foreach($buf[0] as $ke=>$item)
				//var_dump($buf);
							$catalog['timage'].=$ke.')'.$item; 
				$catalog['text']=$catalog['image'];
				$catalog['image']='';
			}
			
			// -- системные модули
			$catalog['yandex_maps_block'] = $this->maps_controller->generateBlock($id, 0);
			$catalog['modules_block']     = $this->all_controller->generateModulesBlock($id, 0);
			$catalog['settings_block']    = $this->all_controller->generateSettingsBlock($id, $this->module_id);
			
			// -- товары и подкатегории
			$catalog['product_list']  = $this->catalog->getProductList($id, 0);
			$catalog['category_list'] = $this->catalog->getCategoryList($id, 0);
			$catalog['techchars'] = $this->catalog->get_techchars();
			$catalog['techcharList'] = $this->catalog->get_catalog_techchar($id);
			$catalog['id'] = $id;

			$this->html->render('catalog/catalog_dirs.html', $catalog, 'dirs');
			$this->html->render('catalog/view.html', $catalog, 'content_path');
		}

		// -- сохраняем данных основного раздела
		public function save_main($id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $id)) $this->role_controller->AccessError();
			
			$main = array(
				'pid'         => (int)$_POST['pid'],
				'title'       => trim($_POST['title']),
				'title_page'  => trim($_POST['title_page']),
				'description' => trim($_POST['description']),
				'keywords'    => trim($_POST['keywords']),
				'template'    => trim($_POST['template']),
				'inmenu'      => (int)$_POST['inmenu'],
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)(!empty($_POST['feedback'])),
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
				'subsection'  => (int)(!empty($_POST['subsection'])),
			);
			$this->db->update('main', $main, $id);
			
			$this->file->toFile($this->path.$id.'_volume.txt', $_POST['text']);
			
			// -- особая ситуация: изменение основного модуля
			if((int)$_POST['module_id'] !== $this->module_id) {
				$this->module_controller->changeModule($id, $this->module_id, (int)$_POST['module_id'], $_POST['link']);
			}
			
			// -- сохранение конфига
			$config['site'] = array(
				'print_word'     => $_POST['print_word'],
				'feedback_email' => $_POST['feedback_email'],
			);
			$this->config_controller->add_config($config);
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect($this->main->buildAdminURL($id));
		}

		// -- обзор категории
		public function category($cid, $pid, $id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $cid)) $this->role_controller->AccessError();
			
			$category['main_pid'] = (int)$pid;
			$category['main_cid'] = (int)$cid;
			$category['id'] = (int)$id;
			
			// -- заголовок сверху
			$category['title_catalog'] =
				'<a href="/admin/catalog/view/'.$cid.'/">'.
				$this->db->get_one('SELECT title FROM main WHERE id = '.(int)$cid).
				'</a>';
			$category['taitle'] =$this->db->get_one('SELECT taitle FROM catalog_categories WHERE id = '.(int)$id);	
			if ($this->db->get_one('SELECT taitle FROM catalog_categories WHERE id = '.(int)$id)!='') $category['taitle']=$this->db->get_one('SELECT taitle FROM catalog_categories WHERE id = '.(int)$id);
				else $category['taitle']=$this->db->get_one('SELECT title FROM catalog_categories WHERE id = '.(int)$id);
			if(empty($id)) {
				// -- если это для добавления
				$category['main_id']     = 0;
				$category['title_page']  = 'Новая категория';
				$category['cat_display'] = 'none';
			} else {
				// -- если это для изменения
				$cat_item = $this->catalog->getCategory($id);
				if(empty($cat_item)) {
					// -- попали на несуществующую категорию
					$this->url->redirect('::referer');
				}
				$act_pid = (int)$pid;
				// -- страдаем этой херней, т.к. шаблонизатор долбанутый
				$category['note']       = $cat_item['note'];
				$category['main_id']    = $cat_item['id'];
				$category['main_title'] = $cat_item['title'];
				// -- строим что-то вроде крошек
				$category['title_page'] = $cat_item['title'];
				while($act_pid > 0) {
					$sql  = 'SELECT id, pid, title FROM catalog_categories '.
							'WHERE id = '.$act_pid;
					$temp = $this->db->get_row($sql);
					if(!empty($temp)) {
						$category['title_page'] =
							'<a href="/admin/catalog/category/'.$cid.'/'.$temp['pid'].'/'.$temp['id'].'/">'.
							$temp['title'].'</a> / '.$category['title_page'];
						$act_pid = $temp['pid'];
					} else break;
				}
				if(file_exists($this->path.$cat_item['id'].'_category.txt')) {
					$category['text'] = file_get_contents($this->path.$cat_item['id'].'_category.txt');
				}
				if (isset($category['text']))
					{
						$category['image']=$category['text'];
				
					
					$category['image']=preg_replace('#(src.{7}http://www.trios.ru/Pages/)#','src="/application/includes/pagesImg/',$category['text']);
					//preg_match_all('/(.{4}a href.{7}http://www.trios.ru/Pages/(.*)a)/', $page['text'], $buf);
					preg_match_all('/.{4}a.href.{10,30}trios(.*).lt.\/a.{4}/Uix', $category['text'], $buf);
				
					$category['timage']='';
		
						foreach($buf[0] as $ke=>$item)
						//var_dump($buf);
									$category['timage'].=$ke.')'.$item; 
						$category['text']=$category['image'];
						$category['image']='';
					}
				// -- товары и подкатегории
				$category['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
				$category['files_block']       = $this->addmodules_controller->generateFilesBlock($id,  $this->module_id);
				$category['prod_display']  = 'block';
				$category['product_list']  = $this->catalog->getProductList($cid,  $id);

				if(!empty($category['product_list'])){
					foreach ($category['product_list'] as $i => &$item) {
						if($item['is_leader'] == 1){
							$item['option_style'] = 'style="color: #ffaa00;"';
						}
						elseif($item['is_new'] == 1){
							$item['option_style'] = 'style="color: #00aa00;"';
						}
						elseif($item['is_popular'] == 1){
							$item['option_style'] = 'style="color: #ee0000;"';
						}
					}
				}

				$category['category_list'] = $this->catalog->getCategoryList($cid, $id);				
				$category['techchars'] = $this->catalog->get_techchars();
				$category['techcharList'] = $this->catalog->get_category_techchar($id);
				$category['cat_id'] = $id;
				$category['first_name_column']=$this->catalog->getCategoryFirstName($cid, $id);
					
			}
			//var_dump($category['techcharList']);
			//$category['list1']=$category['techcharList'];
			$category['alias'] = $this->catalog->getMainAlias($id, 1);

			$this->html->render('catalog/dirs.html', $category, 'dirs');
			$this->html->render('catalog/category.html', $category, 'content_path');
		}
		
		public function get_techchar_list($category){
			$data['techcharList'] = $this->catalog->get_category_techchar($category);			
			echo $this->html->render('catalog/dirs.html',$data);
			die();
		}
		
		public function get_catalog_techchar_list($catalog){
			$data['techcharList'] = $this->catalog->get_catalog_techchar($catalog);			
			echo $this->html->render('catalog/dirs.html',$data);
			die();
		}
		
		public function add_catalog_techchar(){
			$data = array(
				'category_id' => $_POST['category_id'],
				'techchar_id' => $_POST['techchar_id']
			);
			$this->catalog->add_catalog_techchar($data);
			die();
		}

		public function add_main_techchar(){
			$data = array(
				'catalog_id' => $_POST['catalog_id'],
				'techchar_id' => $_POST['techchar_id']
			);
			$this->catalog->add_catalog_techchar($data);
			die();
		}
		
		public function del_catalog_techchar($id){			
			$this->catalog->del_catalog_techchar($id);
			die();
		}
		
		public function del_main_techchar($id){			
			$this->catalog->del_catalog_techchar($id);
			die();
		}
		
		// -- сохраняем данных категории
		public function save_category($cid, $pid, $id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $cid)) $this->role_controller->AccessError();
			
			$category = array(
				'pid'         => (int)$pid,
				'cid'         => (int)$cid,
				'title'       => trim($_POST['title']),
				'note'        => trim($_POST['note']),
				'description' => trim($_POST['description']),
				'keywords'    => trim($_POST['keywords']),
				'first_name_column'    => trim($_POST['first_name_column']),
				'taitle'    => trim($_POST['taitle']),
			);

			if(!empty($id)) {	
				$mid = $this->catalog->getMainIdCategory($id);			
				$this->db->update('catalog_categories', $category, $id);
				$this->db->update('main', array('title' => $category['title'], 'alias' => $this->all_controller->rus2translit(strip_tags($_POST['alias']))), $mid);
			} else {
				$mid = $this->db->insert('main', array('pid' => $cid, 'title' => $category['title'], 'alias' => $this->all_controller->rus2translit(strip_tags($_POST['alias']))));
				$category['mid'] = $mid;
				$id = $this->db->insert('catalog_categories', $category);
				$this->db->update('main', array('cid' => $id), $mid);
			}



			if(!empty($id)) {
				$this->db->update('catalog_categories', $category, $id);
			} else {
				$id = $this->db->insert('catalog_categories', $category);

			}
			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => '200',
				'title'     => $_POST['title'],
				'text'      => $_POST['text'],
			);
			$this->search->saveIndex($search);
			
			// -- сохраняем текст страницы
			$this->file->toFile($this->path.$id.'_category.txt', $_POST['text']);
			
			// -- характеризующая картинка
			if(isset($_FILES['main_img']['tmp_name']) && file_exists($_FILES['main_img']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['main_img']['name']);
				if(!empty($file) && is_uploaded_file($_FILES['main_img']['tmp_name'])) {
					$image = $this->image->analyze($_FILES['main_img']['tmp_name']);
					if(!empty($image)) {
						$img_b_w = (int)$this->config->get('category_w',   'images');
						$img_b_h = (int)$this->config->get('category_h',   'images');
						// $img_s_w = (int)$this->config->get('product_small_w', 'images');
						// $img_s_h = (int)$this->config->get('product_small_h', 'images');
						$this->image->resize($img_b_w, $img_b_h);
						$this->image->toFile($this->path.'catalog_category/b/'.$id.'.png');
						// $this->image->resize($img_s_w, $img_s_h);
						// $this->image->toFile($this->path.'catalog_category/s/'.$id.'.jpg');
						// $this->image->toFile($this->path.'b/'.$id.'.jpg', 80, $img_b_w, $img_b_h);
						// $this->image->toFile($this->path.'s/'.$id.'.jpg', 80, $img_s_w, $img_s_h);
					}
				}
			}
			
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/admin/catalog/category/'.$cid.'/'.$pid.'/'.$id.'/');
		}
		
		// -- обзор продукта
		public function product($cid, $pid, $id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $cid)) $this->role_controller->AccessError();
			
			$product = $this->catalog->getProduct($id);
			// -- заголовок по типу крошек
			$product['title_product'] = array();
			$act_id = $pid;
			while($act_id > 0) {
				$sql = 'SELECT id, pid, title FROM catalog_categories WHERE id = '.$act_id;
				$tmp = $this->db->get_row($sql);
				if(empty($tmp)) break;
				$product['title_product'][] =
					'<a href="/admin/catalog/category/'.$cid.'/'.$tmp['pid'].'/'.$tmp['id'].'/">'.$tmp['title'].'</a>';
				$act_id = $tmp['pid'];
			}
			$product['title_product'][] =
				'<a href="/admin/catalog/view/'.$cid.'/">'.
				$this->db->get_one('SELECT title FROM main WHERE id = '.$cid).'</a>';
			$product['title_product'] = join(' / ', array_reverse($product['title_product']));
			
			// -- список категорий
			$product['category_list'] = $this->catalog->getCategoryForSelect($cid, $pid);
			if(!empty($product['category_list'])) {
				foreach($product['category_list'] as $i => &$item) {
					$item['cat_title']  = str_repeat('--', $item['cat_deep']).' '.$item['cat_title'];
					$item['cat_select'] = $item['active'] ? 'selected="selected"' : '';
				}
			}
			
			if(empty($id)) {
				// -- если это для добавления
				$product['main_title'] = 'Новый продукт';
				$product['id']  = $id;
				$product['pid'] = $pid;
				$product['cid'] = $cid;

				
				
			} else {
				// -- если это для изменения
				$product['main_title'] = $product['title'];
				
				// -- подготовка данных для блока ТХ
				
				$tchars['list'] = array();
				
				// -- текст страницы
				$product['text'] = '';
				if(file_exists($this->path.'catalog_product/'.$id.'_product.txt')) {
					$product['text'] = file_get_contents($this->path.'catalog_product/'.$id.'_product.txt');
				}
				if (isset($product['text']))
					{
						$product['image']=$product['text'];
				
					
					$product['image']=preg_replace('#(src.{7}http://www.trios.ru/Pages/)#','src="/application/includes/pagesImg/',$product['text']);
					//preg_match_all('/(.{4}a href.{7}http://www.trios.ru/Pages/(.*)a)/', $page['text'], $buf);
					preg_match_all('/.{4}a.href.{10,30}trios(.*).lt.\/a.{4}/Uix', $product['text'], $buf);
				
					$product['timage']='';
		
						foreach($buf[0] as $ke=>$item)
						//var_dump($buf);
									$product['timage'].=$ke.')'.$item; 
						$product['text']=$product['image'];
						$product['image']='';
					}
				// -- характеризующая картинка
				if(file_exists($this->path.'b/'.$id.'.jpg')) {
					$product['character']  = 'просмотр: <a target="_blank" href="/application/includes/catalog/b/'.$id.'.jpg">бол.</a>';
				}
				if(file_exists($this->path.'s/'.$id.'.jpg')) {
					$product['character']  = (empty($product['character'])) ? 'просмотр: ' : $product['character'].' | ';
					$product['character'] .= '<a target="_blank" href="/application/includes/catalog/s/'.$id.'.jpg">мал.</a>';
				}
				
				// -- дополнительные модули
				$product['images_block']      = $this->addmodules_controller->generateImagesBlock($id, $this->module_id);
				$product['photos_block']      = $this->addmodules_controller->generatePhotosBlock($id, $this->module_id);
				$product['files_block']       = $this->addmodules_controller->generateFilesBlock ($id,  $this->module_id);
			
			}
			
			// -- системные модули
			$product['yandex_maps_block'] = $this->maps_controller->generateBlock($id, $this->module_id);
			$product['modules_block']     = $this->all_controller->generateModulesBlock($id, $this->module_id, true, false);
			if(!empty($product['type'])){
				$product['type'.$product['type']] = true;
			}			
			// if(count(explode(',',$product['fin_type'])) == 2){
			// 	$product['fin_type1'] = true;
			// 	$product['fin_type2'] = true;
			// } else if (count($product['fin_type']) == 1){
			// 	$product['fin_type'.$product['fin_type'][0]] = true;
			// }
			$product['tchars'] = $this->catalog->getTechChars($pid,$id);
			// var_dump($product['tchars']);
			// if ($product['taitle']=='') $product['taitle']=$product['title'];
			// -- основной рендер



			// switch($product['top']){
			// 	case 0:	$product['b0'] = true; break;
			// 	case 1: $product['b1'] = true; break;
			// 	case 2: $product['b2'] = true; break;
			// 	case 3: $product['b3'] = true; break;
			// 	case 4: $product['b4'] = true; break;
			// 	case 5: $product['b5'] = true; break;
			// }
			if(!empty($product['is_new']))
				$product['is_new']   = $product['is_new'] ? 'checked="checked"' : '';
			if(!empty($product['is_leader']))
				$product['is_leader']   = $product['is_leader'] ? 'checked="checked"' : '';
			if(!empty($product['is_popular']))
				$product['is_popular']   = $product['is_popular'] ? 'checked="checked"' : '';

			$product['alias'] = $this->catalog->getMainAlias($id, 2);

			// получаем список изображений в галереи из футера
			$product['minigallery'] = $this->minigallery->getAllImg($id);
			$product['id']  = $id;

			$this->html->render('catalog/product.html', $product, 'content_path');
		}
		
		public function techchar() {	
			$data['techchars'] = $this->catalog->get_techchars();
			$this->html->render('catalog/techchar.html', $data, 'content_path');
		}
		
		public function edit_techchar($id = false) {			
			$this->html->render('catalog/techchar_edit.html', NULL, 'content_path');
		}
		
		public function save_techchar($id = false) {
			$data = array(
				'name' => $_POST['techchar_name'],
				'title' => $_POST['techchar_title'],
				'left_part' => $_POST['left_part'],
				'right_part' => $_POST['right_part']				
			);
			$this->catalog->save_techchar($data);
			$this->url->redirect('/admin/catalog/techchar/');		
		}
		
		public function del_techchar($id) {			
			$this->catalog->del_techchar($id);
			die();
		}
		
		// -- сохраняем данных категории
		public function save_product($cid, $pid, $id) {
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $cid)) $this->role_controller->AccessError();
			
			$pid     = (int)$_POST['pid'];
			$pmid = $this->catalog->getParentMidProduct($pid);	
			// $price   = preg_replace('/[^0-9\.,]+/', '', $_POST['price']);
			// $price   = (float)str_replace(',', '.', $price);
			if(!empty($_POST['top'])){
				$sql  = 'UPDATE catalog SET top = 0 WHERE (top = '.(int)$_POST['top'].')';
		
				$this->db->query($sql);
			}
			$product = array(
				'pid'         => (int)$pid,
				'cid'         => (int)$cid,
				'title'       => trim($_POST['title']),
				'note'        => trim($_POST['note']),
				// 'price_hour'       => (float)$_POST['price_hour'],
				// 'price_turn'       => (float)$_POST['price_turn'],
				// 'geo'		=> (int)$_POST['geo'],
				// 'type'		=> (int)$_POST['type'],
				// 'author'		=> trim($_POST['author']),
				// 'phone'		=> trim($_POST['phone']),
				'fin_type' => !empty($_POST['fin_type']) ? join(',', $_POST['fin_type']) : 0,
				'taitle'    => trim($_POST['taitle']),
				'description' => trim($_POST['description']),
				'keywords'    => trim($_POST['keywords']),
				'print'       => (int)(!empty($_POST['print'])),
				'feedback'    => (int)(!empty($_POST['feedback'])),
				'sendfile'    => (int)(!empty($_POST['sendfile'])),
				'date'		  => time(),
				'active'    => (int)(!empty($_POST['active'])),
				'is_new' 	   => (int)$_POST['is_new']>0 ? 1 : 0,
				'is_leader' 	   => (int)$_POST['is_leader']>0 ? 1 : 0,
				'is_popular' 	   => (int)$_POST['is_popular']>0 ? 1 : 0,
				// 'top'		=>(int)$_POST['top'],
			);
			if(!empty($id)) {	
				$mid = $this->catalog->getMainIdProduct($id);			
				$this->db->update('catalog', $product, $id);
				$this->db->update('main', array('title' => $product['title'], 'alias' => $this->all_controller->rus2translit(strip_tags($_POST['alias']))), $mid);
			} else {
				$mid = $this->db->insert('main', array('pid' => $pmid, 'title' => $product['title'], 'alias' => $this->all_controller->rus2translit(strip_tags($_POST['alias']))));
				$product['mid'] = $mid;
				$id = $this->db->insert('catalog', $product);
				$this->db->update('main', array('cid' => $id), $mid);
			}

			// -- формирование данных для таблицы поиска
			$search = array(
				'pid'       => $id,
				'module_id' => '300',
				'title'     => $_POST['title'],
				'text'      => $_POST['text'],
			);
			$this->search->saveIndex($search);

			$text = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['text']);
			// $characters = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['characters']);
			// $descript = preg_replace('/(<li [^>]*>) [\s]* ([^><]+) /ix', '$1<span>$2</span>', $_POST['descript']);

			// -- сохранение контента
			$this->file->toFile($this->path.'catalog_product/'.$id.'_product.txt', $text);
			// $this->file->toFile($this->path.'characters/'.$id.'.txt', $characters);
			// $this->file->toFile($this->path.'descript/'.$id.'.txt', $descript);
			
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
			
			// -- сохраняем текст страницы
			// $this->file->toFile($this->path.$id.'_product.txt', $_POST['text']);
			
			// -- сохранение конфига
			// $config['site'] = array(
				// 'print_word'     => $_POST['print_word'],
				// 'feedback_email' => $_POST['feedback_email'],
			// );
			// $this->config_controller->add_config($config);

			// основное изображение (большая картинка)
			if(isset($_FILES['big_img']['tmp_name']) && file_exists($_FILES['big_img']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['big_img']['name']);
				if(!empty($file) && is_uploaded_file($_FILES['big_img']['tmp_name'])) {
					$image = $this->image->analyze($_FILES['big_img']['tmp_name']);
					if(!empty($image)) {
						$img_b_w = (int)$this->config->get('product_big_w',   'images');
						$img_b_h = (int)$this->config->get('product_big_h',   'images');
						$this->image->resize($img_b_w, $img_b_h);
						$this->image->toFile($this->path.'catalog_product/b/'.$id.'.png');

						$img_m_w = (int)$this->config->get('product_medium_w',   'images');
						$img_m_h = (int)$this->config->get('product_medium_h',   'images');
						$this->image->resize($img_m_w, $img_m_h);
						$this->image->toFile($this->path.'catalog_product/m/'.$id.'.png');

						$img_s_w = (int)$this->config->get('product_small_w',   'images');
						$img_s_h = (int)$this->config->get('product_small_h',   'images');
						$this->image->resize($img_s_w, $img_s_h);
						$this->image->toFile($this->path.'catalog_product/s/'.$id.'.png');
					}
				}
			}
			
			// // -- средняя картинка
			// if(isset($_FILES['medium_img']['tmp_name']) && file_exists($_FILES['medium_img']['tmp_name'])) {
			// 	$file = $this->file->getFileInfo($_FILES['medium_img']['name']);
			// 	if(!empty($file) && is_uploaded_file($_FILES['medium_img']['tmp_name'])) {
			// 		$image = $this->image->analyze($_FILES['medium_img']['tmp_name']);
			// 		if(!empty($image)) {
			// 			$img_b_w = (int)$this->config->get('product_medium_w',   'images');
			// 			$img_b_h = (int)$this->config->get('product_medium_h',   'images');
			// 			$this->image->resize($img_b_w, $img_b_h);
			// 			$this->image->toFile($this->path.'catalog_product/m/'.$id.'.png');
			// 		}
			// 	}
			// }

			// // маленькая картинка
			// if(isset($_FILES['small_img']['tmp_name']) && file_exists($_FILES['small_img']['tmp_name'])) {
			// 	$file = $this->file->getFileInfo($_FILES['small_img']['name']);
			// 	if(!empty($file) && is_uploaded_file($_FILES['small_img']['tmp_name'])) {
			// 		$image = $this->image->analyze($_FILES['small_img']['tmp_name']);
			// 		if(!empty($image)) {
			// 			$img_b_w = (int)$this->config->get('product_small_w',   'images');
			// 			$img_b_h = (int)$this->config->get('product_small_h',   'images');
			// 			$this->image->resize($img_b_w, $img_b_h);
			// 			$this->image->toFile($this->path.'catalog_product/s/'.$id.'.png');
			// 		}
			// 	}
			// }

			// загрузка изображений для мини-галереи
			if(!empty($_FILES)){
				$files_count = sizeof($_FILES['img']['name']);
				for ($i = 0; $i < $files_count-1; $i++) {
					$files = $this->file->getFileInfo($_FILES['img']['name'][$i]);
					if(!empty($files)){
						$tmp = array('filename' => $files['filename'],
									 'pid' => $id
							);
						$tmp['id'] = $this->db->insert('product_minigallery',$tmp);
						
						if(copy($_FILES['img']['tmp_name'][$i], INCLUDES.'catalog/catalog_product/d/b/'.$tmp['id'].'.png')) {
							$size = $this->image->analyze(INCLUDES.'catalog/catalog_product/d/b/'.$tmp['id'].'.png');
							if ($size['b_width'] > 318){
								$size['b_height'] = 318*$size['b_height']/$size['b_width'];
								$size['b_width'] = 318;
							}
							$size['b_width'] = ($size['b_width'] > 775) ? 775 : $size['b_width'];
							if(in_array($_POST['type'], array(1,2)))
								$this->image->ToFile(INCLUDES.'catalog/catalog_product/d/b/'.$tmp['id'].'.png', 80, $size['b_width'], $size['b_height']);
							else
							{
								$this->image->ToFile(INCLUDES.'catalog/catalog_product/d/b/'.$tmp['id'].'.png', 80, $size['b_width'], $size['b_height']);
							}
						}

						if(move_uploaded_file($_FILES['img']['tmp_name'][$i], INCLUDES.'catalog/catalog_product/d/s/'.$tmp['id'].'.png')) {
							$this->image->analyze(INCLUDES.'catalog/catalog_product/d/s/'.$tmp['id'].'.png');
							if(in_array($_POST['type'], array(1,2)))
								$this->image->ToFile(INCLUDES.'catalog/catalog_productd/d/s/'.$tmp['id'].'.png', 80, $this->config->get('img_width','minigallery_small'), $this->config->get('img_height','minigallery_small'));
							else
							{
								$this->image->ToFile(INCLUDES.'catalog/catalog_product/d/s/'.$tmp['id'].'.png', 80, $this->config->get('img_width','minigallery_small'), $this->config->get('img_height','minigallery_small'));
							}
						}
						unset($tmp);
					}
		    	}
			}

			$this->session->set('alert', ALERT_CHANGE_DATA);			
			$this->url->redirect('/admin/catalog/product/'.$cid.'/'.$pid.'/'.$id.'/');
		}

		// -- удаляем категорию со всеми подкатегориями
		public function delete_category($id) {
			$cid = $this->db->get_one('SELECT cid FROM catalog_categories WHERE id='.(int)$id);
			$mid = $this->catalog->getMainIdCategory($id);
			$this->db->delete('main', array('id' => $mid));
			$this->db->delete('search_index', array('pid' => $id, 'module_id' => 200));
			
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $cid)) $this->role_controller->AccessError();
			
			// -- запускаем рекурсивное удаление категории и продуктов
			$this->delete_category_rec($id);
			
			$this->url->redirect('::referer');
		}
		
		// -- удаляем продукт
		public function delete_product($id) {
			$cid = $this->db->get_one('SELECT cid FROM catalog WHERE id='.(int)$id);
			//Проверяем права
			if (!$this->role_controller->CheckAccess(2, $cid)) $this->role_controller->AccessError();
			
			$this->delete_product_once($id);
			
			$this->url->redirect('::referer');
		}
		
		// -- рекурсивное удаление категорий и продуктов
		private function delete_category_rec($id) {
			// -- удаляем подкатегории
			$list = $this->db->get_all_one('SELECT id FROM catalog_categories WHERE pid = '.(int)$id);
			if(!empty($list)) {
				foreach($list as $i => &$item) {
					$this->delete_category_rec($item['id']);
				}
			}
			// -- удаляем все товары в категории
			$list = $this->db->get_all_one('SELECT id FROM catalog WHERE pid = '.(int)$id);
			if(!empty($list)) {
				foreach($list as $i => &$item) {
					$this->delete_product_once($item['id']);
				}
			}
			// -- удаляем категорию
			$this->db->delete('catalog_categories', $id);
		}
		
		// -- удаляем продукт
		private function delete_product_once($id) {	
			// -- удаляем связанные данные
			// -- TODO: фото, видео, файлы
			$mid = $this->catalog->getMainIdProduct($id);
			$this->db->delete('main', array('id' => $mid));
			$this->db->delete('catalog', $id);
			$this->db->delete('search_index', array('pid' => $id, 'module_id' => 300));
			$this->trash_controller->delete_addition($id, $this->module_id);
		}
		public function update() {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			$trees = explode(',',$_POST['tree']);
			if(!empty($trees)) {
				foreach($trees as $key=>$val) {
					$tree['sort'] = $key;
					$this->db->update('catalog_categories_techchars_links',$tree,$val);
				}
			}
			
			die();
		}		

		public function del_img_ajax($id, $pid){
			unlink(INCLUDES.'catalog/catalog_product/d/b/'.$id.'.jpg');
			unlink(INCLUDES.'catalog/catalog_product/d/s/'.$id.'.jpg');
			$this->db->delete('product_minigallery', array(	'id'  => (int)$id,
															'pid' => (int)$pid));
			// получаем список изображений в галереи из футера
			$data['minigallery'] = $this->minigallery->getAllImg($pid);
			// var_dump($data);
			// die();

			echo $this->html->render('/layouts/uploaded_img.html', $data);
			die();
		}
	
	}
?>
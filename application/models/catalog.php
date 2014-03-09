<?php
	class catalog extends app_model {
	

		// проверка на корректность ссылки
		public function verify_url($url){
			$data = $this->db->get_row('SELECT cl.pid, m.alias as root_alias FROM catalog_links cl LEFT JOIN main m ON cl.cid = m.id WHERE cl.alias = '.'"'.$url.'"');
			$correct_url = '/'.$url;
			$this->verify_url_req($data['pid'], $correct_url);
			$tmp = explode('/', $correct_url.'/'.$data['root_alias'].'/');
			$tmp = array_reverse($tmp);
			$result = '/';
			if(!empty($result)){
				foreach ($tmp as $i => &$item) {
					if(!empty($item))
						$result .= $item.'/';
				}
			}	

			if(substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) != '/'){
				$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'].'/';
			}

			if ($result == $_SERVER['REQUEST_URI']){
				return true;
			}
			else {
				return false;
			}
		}
	
		public function verify_url_req($pid, &$url){
			$data = $this->db->get_row('SELECT pid, alias FROM catalog_links WHERE id = '.(int)$pid);
			$url .= '/'.$data['alias'];
			if ($data['pid'] != 0) {
				$this->verify_url_req($data['pid'], $url);
			}
		}

		// достаем название метода каталога
		public function getMethod($url){
			$data = $this->db->get_row('SELECT cat_id, prod_id FROM catalog_links WHERE alias = '.'"'.$url.'"');
			if(!empty($data['cat_id'])) {
				return 'category';
			}
			else {
				return 'product';
			}
		}



		// -- возвращаем список товаров для заданной категории и каталога
		public function getProductList($cid, $pid, $page = 0, $filter = false) {
			$where = '';
			if(!empty($filter)){
				$opt = array();
				if(!empty($filter['fin_type']) && (count($filter['fin_type'] == 1))) {
					$opt['fin_type'] = ' fin_type LIKE "%'.$filter['fin_type'][0].'%" ';
				}
				if(!empty($filter['type'])){
					$opt['type'] = ' type = '.$filter['type'].' ';
				}
				if(!empty($filter['geo'])){
					$opt['geo'] = ' geo = '.$filter['geo'].' ';
				}
				if(!empty($filter['price_from'])){
					$opt['price_turn_min'] = ' price_turn >'.(int)$filter['price_from'].' '; 
				}
				if(!empty($filter['price_to'])){
					$opt['price_turn_min'] = ' price_turn <'.(int)$filter['price_to'].' '; 
				}
				if(!empty($filter['price_from_hour'])){
					$opt['price_turn_min'] = ' price_hour >'.(int)$filter['price_from_hour'].' '; 
				}
				if(!empty($filter['price_to_hour'])){
					$opt['price_turn_min'] = ' price_hour <'.(int)$filter['price_to_hour'].' '; 
				}
				if(!empty($opt)){
					$where = join(' AND ',$opt);
				}
			}


			$where = ($pid != 0) ? ' cid = '.(int)$cid.' AND active =1 AND pid = '.(int)$pid.' '.(!empty($where) ? ' AND ' : '').$where : $where;
			if(!empty($where)) 
				$where = ' WHERE '.$where;
			$count = $this->config->get('product_count', 'site');
			$sql  = 'SELECT * FROM catalog '.
					$where.
					'ORDER BY  top ASC, id ASC '.
					'LIMIT '.($page*$count).','.$count;
			$data =  $this->db->get_all($sql);
			if(!empty($data)){
				foreach ($data as $key => &$item) {
					$item['techchars']=$this->getTechChars($item['pid'],$item['id']);
				}
			}
			
			return $data;
		}

		public function getPublicTitle($id){
			$sql = 'SELECT title FROM main WHERE id = '.(int)$id;
			$result['title'] = $this->db->get_one($sql);
			$result['url'] = $this->application_controller->get_url((int)$id);
			return $result;
		}

		// Сбор алиасов для постройки урла 
		public function getCatalogUrl($lid){
			$sql = 'SELECT cl.alias, cl.pid, m.alias as root_alias FROM catalog_links cl LEFT JOIN main m ON cl.cid = m.id WHERE cl.id = '.(int)$lid;
			return $this->db->get_row($sql);
		}

		public function getMidCategories($pid){
			$sql = 'SELECT mid FROM catalog_categories WHERE id = '.(int)$pid;
			return $this->db->get_one($sql);
		}

		// главные категории (Образование, строительство, спорт и экология)
		public function getMainCategory($cid){
			$sql = 'SELECT * FROM catalog_categories WHERE cid = '.(int)$cid.' AND pid = 0';
			return $this->db->get_all($sql);
		}

		// подкатегории (профессиональная переподготовка, повышения квалификации)
		public function getSubType($cid, $id){
			$sql = 'SELECT * FROM catalog_categories WHERE cid = '.(int)$cid.' AND pid = '.(int)$id;
			return $this->db->get_all($sql);
		}




		// -- данные о различных публикациях
		public function getPublicData($table, $pid, $limit) {
			$sql = 'SELECT id, title FROM '.$table.' WHERE pid = '.(int)$pid.' ORDER BY date DESC LIMIT '.$limit;
			$publication = $this->db->get_all($sql);
			$controller_name = $table.'_controller';
			if(!empty($publication)){
				foreach ($publication as $i => &$item) {
					$item['url'] = $this->$controller_name->get_url($item['id']);
				}
			}
			return $publication;
		}

		// -- данные о продуктах
		public function getSimpleProductList($cid, $pid, $page = 0) {
			$count = $this->config->get('product_count', 'site');
			$sql = 'SELECT * FROM catalog WHERE cid = '.(int)$cid.' AND pid = '.(int)$pid.' ORDER BY id ASC LIMIT '.($page*$count).','.$count;
			return $this->db->get_all($sql);
		}

		public function getPageIdAlias($alias, $type) {
			switch ($type) {
				case 0:
					$id = 'cat_id';
					break;
				case 1:
					$id = 'prod_id';
					break;
			}
			$sql = 'SELECT '.$id.' FROM catalog_links WHERE alias = "'.$alias.'"';
			return $this->db->get_one($sql);
		}

		public function getMainIdProduct($id) {
			$sql = 'SELECT id FROM main WHERE cid = '.(int)$id;
			return $this->db->get_one($sql);
		}
		
		// -- возвращаем список товаров для заданной категории и каталога
		public function getProductCount($cid, $pid) {
			$sql  = 'SELECT COUNT(id) FROM catalog '.
					'WHERE cid = '.(int)$cid.' AND pid = '.(int)$pid;
			return $this->db->get_one($sql);
		}
		
		public function getCategoryFirstName($cid, $id) {
			$sql  = 'SELECT first_name_column FROM catalog_categories '.
					'WHERE cid = '.(int)$cid.' AND id = '.(int)$id.' ';
			
			return $this->db->get_one($sql);
		}
		// -- возвращаем список подкатегорий для заданной категории и каталога
		public function getCategoryList($cid, $pid, $limit = 0) {
			$sql  = 'SELECT id, lid, title, note FROM catalog_categories '.
					'WHERE cid = '.(int)$cid.' AND pid = '.(int)$pid.' '.
					'ORDER BY id';
			if(!empty($limit)) {
				$sql .= ' LIMIT '.$limit;
			}
			return $this->db->get_all($sql);
		}
		
		// -- данные о продукте
		public function getProduct($id) {
			$sql = 'SELECT * FROM catalog WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		// -- данные о продуктах
		public function getMainProductList($cid) {
			$sql = 'SELECT * FROM catalog WHERE cid = '.(int)$cid;
			return $this->db->get_row($sql);
		}
		
		// -- данные о продуктах категории
		public function getCategoryProduct($cid, $pid) {
			$sql = 'SELECT c.* FROM catalog_categories cc LEFT JOIN catalog c ON cc.id = c.pid WHERE cc.cid = '.(int)$cid.' AND cc.pid = '.(int)$pid;
			return $this->db->get_all($sql);
		}
		// -- данные о количестве продуктов категории
		public function getCountCategoryProduct($cid, $pid) {
			$sql = 'SELECT COUNT(c.id) FROM catalog_categories cc LEFT JOIN catalog c ON cc.id = c.pid WHERE cc.cid = '.(int)$cid.' AND cc.pid = '.(int)$pid;
			return $this->db->get_one($sql);
		}
		
		// -- данные о самых популярных продуктах
		public function getMostPopularList($cid, $limit) {
			$sql = 'SELECT * FROM catalog WHERE is_popular = 1 AND is_new != 1 AND is_leader != 1 AND cid = '.(int)$cid.' ORDER BY rand() LIMIT '.(int)$limit;
			return $this->db->get_all($sql);
		}

		// -- данные о новых продуктах
		public function getNewestList($cid) {
			$sql = 'SELECT * FROM catalog WHERE is_new = 1 AND is_leader != 1 AND cid = '.(int)$cid.' ORDER BY rand() LIMIT 1';
			return $this->db->get_all($sql);
		}

		// -- данные о новых продуктах
		public function getLeadList($cid) {
			$sql = 'SELECT * FROM catalog WHERE is_leader = 1 AND is_new != 1 AND cid = '.(int)$cid.' ORDER BY rand() LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		// -- название по айди
		public function getTitleProduct($id) {
			$sql = 'SELECT title FROM catalog WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}
		
		// -- данные о подкатегории
		public function getCategory($id) {
			$sql = 'SELECT * FROM catalog_categories WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		// -- возвращаем цену в нужном формате
		public function getPrice($price) {
			return number_format($price, 0, '.', ' ');
		}
		
		// -- список технических характеристик для продукта
		// public function getTechChars($id) {
			// $sql  = 'SELECT * FROM catalog_techchars '.
					// 'WHERE pid = '.(int)$id.' ORDER BY name';
			// return $this->db->get_all($sql);
		// }
		
		public function getTechChars($pid,$id = false) {
			if(!empty($pid)){
				$tchars = $this->db->get_all('SELECT ct.id as techchar_id, ct.title as techchar_title, ct.right_part, ct.left_part FROM catalog_categories_techchars_links cctl, catalog_techchars ct WHERE (cctl.techchar_id = ct.id) and (cctl.category_id = '.$pid.') ORDER BY cctl.sort');			
				if((!empty($id)) && (!empty($tchars))){
					foreach($tchars as &$item){	
					if ($item['techchar_id']) 
						$item['techchar_value'] = $this->db->get_one('SELECT ctl.value FROM catalog_techchars_links ctl WHERE ctl.catalog_id = '.$id.' AND cat_cat_techchars_id = '.$item['techchar_id']);
						else $item['techchar_value']="<span style='color:red'>Неизвестно</span>";
					}
				}
				return $tchars;
			}
		}
		
		// -- список заказов
		public function getOrders($user_id, $count, $page = 0) {
			$sql  = 'SELECT * FROM order_history '.
					'WHERE user_id = '.(int)$user_id.' '.
					'ORDER BY time DESC LIMIT '.($page * $count).', '.$count;
			return $this->db->get_all($sql);	
		}
		
		// -- список заказов
		public function getOrdersCount($user_id) {
			$sql  = 'SELECT COUNT(id) FROM order_history '.
					'WHERE user_id = '.(int)$user_id;
			return $this->db->get_one($sql);	
		}
		
		// -- конкретный заказ
		public function getOrder($user_id, $id) {
			$sql  = 'SELECT OHP.*, OH.total_sum, OH.id order_id '.
					'FROM order_history OH LEFT JOIN order_history_prod OHP ON OH.id = OHP.pid '.
					'WHERE OH.user_id = '.(int)$user_id.' AND OH.id = '.(int)$id;
			return $this->db->get_all($sql);
		}
		
	}
?>
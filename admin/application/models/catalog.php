<?php
	class catalog extends app_model {

		// -- возвращаем элемент категории каталога
		public function getCategory($id) {
			$sql =  'SELECT * FROM catalog_categories '.
					'WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getCategoryList($cid, $pid) {
			$sql  = 'SELECT * FROM catalog_categories '.
					'WHERE cid = '.(int)$cid.' AND pid = '.(int)$pid.' '.
					'ORDER BY title';
			return $this->db->get_all($sql);
		}
				public function getCategoryFirstName($cid, $id) {
			$sql  = 'SELECT first_name_column FROM catalog_categories '.
					'WHERE cid = '.(int)$cid.' AND id = '.(int)$id.' ';
			
			return $this->db->get_one($sql);
		}
		public function getProduct($id) {
			$sql =  'SELECT * FROM catalog '.
					'WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}

		public function getMainIdCategory($id) {
			$sql =  'SELECT mid FROM catalog_categories '.
					'WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}


		public function getMainAlias($id) {
			$sql =  'SELECT alias FROM main '.
					'WHERE cid = '.(int)$id;
			return $this->db->get_one($sql);
		}

		public function getMainIdProduct($id) {
			$sql =  'SELECT mid FROM catalog '.
					'WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}

		public function getParentMidProduct($pid) {
			$sql =  'SELECT id FROM main '.
					'WHERE cid = '.(int)$pid;
			return $this->db->get_one($sql);
		}

		public function getAliasIdProduct($id) {
			$sql =  'SELECT alias FROM main '.
					'WHERE cid = '.(int)$id;
			return $this->db->get_one($sql);
		}
		
		public function getProductList($cid, $pid) {
			$sql  = 'SELECT * FROM catalog '.
					'WHERE cid = '.(int)$cid.' AND pid = '.(int)$pid.' '.
					'ORDER BY title';
			return $this->db->get_all($sql);
		}
		
		public function getTechChars($pid,$id) {
			if(!empty($pid)){
				$tchars = $this->db->get_all('SELECT ct.id as techchar_id, ct.title as techchar_title, ct.right_part, ct.left_part FROM catalog_categories_techchars_links cctl, catalog_techchars ct WHERE (cctl.techchar_id = ct.id) and (cctl.category_id = '.$pid.') ORDER BY cctl.sort');			
			}
			if((!empty($id)) && (!empty($tchars))){
				foreach($tchars as &$item){
					$item['techchar_value'] = $this->db->get_one('SELECT ctl.value FROM catalog_techchars_links ctl WHERE ctl.catalog_id = '.$id.' AND cat_cat_techchars_id = '.$item['techchar_id']);
				}
			}
			return $tchars;
		}
		
		public function getCategoryForSelect($cid, $pid = 0, &$list = false, $deep = 0, $act = 0) {
			// -- защита от зацикливания
			if($deep > 20) return false;
			// -- внешнее вхождение
			if($list === false) {
				$list = array();
				$this->getCategoryForSelect($cid, 0, $list, 0, $pid);
				return $list;
			}
			// -- внутреннее вхождение
			$sql  = 'SELECT id cat_id, title cat_title FROM catalog_categories '.
					'WHERE cid = '.(int)$cid.' AND pid = '.(int)$pid;
			$data = $this->db->get_all($sql);
			if(empty($data)) return false;
			foreach($data as $i => &$item) {
				$item['cat_deep'] = $deep;
				$item['active']   = ($item['cat_id'] == $act);
				$list[]           = $item;
				// -- вхождение в глубину
				$this->getCategoryForSelect($cid, $item['cat_id'], $list, $deep + 1, $act);
			}
		}
		
		public function save_techchar($data){
			if(!empty($data['id'])){
				$id = $data['id'];
				unset($data['id']);
				$this->db->update('catalog_techchars',$data,$id);
			}
			else{
				$id = $this->db->insert('catalog_techchars',$data);
			}
			return $id;
		}		
		
		public function get_techchars(){
			return $this->db->get_all('SELECT * FROM catalog_techchars order by name');
		}
		
		public function get_category_techchar($category){
			return $this->db->get_all('SELECT * FROM catalog_techchars ct LEFT JOIN catalog_categories_techchars_links cctl ON cctl.techchar_id = ct.id WHERE cctl.category_id = '.$category.' order by sort');
		}
		
		public function get_catalog_techchar($catalog){
			return $this->db->get_all('SELECT * FROM catalog_techchars ct LEFT JOIN catalog_categories_techchars_links cctl ON cctl.techchar_id = ct.id WHERE cctl.catalog_id = '.$catalog.' order by sort');
		}
		
		public function add_catalog_techchar($data){
			$this->db->insert('catalog_categories_techchars_links',$data);
		}
		
		public function del_catalog_techchar($id){
			$this->db->delete('catalog_categories_techchars_links',$id);
		}
		
		public function del_techchar($id){
			return $this->db->delete('catalog_techchars',$id);
		}
	}
?>
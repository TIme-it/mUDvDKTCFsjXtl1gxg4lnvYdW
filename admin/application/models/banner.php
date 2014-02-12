<?php
	class banner extends app_model {
		
		//Список всех баннеров (из указанного множества access, если он задан)
		public function getBanners() {
			$sql = 'SELECT * FROM banners ';
			$res = $this->db->get_all($sql);
			return empty($res) ? array() : $res;
		}
		
		//Выборка кол-ва категорий
		public function getCountCategories() {
			$sql = 'SELECT COUNT(*) FROM banners_categories';
			return (int)$this->db->get_one($sql);
		}
		
		//Выборка баннера по id
		public function getOneBanner($id) {
			$sql = 'SELECT id, title, category_id, link, extension, DATE_FORMAT(date_begin,"%Y-%m-%d") as date_begin,DATE_FORMAT(date_end,"%Y-%m-%d") as date_end, active  
					FROM banners WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		//Выборка категорий
		public function getCategories() {
			$sql = 'SELECT id category_id, title category_title, width w, height h
					FROM banners_categories 
					WHERE active != 0';
			return $this->db->get_all($sql);
		}
		
		# С фиксом, потому что category_id перекрывается!
		public function getCategories_fix() {
			$sql = 'SELECT id category_id_fix, title category_title, width w, height h
					FROM banners_categories 
					WHERE active != 0';
			return $this->db->get_all($sql);
		}
		
		public function getOneCategory($id) {
			$sql = 'SELECT * FROM banners_categories WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getProps($type) {
			$sql = 'SELECT width,height 
					FROM banners_categories WHERE id='.$this->db->escape($type);
			return $this->db->get_row($sql);
		}
		
		public function getRandomView($c_id) {
			$sql = 'SELECT MAX(view_random) as view_random 
					FROM banners 
					WHERE category_id = '.(int)$c_id;
			return (int)$this->db->get_one($sql);
		}
		
		public function getExtension($id) {
			$sql = 'SELECT extension FROM banners WHERE id='.$this->db->escape($id);
			return $this->db->get_one($sql);
		}
	}
?>
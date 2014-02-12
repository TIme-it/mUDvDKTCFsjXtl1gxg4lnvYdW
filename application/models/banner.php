<?php
	class banner extends application_controller {
		
		public function __construct() {}
		
		public function getBanner($category_id) {
			$date = date('Y-m-d H:i:s');
			$sql = 'SELECT b.id, b.link, b.extension, c.width, c.height
					FROM banners b LEFT JOIN banners_categories c ON b.category_id = c.id 
					WHERE ((b.date_begin <= "'.$date.'" OR b.date_begin = "0000-00-00 00:00:00") AND (b.date_end >= "'.$date.'" OR b.date_end = "0000-00-00 00:00:00")) AND 
						b.category_id = '.(int)$category_id.' AND b.active != 0 
					ORDER BY RAND() 
					LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		public function getBannerCenter($category_id, $center_id = 0) {
			$date = date('Y-m-d H:i:s');
			$sql = 'SELECT b.id, b.link, b.extension, c.width, c.height
					FROM banners b LEFT JOIN banners_categories c ON b.category_id = c.id 
					WHERE ((b.date_begin <= "'.$date.'" OR b.date_begin = "0000-00-00 00:00:00") AND (b.date_end >= "'.$date.'" OR b.date_end = "0000-00-00 00:00:00")) AND 
						b.category_id = '.(int)$category_id.' AND b.active != 0 '.
					'AND center_id ='.(int)$center_id.' '.	
					'ORDER BY RAND() 
					LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		/*public function getOneBanner($id) {
			$sql = 'SELECT b.id,b.title,b.link,b.extension,c.width,c.height 
					FROM banners b 
					LEFT JOIN banners_categories c 
					ON b.category_id = c.id 
					WHERE b.id='.$this->db->escape($id);
			return $this->db->get_row($sql);
		}
		
		public function getLink($id) {
			$sql = 'SELECT link FROM banners WHERE id='.$this->db->escape($id);
			return $this->db->get_one($sql);
		}*/
	}
?>
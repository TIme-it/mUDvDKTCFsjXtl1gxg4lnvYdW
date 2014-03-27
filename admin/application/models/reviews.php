<?php
	class reviews extends app_model {
		
		public function getReviews($pid) {
			$sql = 'SELECT * FROM reviews WHERE pid = '.(int)$pid.' ORDER BY dateQuestion DESC';
			return $this->db->get_all($sql);
		}
		
		public function getOne($id) {
			$sql = 'SELECT * FROM reviews WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getPid($id) {
			return $this->db->get_one('SELECT id FROM reviews WHERE id = "'.(int)$id.'"');
		}

		// -- ïîëó÷èòü èíôó î ðàçäåëå íîâîñòåé
		public function aboutReviewsCategory($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
	}
?>
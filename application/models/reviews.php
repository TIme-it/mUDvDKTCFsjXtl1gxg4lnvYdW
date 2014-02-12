<?php
	class reviews extends app_model {
		
		public function getReview($id) {
			$sql  = 'SELECT *, IF(dateAnswer IS NULL, dateQuestion, dateAnswer) sortDate '.
					'FROM reviews WHERE id = '.(int)$id.' AND active = 1 LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		public function getReviewsCount($pid, $is_active_only) {
			$sql  = 'SELECT COUNT(*) '.
					'FROM reviews WHERE pid = '.(int)$pid.($is_active_only ? ' AND active = 1' : '');
			return $this->db->get_one($sql);
		}
		
		public function getReviews($pid, $page, $count, $is_active_only) {
			$sql  = 'SELECT *, IF(dateAnswer IS NULL, dateQuestion, dateAnswer) sortDate '.
					'FROM reviews WHERE pid = '.(int)$pid.($is_active_only ? ' AND active = 1' : '').' ORDER BY dateQuestion DESC LIMIT '.($page * $count).', '.$count;
			return $this->db->get_all($sql);
		}
		
		public function getLast($pid = false) {
			$sql  = 'SELECT *, IF(dateAnswer IS NULL, dateQuestion, dateAnswer) sortDate '.
					'FROM reviews WHERE '.($pid ? 'pid = '.(int)$pid.' AND ' : '').'active = 1 ORDER BY dateQuestion DESC LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		public function getMainInfo($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
	}
?>
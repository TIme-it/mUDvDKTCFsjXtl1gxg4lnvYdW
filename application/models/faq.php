<?php
	class faq extends app_model {
		
		public function getFaq($id) {
			$sql  = 'SELECT *, IF(dateAnswer IS NULL, dateQuestion, dateAnswer) sortDate '.
					'FROM faq WHERE id = '.(int)$id.' AND active = 1 LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		public function getFaqsCount($pid, $is_active_only, $user_id = false) {
			$sql  = 'SELECT COUNT(*) '.
					'FROM faq WHERE pid = '.(int)$pid.($is_active_only ? ' AND active = 1' : '').' '.(!empty($user_id) ? ' AND user_id = '.$user_id.' ' : '');
			return $this->db->get_one($sql);
		}
		
		public function getFaqs($pid, $page, $count, $is_active_only, $user_id = false) {
			$sql  = 'SELECT *, IF(dateAnswer IS NULL, dateQuestion, dateAnswer) sortDate '.
					'FROM faq WHERE pid = '.(int)$pid.($is_active_only ? ' AND active = 1' : '').' '.(!empty($user_id) ? ' AND user_id = '.$user_id.' ' : '').' ORDER BY dateQuestion DESC LIMIT '.($page * $count).', '.$count;
			
			return $this->db->get_all($sql);
		}
		
		public function getLast($pid = false) {
			$sql  = 'SELECT *, IF(dateAnswer IS NULL, dateQuestion, dateAnswer) sortDate '.
					'FROM faq WHERE '.($pid ? 'pid = '.(int)$pid.' AND ' : '').'active = 1 ORDER BY dateQuestion DESC LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		public function getMainInfo($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
	}
?>
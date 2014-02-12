<?php
	class faq extends app_model {
		
		public function getFaqs($pid) {
			$sql = 'SELECT * FROM faq WHERE pid = '.(int)$pid.' ORDER BY dateQuestion DESC';
			return $this->db->get_all($sql);
		}
		
		public function getOne($id) {
			$sql = 'SELECT * FROM faq WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getPid($id) {
			return $this->db->get_one('SELECT id FROM faq WHERE id = "'.(int)$id.'"');
		}
	}
?>
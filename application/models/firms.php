<?php
	class firms extends app_model {

		public function getInfo($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getList($pid) {
			$sql = 'SELECT * FROM firms WHERE pid = '.(int)$pid;
			return $this->db->get_all($sql);
		}
		
		public function getItem($id) {
			$sql = 'SELECT * FROM firms WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
	}
?>
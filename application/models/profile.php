<?php
	class profile extends app_model {
		
		public function getUser($id) {
			$sql = 'SELECT * FROM users WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
	}
?>
<?php
	class firms extends app_model {
		
		public function getPage($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getItem($id) {
			$sql = 'SELECT * FROM firms WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getList($pid) {
			$sql = 'SELECT * FROM firms WHERE pid = '.(int)$pid;
			return $this->db->get_all($sql);
		}
		
		public function getCount($pid) {
			$sql = 'SELECT COUNT(*) FROM firms WHERE pid = '.(int)$pid;
			return $this->db->get_one($sql);
		}
		
		public function getListPart($pid, $page = 1, $count = 5) {
			$sql = 'SELECT * FROM firms WHERE pid = '.(int)$pid.' 
					LIMIT '.(($page-1)*$count).', '.$count;
			return $this->db->get_all($sql);
		}
	}
?>
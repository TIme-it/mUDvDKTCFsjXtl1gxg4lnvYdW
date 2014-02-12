<?php
	class page extends app_model {
		
		public function getPage($id) {
			$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getFile($id) {
			$sql = 'SELECT extension FROM files WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getPlacemarks($pid) {
			$sql  = 'SELECT id pl_id, title pl_title, note pl_note, latitude pl_latitude, longitude pl_longitude '.
					'FROM map_placemarks '.
					'WHERE pid = '.(int)$pid;
			return $this->db->get_all($sql);
		}
		
		public function getPlacemarkInfo($id) {
			$sql  = 'SELECT id pl_id, title pl_title, note pl_note, latitude pl_latitude, longitude pl_longitude '.
					'FROM map_placemarks '.
					'WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
	}
?>
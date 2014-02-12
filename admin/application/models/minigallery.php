<?php
	class minigallery extends app_model {
		
		// изъятие всех изображений в футере
		public function getAllImg($pid) {
			$sql = 'SELECT id, pid FROM product_minigallery WHERE pid = '.(int)$pid;
			return $this->db->get_all($sql);
		}
	}
?>
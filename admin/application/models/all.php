<?php
	class all extends app_model {
		
		public function getMaps($pid, $mid) {
			$sql = 'SELECT * FROM maps WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid.' ORDER BY title';
			return $this->db->get_all($sql);
		}
		
		public function getMapsCount($pid, $mid) {
			$sql = 'SELECT COUNT(*) FROM maps WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			return (int)$this->db->get_one($sql);
		}
		
		public function getPlacemarks($map_id) {
			if(is_array($map_id)) {
				$sql = 'SELECT * FROM maps_placemarks WHERE pid IN ('.join(', ', $map_id).') ORDER BY pid, id';
			} else {
				$sql = 'SELECT * FROM maps_placemarks WHERE pid = '.(int)$map_id.' ORDER BY id';
			}
			return $this->db->get_all($sql);
		}
		
		public function getPlacemark($id) {
			$sql = 'SELECT * FROM maps_placemarks WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function isValidPlacemark($data) {
			return (bool)(!$this->db->get_one('SELECT 1 FROM maps_placemarks WHERE 
				pid       = '.$data['pid'].' AND 
				latitude  = '.$data['latitude'].' AND 
				longitude = '.$data['longitude']));
		}
		
		public function getModulesBlockInfo($pid, $mid) {
			$is_main = (bool)(!$mid || $mid == 1);
			$table   = $is_main ? 'main' : $this->db->get_one('SELECT name FROM module WHERE id = '.(int)$mid);
			if(empty($table)) return false;
			
			$fields = 'print, feedback, sendfile';

			if($is_main) $fields .= ', subsection';
			
			$sql = 'SELECT '.$fields.' FROM `'.$table.'` WHERE id = '.(int)$pid;
			return $this->db->get_row($sql);
		}
		
		// -- получить количество элементов в таблице дополнительных модулей
		public function getCountItems($pid, $mid, $table, $where = '') {
			$sql = 'SELECT COUNT(*) FROM '.$table.' WHERE '.($where?$where.' AND ':'').'pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			return $this->db->get_one($sql);
		}
		public function getFeedBackTypes(){
			$sql = 'SELECT * FROM feedback WHERE 1';
			return $this->db->get_all($sql);
		}
		
	}
?>
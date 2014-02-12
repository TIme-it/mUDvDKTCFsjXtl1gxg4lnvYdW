<?php
	class search extends app_model {
		
		// -- проверка на наличии записи в таблице поиска
		public function issetIndex($pid, $module_id) {
			$sql = 'SELECT id FROM search_index WHERE pid = '.(int)$pid.' AND module_id = '.(int)$module_id;
			return (int)$this->db->get_one($sql);
		}
		
		public function saveIndex($data) {
			if(empty($data['text'])) return false;
			$data['text'] =  strip_tags($data['text']);
			if($id = $this->issetIndex($data['pid'], $data['module_id'])) {
				return $this->db->update('search_index', $data, $id);
			}
			return $this->db->insert('search_index', $data);
		}
		
	}
?>
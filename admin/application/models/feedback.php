<?php
	class feedback extends app_model {

		public function getOnePattern($id){
			$sql = 'SELECT * FROM feedback WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		public function getAllPatterns(){
			$sql = 'SELECT * FROM feedback WHERE 1';
			return $this->db->get_all($sql);
		}
		public function getFields($pid){
			$sql = 'SELECT * FROM feedback_fields WHERE pid= '.(int)$pid.' ORDER BY position DESC';
			return $this->db->get_all($sql);
		}
		public function getMiniMaxPosItem($pos,$pid){
			$sql = 'SELECT * FROM feedback_fields WHERE pid = '.(int)$pid.' AND position >  '.(int)$pos.' ORDER BY position ASC LIMIT 1';
			return $this->db->get_row($sql);
		}
		public function getMaxiMinPosItem($pos,$pid){
			$sql = 'SELECT * FROM feedback_fields WHERE pid = '.(int)$pid.' AND position <  '.(int)$pos.' ORDER BY position DESC LIMIT 1';
			return $this->db->get_row($sql);
		}
		public function getMaxPosition(){
			$sql = 'SELECT MAX(position) FROM feedback_fields WHERE 1';
			return $this->db->get_one($sql);
		}
		public function getOneField($id){
			$sql = 'SELECT * FROM feedback_fields WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
	}
?>
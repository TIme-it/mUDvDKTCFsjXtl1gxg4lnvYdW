<?php
	// -- модель для модуля "Голосование"
	
	class question extends app_model {
		
		public function getItem($id) {
			$sql = 'SELECT * FROM question WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getList($is_visible = true) {
			$where = 'active = 1 AND date_begin <= NOW() AND date_end >= NOW()';
			if(!$is_visible) $where = 'NOT('.$where.')';
			$sql = 'SELECT * FROM question WHERE '.$where.' ORDER BY date_begin DESC';
			return $this->db->get_all($sql);
		}
		
		public function getAnswerList($pid) {
			$sql = 'SELECT id answer_id, answer, count '.
				   'FROM question_answer '.
				   'WHERE pid = '.(int)$pid.' ORDER BY sort ASC';
				   
			return $this->db->get_all($sql);
		}
		
		public function issetAnswer($id) {
			$sql = 'SELECT 1 FROM question_answer WHERE id = '.(int)$id;
			return (bool)$this->db->get_one($sql);
		}
		
		public function getNextIdAnswer() {
			$sql = 'SELECT MAX(id)+1 FROM question_answer';
			return (int)$this->db->get_one($sql);
		}

	}
?>
<?php
	class question extends app_model {
		
		// -- формируем опрос для текущего IP
		public function getQuestion($ip, $time) {
			// -- проверка на уже отвеченные вопросы для текущего IP
			$ip = $this->getBlockQuestionByIP($ip, $time);
			$ip_where = '';
			if(!empty($ip)) {
				foreach($ip as $i => $id) {
					$ip_where .= 'AND id != '.(int)$id.' ';
				}
			}
			// -- из возможных опросов выбираем один
			$sql = 'SELECT id pid, question FROM question '.
				   'WHERE active = 1 AND date_begin <= NOW() AND date_end >= NOW() '.$ip_where.
				   'ORDER BY RAND() LIMIT 1';
			$data = $this->db->get_row($sql);
			if(empty($data)) {
				return false;
			}
			// -- присобачиваем к выбранному опросы варианты ответов
			$sql = 'SELECT id, answer FROM question_answer '.
				   'WHERE pid = '.(int)$data['pid'].' '.
				   'ORDER BY sort';
			$data['answer_list'] = $this->db->get_all($sql);
			return $data;
		}
		
		// -- формируем результаты последнего опроса
		public function getResultLastQuestion() {
			$sql = 'SELECT id pid, question FROM question '.
				   'WHERE active = 1 '.
				   'ORDER BY date_end DESC LIMIT 1';
			$data = $this->db->get_row($sql);
			if(empty($data)) {
				return false;
			}
			$sum = $this->db->get_one('SELECT SUM(count) FROM question_answer WHERE pid = '.(int)$data['pid']);
			$sql = 'SELECT id, answer, count, IF(count > 0 AND '.$sum.' > 0, count/'.$sum.'*100, 0) prc FROM question_answer '.
				   'WHERE pid = '.(int)$data['pid'].' '.
				   'ORDER BY sort';
			$data['answer_list'] = $this->db->get_all($sql);
			$data['sum'] = $sum;
			return $data;
		}
		
		// -- список опросов
		public function getQuestionList($page, $count) {
			$sql  = 'SELECT id question_id, question, date_begin, date_end '.
					'FROM question WHERE active = 1 ORDER BY date_end DESC LIMIT '.(int)(($page-1)*$count).', '.(int)$count;
			$list = $this->db->get_all($sql);
			if(empty($list)) return false;
			foreach($list as $i => &$item) {
				$sum  = $this->db->get_one('SELECT SUM(count) FROM question_answer WHERE pid = '.(int)$item['question_id']);
				$sql  = 'SELECT answer, count, IF(count > 0 AND '.$sum.' > 0, ROUND(count/'.$sum.'*100), 0) prc '.
						'FROM question_answer '.
						'WHERE pid = '.(int)$item['question_id'].' '.
						'ORDER BY count DESC, answer ASC';
				$item['answer_list'] = $this->db->get_all($sql);
				$item['sum'] = $sum;
			}
			return $list;
		}
		
		public function issetAnswer($id, $pid) {
			$sql = 'SELECT 1 FROM question_answer WHERE id = '.(int)$id.' AND pid = '.(int)$pid;
			return (bool)$this->db->get_one($sql);
		}

		public function getCourses($cid) {
			$sql = 'SELECT id, title FROM catalog_categories WHERE cid = '.(int)$cid.' AND pid = 0';
			return $this->db->get_all($sql);
		}
		
		public function blockIP($user_ip, $time, $pid) {
			$sql = 'SELECT 1 FROM question_ip '.
				   'WHERE ip = '.$this->db->escape($user_ip).' AND '.
				   '	  NOW() - time <= '.(int)$time.' AND '.
				   '	  pid = '.(int)$pid;
			return (bool)$this->db->get_one($sql);
		}
		
		public function addVoice($id) {
			$sql = 'UPDATE question_answer SET count = count + 1 WHERE id = '.(int)$id;
			$this->db->query($sql);
		}
		
		private function getBlockQuestionByIP($ip, $time) {
			$sql = 'SELECT pid FROM question_ip WHERE ip = '.$this->db->escape($ip).' AND NOW() - time <= '.(int)$time;
			return $this->db->get_all_one($sql);
		}
		
		// -- постраничная навигация
		public function getForPages($page, $count) {
			$data = array();
			$all_count  = $this->db->get_one('SELECT COUNT(*) FROM question');
			$page_count = (int)ceil($all_count / $count);
			if(empty($page_count) || $page_count < 2) {
				return false;
			}
			for($i = 1; $i <= $page_count; $i++) {
				$data['list'][] = array(
					'page'   => $i,
					'url'    => '/question/'.(($i > 1) ? $i.'/' : ''),
					'active' => ($i == $page) ? 'class="active"' : ''
				);
			}
			if($page > 1) {
				$data['left'] = '<a id="nav_left" href="/question/'.(($page-1 > 1) ? ($page-1).'/' : '').'"><span>&#9668;</span></a>';
			}
			if($page < $page_count) {
				$data['right'] = '<a id="nav_right" href="/question/'.($page+1).'/"><span>&#9658;</span></a>';
			}
			return $data;
		}
		
	}
?>
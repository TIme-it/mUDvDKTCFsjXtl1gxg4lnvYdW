<?php
	class mailer extends app_model {

		public function getTasks(){
			$sql = 'SELECT * FROM mail_task WHERE 1';
			return $this->db->get_all($sql);
		}
		public function getLettersByTask($id){
			$sql = 'SELECT * FROM letters WHERE task_id = '.(int)$id;
			return $this->db->get_all($sql);
		}
		public function getGroups()
		{
			$sql = 'SELECT * FROM mail_groups WHERE 1';
			return $this->db->get_all($sql);
		}
		public function getGroupTitle($id){
			 $sql = 'SELECT title FROM mail_groups WHERE id = '.(int)$id;
			
			 return $this->db->get_one($sql);
		}
		public function getGroupInfo($id){
			$sql = 'SELECT u.*, umg.email AS zero_mail FROM mail_groups mg LEFT JOIN users_mail_group umg ON mg.id=umg.mg_id LEFT JOIN users u ON u.id = umg.uid WHERE mg.id = '.(int)$id;
			return $this->db->get_all($sql);
		}
		public function getUserMail($id){
			$sql = 'SELECT email FROM users WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}
		public function getUsersResults($cond, $page, $count) {
			$phrase = $cond['str'];
			unset($cond['str']);
			if(empty($cond['country'])) unset($cond['country']);
			if(empty($cond['region'])) unset($cond['region']);
			if(empty($cond['city'])) unset($cond['city']);
			if(empty($cond['sex'])) unset($cond['sex']);
			if(!empty($cond['age_from'])){
				$age_from = ' AND bday <= '.(strtotime(date('d.m.Y',time()).' - '.$cond['age_from'].' years')).' ';
			} else {
				$age_from = '';
			}
			unset($cond['age_from']);
			if(!empty($cond['age_to'])){
				$age_to = ' AND bday >= '.(strtotime(date('d.m.Y',time()).' - '.$cond['age_to'].' years')).' ';
			} else {
				$age_to = '';
			}
			unset($cond['age_to']);
			$_cond = !empty($cond) ? ' AND '.$this->db->sql_prepare($cond,' AND ') : '';
			$_cond .= $age_from.$age_to.' AND u.fname != "" AND u.sname != "" AND u.lname != "" ';
			$phrase = explode(' ', $phrase);
			$where  = '';
			// -- составляем условие поиска
			foreach($phrase as $i => $word) {
				$word    = $this->getCutWord($word);
				// $pref = (mb_strlen($word, 'UTF-8') > 4) ? '' : ' ';
				$pref = (mb_strlen($word, 'UTF-8') > 4) ? '' : '';
				$where  .= ($i > 0 ? ' AND ' : '') .''
						.' u.fname LIKE "%'.$pref.addslashes($word).'%" OR '
						.' u.sname LIKE "%'.$pref.addslashes($word).'%" OR '						
						.' CONCAT(u.fname," ", u.sname) LIKE "%'.$pref.addslashes($word).'%"';
				$words[] = $word;
			}
			if(!empty($where)) $where = '('.$where.')';
			$this->words_arr = $words;

			// -- кол-во соответствий
			$sql  = 'SELECT COUNT(*) FROM users u '.
					'WHERE '.$where.$_cond;
			$data['all_count'] = $this->db->get_one($sql);

			// -- сам поиск =)
			$sql  = 'SELECT u.* FROM users u '.
					'WHERE '.$where.$_cond.' AND state = 1 LIMIT '.($page-1)*$count.', '.$count;
			$data['list'] = $this->db->get_all($sql);			
			return $data;
		}
		private function getCutWord($input) {
			$lenght = mb_strlen($input, 'UTF-8');
			if($lenght > 9) {
				return mb_substr($input, 0, -3, 'UTF-8');
			} elseif($lenght > 7) {
				return mb_substr($input, 0, -2, 'UTF-8');
			}  elseif($lenght > 4) {
				return mb_substr($input, 0, -1, 'UTF-8');
			}
			return $input;
		}
		public function getLetters($count){
			$sql = 'SELECT mt.*, l.* FROM letters l LEFT JOIN mail_task mt ON mt.id = l.task_id WHERE l.status = 0 AND l.try_count > 0 AND l.date < NOW() ORDER BY l.priority LIMIT '.(int)$count;
			return $this->db->get_all($sql);
		}
		public function getAllUsers(){
			$sql = 'SELECT * FROM users WHERE 1';
			return $this->db->get_all($sql);
		}
	}
?>
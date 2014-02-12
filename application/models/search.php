<?php
	class search extends app_model {
	
		private $words_arr = array();

		public function getMainIdProduct($id) {
			$sql = 'SELECT id FROM main WHERE cid = '.(int)$id;
			return $this->db->get_one($sql);
		}
	
		public function getURL($id, $module_id) {
			switch($module_id) {
				case 0:
					$url = '/';
					break;
				case 2:
					$sql = 'SELECT id, pid, YEAR(date) year, MONTH(date) month FROM news WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/news/'.$row['pid'].'/'.$row['year'].'/'.$row['month'].'/'.$row['id'].'/';
					break;
				case 8:
					$url = '/catalog/';
					break;
				case 10:
					$sql = 'SELECT id, pid, YEAR(date) year, MONTH(date) month FROM articles WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/articles/'.$row['pid'].'/'.$row['year'].'/'.$row['month'].'/'.$row['id'].'/';
					break;
				case 11:
					$sql = 'SELECT id, pid, YEAR(date) year, MONTH(date) month FROM portfolio WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/portfolio/'.$row['pid'].'/'.$row['year'].'/'.$row['month'].'/'.$row['id'].'/';
					break;
				case 12:
					$sql = 'SELECT id, pid, YEAR(date) year, MONTH(date) month FROM actions WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/actions/'.$row['pid'].'/'.$row['year'].'/'.$row['month'].'/'.$row['id'].'/';
					break;
				case 13:
					$sql = 'SELECT id, pid, YEAR(date) year, MONTH(date) month FROM partners WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/partners/'.$row['pid'].'/'.$row['year'].'/'.$row['month'].'/'.$row['id'].'/';
					break;
				case 14:
					$sql = 'SELECT id FROM types WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/catalog/'.$row['id'].'/';
					break;
				case 15:
					$sql = 'SELECT id, type_id FROM modifications WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/catalog/'.$row['type_id'].'/'.$row['id'].'/';
					break;
				case 16:
					$sql = 'SELECT s.id, s.mod_id, m.type_id FROM subfamily s LEFT JOIN modifications m ON m.id = s.mod_id WHERE s.id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = '/catalog/'.$row['type_id'].'/'.$row['mod_id'].'/'.$row['id'].'/';
					break;
				default:
					$sql = 'SELECT url, link FROM main WHERE id = '.(int)$id;
					$row = $this->db->get_row($sql);
					$url = (empty($row['link'])) ? $row['url'] : $row['link'];
					break;
			}
			return $url;
		}
		
		
		public function getResults($phrase, $page, $count) {
			$phrase = explode(' ', $phrase);
			$where  = '';
			// -- составляем условие поиска
			foreach($phrase as $i => $word) {
				$word    = $this->getCutWord($word);
				$pref = (mb_strlen($word, 'UTF-8') > 4) ? '' : ' ';
				$where  .= ($i > 0 ? ' AND ' : '').'text LIKE "%'.$pref.addslashes($word).'%" OR title LIKE "%'.$pref.addslashes($word).'%"';
				$words[] = $word;
			}
			$this->words_arr = $words;
			// -- кол-во соответствий
			$sql  = 'SELECT COUNT(*) FROM search_index '.
					'WHERE '.$where;
			$data['all_count'] = $this->db->get_one($sql);
			// -- сам поиск =)
			$sql  = 'SELECT * FROM search_index '.
					'WHERE '.$where.' LIMIT '.($page-1)*$count.', '.$count;
			$data['list'] = $this->db->get_all($sql);
			// -- обработка результатов поиска
			// var_dump($data['list']);
			// die();
			if(!empty($data['list'])) {
				foreach($data['list'] as $i => &$item) {
					$item['text']  = str_replace('&nbsp;', ' ', $item['text']);
					$item['url']   = $this->getURL($item['pid'], $item['module_id']);
					// $item['note']  = $this->getLight($item['text'], $words);
					$item['note']  = $item['text'];
					// $this->getLightRun($item['title'], $words);
					unset($item['text']);
				}
			}
			
			return $data;
		}
		
		// -- отрезаем окончание у слова
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
		
		// -- подсветка
		private function getLight($text, $words) {
			$result  = array();
			$phrase  = '';
			foreach($words as $i => $item) {
				$phrase .= ($i > 0 ? '|' : '').'('.$item.'[a-zа-я\.]{0,3})';
			}
			$pattern = '/(\S+\s+){0,3}('.$phrase.')(\s+\S+){0,3}/iu';
			$data = array();
			preg_match_all($pattern, $text, $data);
			if(empty($data)) return '';
			foreach($data[0] as $i => &$item) {
				$result[] = '...'.$item.'...';
				if($i == 6) break;
			}
			$result = implode(' ', $result);
			// $this->getLightRun(&$result, $words);
			return $result;
		}
		
		public function getLightRun(&$text) {
			foreach($this->words_arr as $i => $word) {
				$text = preg_replace('/([^а-я]+)('.$word.'[a-zа-я0-9\.]{0,3})([^а-я]+)/iu', '$1<span class="light">$2</span>$3', $text);
			}
		}

	}
?>
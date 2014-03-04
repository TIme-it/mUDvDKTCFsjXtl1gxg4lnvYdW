<?php
	class all extends application_controller {

		public function __construct() {	
			// -- must be empty
		}

		public function getPagePid($string) {
			$sql = 'SELECT id FROM main WHERE alias = "'.$string.'"';
			return $this->db->get_one($sql);
		}

		public function getMainUrl($id){
			$sql = 'SELECT alias, pid FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}

		public function getMainAliasUrl($alias){
			$sql = 'SELECT module FROM main WHERE alias = '.'"'.$alias.'"';
			return $this->db->get_one($sql);
		}

		public function getModuleName($id){
			$sql = 'SELECT name FROM module WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}


		public function getMainPid($alias){
			$sql = 'SELECT pid FROM main WHERE alias = '.'"'.$alias.'"';
			return $this->db->get_one($sql);
		}

		public function getStandartUrl($id){
			$sql = 'SELECT url FROM main WHERE id = '.(int)$id;
			return $this->db->get_one($sql);
		}
	
		public function getPageOnPrint($mid, $id) {
			$sql = 'SELECT title_page title, date FROM main WHERE id = '.(int)$id;
			if($mid > 1) {
				$table = $this->getTable($mid);
				
				$fields = 'x.title, x.date, x.is_show_date, x.source ';
				if ( ($mid == 2) ) //новости
					$fields .= ', x.author';
				
				$sql = 'SELECT '.$fields.'  
						FROM '.$table.' x LEFT JOIN main m ON m.id = x.pid
						WHERE x.id = '.(int)$id;
			}
			return $this->db->get_row($sql);
		}
		
		
		public function getImagesCount($pid, $mid) {
			$sql = 'SELECT COUNT(id) FROM images WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			return $this->db->get_one($sql);
		}
		
		public function getImages($pid, $mid, $page = 0, $count = 16) {
			$sql = 'SELECT * FROM images WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid.' ORDER BY sort, id LIMIT '.($page*$count).','.$count;
			return $this->db->get_all($sql);
		}
		public function getImages1($pid, $mid, $page = 1, $count = 200) {
			//$sql = 'SELECT * FROM images WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid.' ORDER BY sort, id LIMIT '.($page*16).',200';
			$sql = 'SELECT * FROM images WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			return $this->db->get_all($sql);
		}

		public function getFiles($pid, $mid) {
			$sql = 'SELECT * FROM files WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid.' AND is_show = 1	ORDER BY sort, id';
			return $this->db->get_all($sql);
		}
		
		public function getVideoCount($pid, $mid) {
			$sql  = 'SELECT COUNT(*) FROM videos '.
					'WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			return (int)$this->db->get_one($sql);
		}
		
		public function getVideoOne($id) {
			$sql  = 'SELECT * FROM videos WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function getVideo($pid, $mid) {
			$sql  = 'SELECT * FROM videos '.
					'WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			return $this->db->get_all($sql);
		}
		
		public function getFileInfo($id) {
			$sql = 'SELECT * FROM files WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
		public function breadCrums($id)
		{
			$sql = 'SELECT id, pid, title, url FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
			
		public function getPublicationBreadCrumb($id) {
			$sql = 'SELECT id,pid,title,url,link 
					FROM main 
					WHERE id='.$id;
			return $this->db->get_row($sql);
		}
		
		public function getMaps($pid, $mid) {
			$sql  = 'SELECT id map_id, title map_title FROM maps WHERE pid = '.(int)$pid.' AND module_id = '.(int)$mid;
			$data = $this->db->get_all($sql);
			if(!empty($data)) {
				foreach($data as $i => &$item) {
					$sql = 'SELECT * FROM maps_placemarks WHERE pid = '.$item['map_id'];
					$item['placemark_list'] = $this->db->get_all($sql);
					if(!empty($item['placemark_list'])) {
						$min_lat  = false;
						$max_lat  = false;
						$min_long = false;
						$max_long = false;
						foreach($item['placemark_list'] as $j => &$pl_item) {
							if(!$min_lat  || $pl_item['latitude']  < $min_lat)  $min_lat  = $pl_item['latitude'];
							if(!$max_lat  || $pl_item['latitude']  > $max_lat)  $max_lat  = $pl_item['latitude'];
							if(!$min_long || $pl_item['longitude'] < $min_long) $min_long = $pl_item['longitude'];
							if(!$max_long || $pl_item['longitude'] > $max_long) $max_long = $pl_item['longitude'];
						}
						$d_lat  = $max_lat  - $min_lat;
						$d_long = $max_long - $min_long;
						// -- находим центральную точку + маштаб
						$item['center_latitude']  = $min_lat  + $d_lat  / 2;
						$item['center_longitude'] = $min_long + $d_long / 2;
						$item['zoom']             = $this->getZoomMap($d_lat > $d_long ? $d_lat : $d_long);
					}
				}
			}
			return $data;
		}
		
		// -- получаем маштаб для Яндекс.Карты по числу разнице точек
		private function getZoomMap($digit) {
			if($digit > 8.00)  return  4;
			if($digit > 4.00)  return  5;
			if($digit > 2.35)  return  6;
			if($digit > 1.55)  return  7;
			if($digit > 0.5)   return  8;
			if($digit > 0.38)  return  9;
			if($digit > 0.2)   return 10;
			if($digit > 0.1)   return 11;
			if($digit > 0.04)  return 12;
			if($digit > 0.02)  return 13;
			if($digit > 0.01)  return 14;
			if($digit > 0.005) return 15;
			return 16;
		}
		
		public function getForPages($table, $pid, $act_page, $max_count, $is_active_only = false) {
			$data = array();
			$sql  = 'SELECT COUNT(*) FROM '.$table.' '.
					'WHERE pid = "'.(int)$pid.'"'.($is_active_only ? ' AND active = 1' : '');
			$all_count  = $this->db->get_one($sql);
			$page_count = (int)ceil($all_count / $max_count);
			if(empty($page_count) || $page_count < 2) {
				return false;
			}
			for($i = 1; $i <= $page_count; $i++) {
				$data['list'][] = array(
					'page'   => $i,
					'url'    => '/'.$table.'/'.$pid.'/'.(($i > 1) ? 'page'.$i.'/' : ''),
					'active' => ($i == $act_page) ? 'class="active"' : ''
				);
			}
			return $data;
		}
		
		// -- получить данные для блока "модули"
		public function getModulesBlockInfo($pid, $mid) {
			$table = $this->base->getTableName($mid);
			$fields = 'print, feedback, sendfile';
			if ($table != 'firms') $fields .= ', source, date, is_show_date';
			if ($table == 'main') $fields .= ', subsection';
			if ( ($table == 'news') || ($table == 'cnews') ) $fields .= ', author';
			
			$sql = 'SELECT '.$fields.' FROM `'.$table.'` WHERE id = '.(int)$pid;
			return $this->db->get_row($sql);
		}
		
		// -- получаем название таблици по id модуля
		public function getTable($mid) {
			return $this->db->get_one('SELECT name FROM module WHERE id = '.(int)$mid);
		}

		public function getHeadFeed($id){
			return $this->db->get_one('SELECT head FROM feedback WHERE id = '.(int)$id);
		}

		public function getTemplateFeed($id){
			return $this->db->get_one('SELECT title FROM feedback WHERE id = '.(int)$id);
		}

		public function getFeedBack($id){
			return $this->db->get_all('SELECT * FROM feedback_fields WHERE pid = '.(int)$id.' ORDER BY position DESC');
		}
	}
?>
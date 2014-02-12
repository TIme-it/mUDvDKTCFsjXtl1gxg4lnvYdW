<?php
class page extends application_controller {
	
	public function __construct() {}
	
	public function getPage($url) {
		// -- отсекаем $_GET
		$url = explode('?', $url);
		$url = $url[0];
		$sql = 'SELECT * FROM main WHERE url = '.$this->db->escape($url);
		return $this->db->get_row($sql);
	}
	public function getPageAlias($alias) {
		$sql = 'SELECT * FROM main WHERE alias = "'.$alias.'"';
		return $this->db->get_row($sql);
	}

	public function getPageOnPrint($id) {
		$sql = 'SELECT m.title, p.date 
				FROM pages AS p LEFT JOIN main AS m ON m.id = p.id
				WHERE p.id = '.(int)$id;
		return $this->db->get_row($sql);
	}
	
	public function getRecalls($pid,$limit) {
		$limit = mysql_real_escape_string($limit);
		$sql = 	'SELECT id,fio,email,text,date 
					 FROM recalls 
					 WHERE pid='.$this->db->escape($pid).'
					 ORDER by date desc 
					 LIMIT 0,'.$limit;
		return $this->db->get_all($sql);
	}
	
	public function getBreadCrumb($alias) {
		$sql = 'SELECT title 
				FROM main 
				WHERE alias='.$this->db->escape($alias);
		return $this->db->get_one($sql);
	}

	public function getPlacemarkList($pid) {
		$sql  = 'SELECT * FROM map_placemarks WHERE pid = '.(int)$pid;
		return $this->db->get_all($sql);
	}
	
}
?>
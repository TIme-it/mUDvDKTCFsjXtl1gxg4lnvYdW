<?php
class trash extends application_controller {

	public function __construct() {}
	
	// public function getTrash($table = 'main') {
		// $sql =($table != 'main')? 
				// 'SELECT * 
				 // FROM '.$table.'
				 // WHERE active = 0' :
				 
				// 'SELECT m.id,m.pid,m.title,mm.name
				 // FROM '.$table.' m
				 // LEFT JOIN module mm 
				 // ON mm.id = m.module 
				 // WHERE active = 0';
		// return $this->db->get_all($sql);
	// }
	
	public function getTrash($table = 'main', $access = array()) {
		$sql = 'SELECT * FROM '.$table.' WHERE active = 0'.(empty($access) ? '' : ' AND id IN('.join(',',$access).')');
		return $this->db->get_all($sql);
	}
	
	public function getInfo($id) {
		$sql = 'SELECT m.id,m.pid,m.title,mm.name 
				  FROM main m
				  LEFT JOIN module mm 
				  ON mm.id = m.module
				  WHERE m.id='.$this->db->escape($id);
		return $this->db->get_row($sql);
	}
}
?>
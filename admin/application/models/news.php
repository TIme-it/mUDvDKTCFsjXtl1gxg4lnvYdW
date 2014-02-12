<?php
class news extends application_controller {
	
	public function __construct() {
		// empty
	}
	
	public function getNewsList($pid) {
		$sql = 'SELECT * FROM news WHERE pid = '.(int)$pid.' ORDER BY date DESC, id DESC';
		return $this->db->get_all($sql);
	}
	
	public function getOneNews($id) {
		$sql = 'SELECT * FROM news WHERE id = '.(int)$id.' LIMIT 1';
		return $this->db->get_row($sql);
	}
	
	// -- получить инфу о разделе новостей
	public function aboutNewsCategory($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
}
?>
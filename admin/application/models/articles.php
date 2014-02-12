<?php
class articles extends application_controller {
	
	public function __construct() {
		// empty
	}
	
	public function getarticlesList($pid) {
		$sql = 'SELECT * FROM articles WHERE pid = '.(int)$pid.' ORDER BY date DESC, id DESC';
		return $this->db->get_all($sql);
	}
	
	public function getOnearticles($id) {
		$sql = 'SELECT * FROM articles WHERE id = '.(int)$id.' LIMIT 1';
		return $this->db->get_row($sql);
	}
	
	// -- получить инфу о разделе новостей
	public function aboutarticlesCategory($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
}
?>
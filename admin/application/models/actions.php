<?php
class actions extends application_controller {
	
	public function __construct() {
		// empty
	}
	
	public function getActionsList($pid) {
		$sql = 'SELECT * FROM actions WHERE pid = '.(int)$pid.' ORDER BY date DESC, id DESC';
		return $this->db->get_all($sql);
	}
	
	public function getOneactions($id) {
		$sql = 'SELECT * FROM actions WHERE id = '.(int)$id.' LIMIT 1';
		return $this->db->get_row($sql);
	}
	
	// -- получить инфу о разделе новостей
	public function aboutActionsCategory($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
}
?>
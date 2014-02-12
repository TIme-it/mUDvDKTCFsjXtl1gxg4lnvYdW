<?php
class partners extends application_controller {
	
	public function __construct() {
		// empty
	}
	
	public function getpartnersList($pid) {
		$sql = 'SELECT * FROM partners WHERE pid = '.(int)$pid.' ORDER BY date DESC, id DESC';
		return $this->db->get_all($sql);
	}
	
	public function getOnepartners($id) {
		$sql = 'SELECT * FROM partners WHERE id = '.(int)$id.' LIMIT 1';
		return $this->db->get_row($sql);
	}
	
	// -- получить инфу о разделе новостей
	public function aboutpartnersCategory($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
}
?>
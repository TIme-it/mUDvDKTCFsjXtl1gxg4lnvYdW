<?php
class portfolio extends application_controller {
	
	public function __construct() {
		// empty
	}
	
	public function getPortfolioList($pid) {
		$sql = 'SELECT * FROM portfolio WHERE pid = '.(int)$pid.' ORDER BY date DESC, id DESC';
		return $this->db->get_all($sql);
	}
	
	public function getOnePortfolio($id) {
		$sql = 'SELECT * FROM portfolio WHERE id = '.(int)$id.' LIMIT 1';
		return $this->db->get_row($sql);
	}
	
	// -- получить инфу о разделе новостей
	public function aboutPortfolioCategory($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
}
?>
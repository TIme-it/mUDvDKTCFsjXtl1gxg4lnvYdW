<?php

class personal extends application_controller {

	public function __construct() {}
	
	public function getPersonalList($pid, $dir_id) {
		$sql  = 'SELECT * FROM personal '.
				'WHERE pid = '.(int)$pid.' AND id != '.(int)$dir_id.' '.
				'ORDER BY department, id';
		$list = $this->db->get_all($sql);
		if(empty($list)) return array();
		$result = array();
		foreach($list as $i => &$item) {
			$result[$item['department']]['list'][] = $item;
		}
		unset($list);
		return $result;
	}
	
	public function getOnePerson($id) {
		$sql = 'SELECT * FROM personal WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
	
	public function getMainInfo($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
		
	}

}

?>
<?php
class menu extends application_controller {
	
	public function __construct() {
		$this->table = 'main';
	}
	
	public function getModules($mode = false) {
		$sql = 'SELECT id, title, name FROM module';
		switch($mode) {
			case 'add':
				$sql = 'SELECT id, title, name FROM module WHERE is_show_add = 1';
				break;
		}
		return $this->db->get_all($sql);
	}
	
	public function getDirectories($pid = null) {
		$sql = 'SELECT m.id, m.pid, m.title, m.alias, a.name as module, m.tree 
				FROM '.$this->table.' m 
				LEFT JOIN module a ON a.id = m.module
				WHERE m.active != 0'; 
		$sql .= (is_null($pid)) ? '' : ' AND m.pid = '.(int)$pid;
		$sql .= ' ORDER BY tree, id, pid';
		return $this->db->get_all($sql);
	}
	
	public function getDirectories2($pid = null, $access = array()) {
		$sql = 'SELECT m.id, m.pid, m.title, m.alias, a.name as module, m.tree 
				FROM '.$this->table.' m 
				LEFT JOIN module a ON a.id = m.module
				WHERE m.active != 0'; 
		$sql .= (is_null($pid)) ? '' : ' AND m.pid = '.(int)$pid;
		$sql .= (empty($access)) ? '' : ' AND m.id IN('.join(',', $access).') ';
		$sql .= ' ORDER BY tree, id, pid';
		
		return $this->db->get_all($sql);
	}

	public function isChilds($id) {
		$sql = 'SELECT id 
				FROM '.$this->table.' 
				WHERE pid='.$id.' 
				AND active <> 0';
		return (false === $this->db->count_rows($sql))? false : true;
	}
	
	public function countChilds($pid) {
		$sql = 'SELECT COUNT(*) FROM main WHERE pid = '.(int)$pid.' AND active != 0';
		return $this->db->get_one($sql);
	}
	
	public function getMainInfo($id) {
		$sql = 'SELECT m.pid,m.title as name,m.alias,m.module,m.active,m.inmenu,a.name as module_name
				  FROM main m
				  LEFT JOIN module a ON a.id = m.module
				  WHERE m.id = '.$this->db->escape($id);
		return $this->db->get_row($sql);
	}
	
	public function getParents($id) {
		$sql = 'SELECT pid 
				FROM main 
				WHERE id ='.$this->db->escape($id);
		return $this->db->get_one($sql);
	}
	
	public function getNeighbor($pid) {
		$sql = 'SELECT id,pid,tree 
				FROM main 
				WHERE pid='.$this->db->escape($pid).' AND active <> 0
				ORDER BY tree';
		return $this->db->get_all($sql);
	}
}
?>
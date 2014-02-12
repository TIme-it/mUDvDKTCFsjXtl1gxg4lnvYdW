<?php
class module extends application_controller {
	
	public function __construct() {}
	
	public function is_alias($alias) {
		$sql = 'SELECT id 
				FROM main 
				WHERE alias ='.$this->db->escape($alias);
		return $this->db->count_rows($sql);
	}
}
?>
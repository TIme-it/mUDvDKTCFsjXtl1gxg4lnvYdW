<?php
	class map extends application_controller {
		
		public function __construct() {
			// empty
		}
		public function get_attributes_from_main($pid = 0){
			$sql = 'SELECT m.*, md.name as mod_name FROM main m LEFT JOIN module md ON m.module = md.id WHERE m.active = 1 AND m.inmenu = 1 AND m.pid = '.(int)$pid;
			return $this->db->get_all($sql);
		}
		public function getAll($table, $pid){
			$sql = 'SELECT * FROM information_schema.tables WHERE table_name = '.$this->db->escape($table).' LIMIT 1';
			$t_info = $this->db->get_row($sql);
			if(!empty($t_info)){
				$sql = 'SELECT * FROM '.$table.' WHERE active = 1 AND pid = '.(int)$pid;
				return $this->db->get_all($sql);
			}
			else{
				return false;
			}
		}
		
	}
?>
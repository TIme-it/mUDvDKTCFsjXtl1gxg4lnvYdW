<?php
	class app_model extends libs_controller {
		
		protected $table = null;
		
		public function __construct() {
			$this->table = get_class($this);
		}
		
		public function save($_data) {
			if(empty($_data[$this->table])) {
				return false;
			}
			$data =& $_data[$this->table];
			if(!empty($data['id'])) {
				$id = $data['id'];
				unset($data['id']);
				$this->db->update($this->table, $data, $id);
				return $id;
			}
			return $this->db->insert($this->table, $data);
		}
		
		public function getConfig($id) {
			$sql    = 'SELECT config FROM main WHERE id = '.(int)$id;
			$config = $this->db->get_one($sql);
			return empty($config) ? array() : unserialize($config);
		}
		
		// -- общая функция получения данных раздела
		public function getInfo($id) {
			$sql = 'SELECT
						id main_id,
						pid main_pid,
						note main_note,
						title main_title,
						title_page,
						description,
						keywords,
						template,
						active,
						config,
						inmenu,
						link,
						is_show_date
					FROM main WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
	}
?>
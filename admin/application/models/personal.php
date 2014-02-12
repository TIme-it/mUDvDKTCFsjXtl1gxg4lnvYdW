<?php
class personal extends app_model {

	public function getInfoAboutPersonal($pid) {
		$sql = 'SELECT id as id_main,title,title_page,note,keywords,description,template,inmenu
				FROM main 
				WHERE id='.$this->db->escape($pid).' AND active<>0';
		return $this->db->get_row($sql);
	}
	
	public function getPersonal($id) {
		$sql = 'SELECT id as id_person,pid,fio,post,about 
				FROM personal 
				WHERE pid='.$this->db->escape($id);
		return $this->db->get_all($sql);
	}
	
	public function getOnePerson($id) {
		$sql = 'SELECT * FROM personal WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
	
	public function getDepartments($pid) {
		$sql = 'SELECT DISTINCT department AS dep_title FROM personal WHERE pid = '.(int)$pid.' ORDER BY department ASC';
		return $this->db->get_all($sql);
	}
	
	
	public function getInfo($id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}
}
?>
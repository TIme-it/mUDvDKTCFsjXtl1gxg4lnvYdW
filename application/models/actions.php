<?php
class actions extends application_controller {
	
	public function __construct() {
		// empty
	}

	public function getUrl($id){
		$sql = 'SELECT alias, pid FROM actions WHERE id = '.(int)$id;
		return $this->db->get_row($sql);
	}

	
	public function getPagePidAlias($string) {
		$sql = 'SELECT id FROM actions WHERE alias = "'.$string.'"';
		return $this->db->get_one($sql);
	}

	public function getPaged($id) {
		$sql = 'SELECT pid FROM actions WHERE id = '.(int)$id;
		return $this->db->get_one($sql);
	}


	public function getPagePid($string) {
		$sql = 'SELECT id FROM main WHERE alias = "'.$string.'"';
		return $this->db->get_one($sql);
	}

	public function getPageInfo($id, $module_id) {
		$sql = 'SELECT * FROM main WHERE id = '.(int)$id.' AND module = '.(int)$module_id;
		return $this->db->get_row($sql);
	}
	
	public function getActionsCount($pid) {
		$sql   = 'SELECT COUNT(*) FROM actions '.
				 'WHERE pid = '.(int)$pid.' AND active != 0';
		return $this->db->get_one($sql);
	}

	public function getLast($pid, $limit) {
		$sql = 'SELECT id, pid, title, date, note, source, YEAR(date) as year, MONTH(date) as month FROM actions 
			    WHERE pid = '.(int)$pid.' AND active != 0 ORDER BY date DESC, id DESC LIMIT '.(int)$limit;
		return $this->db->get_all($sql);
	}
	
	public function getActions($pid, $page, $count) {
		$sql   = 'SELECT *, MONTH(date) AS month, YEAR(date) AS year FROM actions '.
				 'WHERE pid = '.(int)$pid.' AND active != 0 ORDER BY date DESC, id DESC LIMIT '.($page * $count).', '.$count;
		return $this->db->get_all($sql);
	}

	public function getRandActions(){
		$sql = 'SELECT id, pid, title, note, MONTH(date) AS month, YEAR(date) AS year FROM actions WHERE active = 1 ORDER BY RAND() LIMIT 1';
		return $this->db->get_all($sql);
	}
	
	public function getAllActions($pid) {
		$sql   = 'SELECT *, MONTH(date) AS month, YEAR(date) AS year FROM actions '.
				 'WHERE pid = '.(int)$pid.' AND active != 0 ORDER BY date DESC, id DESC';
		return $this->db->get_all($sql);
	}

	public function getOneMY($id){
		$sql = 'SELECT pid, id, MONTH(date) AS month, YEAR(date) AS year FROM actions '.
				 'WHERE id = '.(int)$id.' AND active != 0 ORDER BY date DESC, id DESC';
		return $this->db->get_row($sql);
	}
	
	public function getOneActions($id) {
		$sql = 'SELECT * FROM actions WHERE active != 0 AND id = '.(int)$id;
		return $this->db->get_row($sql);
	}

	public function getYears($pid) {
		$sql = 'SELECT DISTINCT(YEAR(date)) as year 
				FROM actions 
				WHERE (pid='.$this->db->escape($pid).' AND active<>0) 
				ORDER BY YEAR(date) ';
		return $this->db->get_all($sql);
	}

	public function getMonths($pid,$year) {
		$sql = 'SELECT DISTINCT(MONTH(date)) as month 
				FROM actions 
				WHERE( pid='.$this->db->escape($pid).' AND YEAR(date)='.$year.' and active<>0) 
				ORDER BY MONTH(date)';
		return $this->db->get_all($sql);
	}
	
	public function getLastActiveMonth($pid,$year) {
		$sql = 'SELECT MAX(MONTH(date)) as month 
				FROM actions 
				WHERE (YEAR(date)='.$this->db->escape($year).' AND pid='.$this->db->escape($pid).' and active<>0) 
				LIMIT 0,1';
		return $this->db->get_one($sql);
	}
	
	public function getOthers($id,$pid,$limit = 3) {
		$sql = 'SELECT n.id,n.pid,n.title,n.date,MONTH(n.date) as month,YEAR(n.date) as year,n.note,m.title as title_category 
				FROM actions n
				LEFT JOIN main m 
				ON n.pid = m.id 
				WHERE n.pid='.$this->db->escape($pid).' AND n.id <>'.$this->db->escape($id).' AND n.active<>0
				LIMIT 0,'.$limit;
		return $this->db->get_all($sql);
	}
	
	public function getAboutActionsCategory($id) {
		$sql = 'SELECT title as page_title,alias,note as actionsText,template 
				  FROM main 
				  WHERE id='.$this->db->escape($id);
		return $this->db->get_row($sql);
	}
	
	public function getForPages($id,$year,$month,$count) {
		$sql = 'SELECT COUNT(id)
				FROM actions 
				WHERE pid='.$this->db->escape($id);
		$sql .= ($month !== false) ?' AND MONTH(date)='.$month :'';
		$sql .= ($year !== false)  ?' AND YEAR(date)='.$year   :'';
		$result = $this->db->get_one($sql);
		$result = ceil($result/$count)+1;
		$pages = array();
		if($result !== 0) {
			$i = 1;
			while($i < $result) {
				$pages['list'][$i]['page'] = $i;
				$pages['list'][$i]['url'] = '/actions/'.$id.'/';
				$pages['list'][$i]['url'] .=($year !== false)?$year.'/':'';
				$pages['list'][$i]['url'] .=($month !== false)?$month.'/':'';
				$pages['list'][$i]['url'] .= 'page'.$i.'/';
				$i++;
			}
		} else {
			$pages = false;
		}
		return $pages;
	}
}
?>
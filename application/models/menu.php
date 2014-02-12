<?php
	class menu extends application_controller {
		
		public function __construct() {
			// empty
		}
		
		// -- получаем иерархичную СД меню, ограниченную $level уровнем
		// -- и $limit запясями с вершиной в $pid 
		public function getMenu($pid = 0, $level = 2, $limit = false) {
			$menu = array('list' => array(), 'deep' => 0);
			$this->getMenuReq($menu['list'], $pid, 1, $level, $limit);
			return $menu;
		}
		
		private function getMenuReq(&$node, $pid, $deep, $level, $limit) {
			if($deep > $level) return false;
			
			$sql  = 'SELECT m.*, module.name '.
					'FROM main AS m LEFT JOIN module ON m.module = module.id '.
					'WHERE m.pid = '.(int)$pid.' AND m.active = 1 AND m.inmenu = 1 '.
					'ORDER BY m.tree, m.id';
					
			if(!empty($limit)) $sql .= ' LIMIT '.$limit;
					
			$node = $this->db->get_all($sql);
			if(empty($node)) return false;
			foreach($node as $i => &$item) {
				$item['childs'] = array('list' => array(), 'deep' => $deep+1);
				if($this->config->get('active','chpu') == 1){
					$item['url'] = $this->application_controller->get_url($item['id']);
				}
				$this->getMenuReq($item['childs']['list'], $item['id'], $deep+1, $level, $limit);
			}
		}
		
		// -- показать подразделы для к-л раздела
		public function getSubsection($pid) {
			$sql  = 'SELECT m.*, module.name '.
					'FROM main AS m LEFT JOIN module ON m.module = module.id '.
					'WHERE m.pid = '.(int)$pid.' AND m.active = 1 AND m.inMenu = 1 '.
					'ORDER BY m.tree, m.id';
			return $this->db->get_all($sql);
		}
		
	}
?>
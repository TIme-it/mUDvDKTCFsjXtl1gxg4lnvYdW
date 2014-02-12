<?php
	class menu_controller extends application_controller {
		
		// -- массив id'ов 
		public $active_ids = array();
		
		// -- выводим дерево структуры сайта
		public function print_tree($active_id = 0) {
			$tree = array();
			
			//Выводим только те, на которые есть права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($role_id == 0) && ($user_id !== 0) ) return array();
			
			$access = ($role_id>0) ? $this->role->getAccessPages($role_id) : array();
			
			
			$this->get_tree_req($tree, 0, $active_id, $access);
			if (!empty($tree))
				foreach($tree as $i => &$child) {
					if (empty($child['childs']) && (!empty($access)) && (!in_array($child['id'], $access)) )
						unset($tree[$i]);
				}
			
			ob_start();
			$this->print_tree_req($tree, $active_id);
			return ob_get_clean();
		}
		
		private function get_tree_req(&$node, $pid, $active_id, $access = array()) {
			$node = $this->menu->getDirectories($pid, $access);
			if(empty($node)) return false;
			foreach($node as $i => &$item) {
				$item['childs'] = array();
				$this->get_tree_req($item['childs'], $item['id'], $active_id, $access);
				
				if (!empty($item['childs']))
					foreach($item['childs'] as $i => &$child) {
						if (empty($child['childs']) && (!empty($access)) &&  (!in_array($child['id'], $access)) )
							unset($item['childs'][$i]);
					}
				
				if($item['id'] == $active_id) {
					$this->active_ids[] = $item['id'];
				}
				if(in_array($item['id'], $this->active_ids)) {
					$this->active_ids[] = $item['pid'];
				}
			}
		}	
		
		private function print_tree_req(&$node, $active_id) {
			if(empty($node)) return false;
			foreach($node as $i => &$item) {
				$has_childs = (!empty($item['childs']));
				$is_active  = ($item['id'] == $active_id);
				$is_act_par = in_array($item['id'], $this->active_ids);
				$class = $has_childs ? ($is_active || $is_act_par ? 'minus' : 'plus') : 'none';
				if($item['module'] == 'catalog'){
					$class = 'none';
				}
				echo '<div class="li">';
				echo '<div class="clear">&nbsp;</div>';
				echo '<a class="'.$class.' link_dir" href="#" onclick="getMenu('.$item['id'].', $(this)); return false;">&nbsp;</a>';
				echo '<a href="/admin/'.$item['module'].'/view/'.$item['id'].'/" class="menu_li_title '.($is_active?'active':'').'">'.$item['title'].'</a>';
				if($has_childs && $item['module'] != 'catalog') {
					echo '<div class="clear">&nbsp;</div>';
					echo '<div class="ul" id="childs_'.$item['id'].'"'.($is_act_par?' style="display: block;"':'').'>';
					$this->print_tree_req($item['childs'], $active_id);
					echo '</div>';
				}
				echo '<div class="clear">&nbsp;</div>';
				echo '</div>';
			}
			
		}
		
		public function menu($id, $active_id = 0) {
			$menu = $this->menu->getDirectories($id);
			$i = 0;
			if(!empty($menu)) {
				foreach($menu as $key=>$value) { 
					$menu[$key]['admin_dir'] = $this->config->get('admin_dir','system');
					$menu[$key]['active'] = ($menu[$key]['id'] == $active_id)?'class="active"':'';
					if(true === $this->menu->isChilds($menu[$key]['id'])) {
						$menu[$key]['dir'] = $this->html->render('menu/link_dir.html',$menu[$key]);
					}
					$menu[$key]['position'] = $i;
					$i++;
				}
			}
			return $menu;
		}
		
		public function ajax_menu($id,$active_id = '') {
			$menu['menu'] = $this->menu($id,$active_id);
			echo $this->html->render('menu/menu.html',$menu);
			die();exit();
		}

		public function upTree($id,$pid,$pos) {
			$tree = $this->menu->getNeighbor($pid);
			$update['tree'] = $tree[($pos-1)]['tree']+1;
			$this->db->update('main',$update,$id);
			$menu['menu'] = $this->menu(0);
			die($this->html->render('menu/menu.html',$menu));
		}
		
		public function downTree($id,$pid,$pos) {
			$tree = $this->menu->getNeighbor($pid);
			$update['tree'] = ($tree[($pos+1)]['tree']-1);
			$this->db->update('main',$update,$id);
			$menu['menu'] = $this->menu(0);
			die($this->html->render('menu/menu.html',$menu));
		}
	}
?>
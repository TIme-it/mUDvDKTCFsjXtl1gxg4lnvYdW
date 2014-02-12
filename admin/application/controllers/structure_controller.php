<?php
	class structure_controller extends application_controller {
		
		public function index($id = 0, $ajax = false) {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			// -- получаем список текущего слоя
			$struct['list'] = $this->menu->getDirectories($id);
			$struct['option_list'] = $this->getOptions();
			$struct['alert_change_data'] = ALERT_CHANGE_DATA;
			
			$this->html->render('structure/dirs.html', $struct, 'dirs');
			if($ajax) {
				echo $this->html->tpl_vars['dirs'];
				die();
			}
			$this->html->render('structure/structure.html', $struct, 'content_path');
		}
		
		public function update() {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			$trees = explode(',',$_POST['tree']);
			if(!empty($trees)) {
				foreach($trees as $key=>$val) {
					$tree['tree'] = $key;
					$this->db->update('main',$tree,$val);
				}
			}
			die();
		}
		
		private function getOptions() {
			$data = array(array('id' => 0, 'title' => '[Корень]'));
			$this->getOptionsReq($data, 0, 0);
			return $data;
		}
		
		private function getOptionsReq(&$data, $pid, $deep) {
			$node = $this->menu->getDirectories($pid);
			if(empty($node)) return false;
			foreach($node as $i => &$item) {
				if($this->menu->countChilds($item['id']) > 1) {
					$data[] = array(
						'id'    => $item['id'],
						'title' => str_repeat('&mdash;', $deep*2).'&nbsp;'.$item['title'],
					);
				}
				$this->getOptionsReq($data, $item['id'], $deep + 1);
			}
		}

	}
?>
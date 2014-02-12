<?php
	class feedback_controller extends application_controller {

		
		
		public function index(){

			$data['patterns'] = $this->feedback->getAllPatterns();
			$this->html->render('feedback/index.html',$data,'content_path');
		}
		public function add_new_pattern($id = false){
			if(!empty($_POST)){
				if(!empty($_POST['id'])){
					$data  = array('title' => $_POST['title'],
								    'head' => $_POST['head'],
								    );
					$this->db->update('feedback', $data, $_POST['id']);
					$id = $_POST['id'];
				}
				else{
					$data  = array('title' => $_POST['title'],
									'head' => $_POST['head'],
												    );
					$id = $this->db->insert('feedback',$data);
				}
				
				$this->url->redirect('/admin/feedback/add_new_pattern/'.(int)$id);
			}
			else{
				if(!empty($id)){
					$data = $this->feedback->getOnePattern($id);
					$data['fields'] = $this->feedback->getFields($id);
					if(!empty($data['fields'])){
						foreach ($data['fields'] as $i => &$field) {
							switch ($field['type']) {
								case '1':
									$field['type'] = 'поле для ввода';
									break;
								case '2':
									$field['type'] = 'большое поле для ввода';
									break;
								case '3':
									$field['type'] = 'выпадающее меню';
									break;
								default:
									# code...
									break;
							}
						}
					}
				}
				else{
					$data = array();
				}
				$this->html->render('feedback/add_new_pattern.html',$data, 'content_path');
			}
			

		}
		public function del_field($id){
			$this->db->delete('feedback_fields',$id);
			$this->url->redirect('::referer');
		}
		public function add_new_field($pid){
			$data =array('pid' =>$pid,
						 'field_name' =>$_POST['field_name'],
						 'type'       =>$_POST['type'],
						 'rel'		  =>$_POST['rel'],
						 'required'   =>$_POST['required']
				);
		
			$pos = $this->feedback->getMaxPosition();
			$data['position'] = $pos+1;

			$this->db->insert('feedback_fields',$data);
			$this->url->redirect('::referer');
		}
		public function go_top($id,$pid){
			$to_top = $this->feedback->getOneField($id);

			$to_bot = $this->feedback->getMiniMaxPosItem($to_top['position'],$pid);
			if(!empty($to_bot)){
				$to_bot_pos =  array('position' => $to_top['position'] );
				$this->db->update('feedback_fields', $to_bot_pos, $to_bot['id']);
				$to_top_pos =  array('position' => $to_bot['position'] );
				$this->db->update('feedback_fields', $to_top_pos, $to_top['id']);
			}
			
			$this->url->redirect('::referer');
		}

		public function go_bot($id,$pid){
			$to_bot = $this->feedback->getOneField($id);

			$to_top = $this->feedback->getMaxiMinPosItem($to_bot['position'],$pid);
			if(!empty($to_top)){
				$to_bot_pos =  array('position' => $to_top['position'] );
				$this->db->update('feedback_fields', $to_bot_pos, $to_bot['id']);
				$to_top_pos =  array('position' => $to_bot['position'] );
				$this->db->update('feedback_fields', $to_top_pos, $to_top['id']);
			}
			$this->url->redirect('::referer');

		}
		

	}
?>
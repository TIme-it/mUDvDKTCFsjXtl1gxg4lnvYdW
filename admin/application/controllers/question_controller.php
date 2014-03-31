<?php
	class question_controller extends application_controller {
		
		public function index() {
			$data['quest_title_category'] = $this->config->get('quest_title_category', 'site');
			$data['quest_title_block']    = $this->config->get('quest_title_block',    'site');
			$data['count_question']       = $this->config->get('count_question',       'site');
			
			$data['question_visible_list'] = $this->question->getList(1);
			if(!empty($data['question_visible_list'])) {
				$data['header_visible'] = '<h2>Активные опросы</h2>';
				foreach($data['question_visible_list'] as $i => &$item) {
					$this->getFormatData(&$item);
				}
			}
			$data['question_unvisible_list'] = $this->question->getList(0);
			if(!empty($data['question_unvisible_list'])) {
				$data['header_unvisible'] = '<h2>Неактивные опросы</h2>';
				foreach($data['question_unvisible_list'] as $i => &$item) {
					$this->getFormatData(&$item);
				}
			}
			
			$this->html->render('question/index.html', $data, 'content_path');
		}
		
		public function item($id = false) {
			if(empty($id)) {
				// -- новый опрос
				$data = array(
					'id' => 0,
					'answer_list' => array(array('answer_id'=>'0','answer'=>'')),
					'active'  => 'checked="checked"',
					'next_id' => $this->question->getNextIdAnswer()
				);
			} else {
				// -- редактируем опрос
				$data = $this->question->getItem($id);
				if(empty($data)) {
					$this->url->redirect('::referer');
				}
				$data['date_begin']  = date('Y-m-d', strtotime($data['date_begin']));
				$data['date_end']    = date('Y-m-d', strtotime($data['date_end']));
				$data['answer_list'] = $this->question->getAnswerList($id);
				$data['active']      = $data['active'] ? 'checked="checked"' : '';
				$data['next_id']     = $this->question->getNextIdAnswer();
			}
			$this->html->render('question/save.html', $data, 'content_path');
		}
		
		public function save() {
			if(empty($this->data['question']['question']) || empty($this->data['answer'])) {
				$this->url->redirect('::referer');
			}
			if (!empty($this->data['question']["date_end"])) {
				$this->data['question']["date_end"]	=	date('Y-m-d 00:00:00', strtotime($this->data['question']["date_end"]));				
			}			
			if (!empty($this->data['question']["date_begin"])) {
				$this->data['question']["date_begin"]	=	date('Y-m-d 00:00:00', strtotime($this->data['question']["date_begin"]));
			}
			
			$id = $this->question->save($this->data);
			if($id) {
				foreach($this->data['answer'] as $answer_id => &$item) {
					$data = array(
						'pid'    => $id,
						'answer' => $item['answer'],
						'count'  => $item['count'],
						'sort'   => $answer_id,
					);
					if(!empty($this->data['question']['id']) && $this->question->issetAnswer($answer_id)) {
						$this->db->update('question_answer', $data, $answer_id);
					} else {
						$this->db->insert('question_answer', $data);
					}
				}
			}
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('/admin/question/item/'.$id.'/');
		}
		
		// -- ajax добавление ответа
		public function add_answer($pid) {
			$id = $this->db->insert('question_answer', array('pid' => $pid));
			$this->db->update('question_answer', array('sort' => $id), $id);
			echo $id;
			die();
		}
		
		// -- ajax удаление ответа
		public function del_answer($id) {
			$this->db->delete('question_answer', $id);
			die();
		}
		
		// -- редактируем данные раздела
		public function edit_main() {
			$this->config_controller->add_config(array('site' => $this->data));
			$this->session->set('alert', ALERT_CHANGE_DATA);
			$this->url->redirect('::referer');
		}
		
		public function delete($id) {
			if(!empty($id)) {
				$this->db->delete('question', $id);
				$this->db->delete('question_ip', array('pid' => $id));
				$this->db->delete('question_answer', array('pid' => $id));
			}
			$this->url->redirect('::referer');
		}
		
		// -- преобразование данных для вывода в список
		private function getFormatData(&$item) {
			$item['question']   = strip_tags($item['question']);
			if(mb_strlen($item['question'], 'utf-8') > 65) {
				$item['question'] = mb_substr($item['question'], 0, 62, 'utf-8').'...';
			}
			$item['date_begin'] = join('.',array_reverse(explode('-',substr($item['date_begin'],0,10))));
			$item['date_end']   = join('.',array_reverse(explode('-',substr($item['date_end'],0,10))));
		}
		
	}
?>
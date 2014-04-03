<?php
	class question_controller extends application_controller {
		
		// private $time_to_block = 7200; // 2 часа
		// private $user_ip = null;
		
		public function __construct() {
			parent::__construct();
			$this->user_ip = (empty($_SERVER['REMOTE_ADDR'])) ? '0.0.0.0' : $_SERVER['REMOTE_ADDR'];
		}
		
		public function index($page = 1) {
			$count = $this->config->get('count_question', 'site');
			$data['list'] = $this->question->getQuestionList($page, $count);
			if(!empty($data['list'])) {
				$html = '';
				foreach($data['list'] as $i => &$item) {
					$item['question'] = substr($item['question'], 3, -4);
					$item['question_info'] = '';
					if(!empty($item['sum'])) {
						$item['question_info'] .= '<small class="q_info">В опросе участвовал'.($item['sum'] > 0 ? 'о' : '').' '.$item['sum'].' человек'.($item['sum'] % 10 > 1 && $item['sum'] % 10 < 5 ? 'а' : '');
						if($item['date_end'] < date('Y-m-d')) {
							$item['date_begin'] = $this->date->format($item['date_begin']);
							$item['date_end']   = $this->date->format($item['date_end']);
							$item['question_info'] .= ', проводился с '.$item['date_begin'].' по '.$item['date_end'];
						}
						$item['question_info'] .= '</small>';
					}
					$html .= $this->html->render('question/one_question.html', $item);
				}
				$this->html->tpl_vars['question_list'] = $html;
				unset($html);
				
			}
			$data['title'] = $this->config->get('quest_title_category', 'site');
			$this->html->render('question/index.html', $data, 'content');
		}
		
		// -- делаем блок
		public function makeBlock() {
			$data = $this->question->getQuestion($this->user_ip, $this->time_to_block);
			// -- если нет неотвеченных опросов, показываем результаты последнего
			if(empty($data)) {
				$data = $this->question->getResultLastQuestion();
				if(!empty($data['answer_list'])) {
					foreach($data['answer_list'] as $i => &$item) {
						$item['prc'] = round($item['prc']).'%';
					}
					$data['title'] = $this->config->get('quest_title_block', 'site');
					$this->html->render('question/block_result.html', $data, 'question_block');
				}
				return true;
			}
			
			$data['title'] = $this->config->get('quest_title_block', 'site');
			// var_dump($data);
			// die();
			$this->html->render('question/block_ask.html', $data, 'question_block');
		}
		
		// -- обработка голоса
		public function send($pid) {
			// -- проверка входящих данных
			$id = (empty($_POST['answer'])) ? 0 : (int)$_POST['answer'];
			if(!$id || !$this->question->issetAnswer($id, $pid)) {
				$this->url->redirect('::referer');
			}
			
			// -- проверка IP
			if($this->question->blockIP($this->user_ip, $this->time_to_block, $pid)) {
				$this->url->redirect('::referer');
			}
			
			// -- учитываем голос
			$this->question->addVoice($id);
			
			// -- записываем IP
			$user_data = array(
				'ip'   => $this->user_ip,
				'pid'  => $pid,
				'time' => date('Y-m-d H:i:s'),
			);
			$this->db->insert('question_ip', $user_data);
			$this->session->set('alert', 'Спасибо, ваш голос учтён');
			$this->url->redirect('::referer');
		}

		// Заказать звонок
		public function phone(){
			echo $this->html->render('question/phone.html', array());
			die();
		}

		// Отправка письма для формы "Заказать звонок"
		public function phone_call(){
			session_start();
			if ((!empty($_POST['capcha'])) && ($_POST['capcha'] != $_SESSION['captcha_cap'])) {
				$message = 'Вы ввели неверный код';
				$this->session->set('alert', $message);
			}

			elseif(!empty($_POST)){
				$quest = array(
					'fio'       => strip_tags($_POST['fio']),
					'phone'  => strip_tags($_POST['phone']),
					'email' => strip_tags($_POST['email'])
				);

				$letter  = $this->html->render('question/phone_call.html', $quest);
				$subject = 'Заказ звонка';
				$to      = $this->config->get('contact_email', 'site');

				$this->mail->send_mail($to, $letter, $subject);

				$message = 'Ваше сообщение успешно отправлено!';
				$this->session->set('alert', $message);
			}
			$this->url->redirect('::referer');
		}

		// Запись на консультацию
		public function sign_up(){
			echo $this->html->render('question/sign_up.html', array());
			die();
		}

		// Отправка письма для формы "Запись на консультацию"
		public function sign_up_send(){
			session_start();
			if ((!empty($_POST['capcha'])) && ($_POST['capcha'] != $_SESSION['captcha_cap'])) {
				$message = 'Вы ввели неверный код';
				$this->session->set('alert', $message);
			}

			elseif(!empty($_POST)){
				$quest = array(
					'fio'       => strip_tags($_POST['fio']),
					'phone'  => strip_tags($_POST['phone']),
					'email' => strip_tags($_POST['email'])
				);

				$letter  = $this->html->render('question/sign_up_send.html', $quest);
				$subject = 'Запись на консультацию';
				$to      = $this->config->get('contact_email', 'site');

				$this->mail->send_mail($to, $letter, $subject);

				$message = 'Ваше сообщение успешно отправлено!';
				$this->session->set('alert', $message);
			}
			$this->url->redirect('::referer');
		}
		
	}
?>
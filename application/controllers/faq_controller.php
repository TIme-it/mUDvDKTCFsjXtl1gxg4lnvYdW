<?php
	class faq_controller extends application_controller {

		protected $path; 
		protected $count; 
		private $module_id = 4;
		
		public function __construct() {
			parent::__construct();
			$this->path  = $this->config->get('faq', 'files');
			$this->count = $this->config->get('faq_count','site');
		}

		public function index($pid) {

			// -- получаем данные по разделу
			$faq = $this->faq->getMainInfo($pid);
			if(empty($faq)) {
				$this->main_controller->page_404();
				return false;
			}
			
			if ($faq['module'] <> $this->module_id) {
				$this->main_controller->page_404();
				return false;
			}
			
			$faq['config'] = unserialize($faq['config']);
			$out_valid     = (bool)$faq['config']['out_valid'];
			
			// -- помечаем активный раздел
			$this->active_main_id = $pid;
			
			// -- SEO
			$this->html->tpl_vars['meta_description'] = $faq['description'];
			$this->html->tpl_vars['meta_keywords']    = $faq['keywords'];
			
			$faq['pageTitle'] = $faq['title_page'];
			$this->title .= ' | '.$faq['pageTitle'];
			
			// -- текст страницы
			$faq['text'] = '';
			if(file_exists($this->path.$pid.'_volume.txt')) {
				$faq['text'] = file_get_contents($this->path.$pid.'_volume.txt');
			}
			$is_long_text = (mb_strlen($faq['text'], 'UTF-8') > 300);
			
			
			
			// -- добавляем Яндекс.Карты, если нужно
			$this->all_controller->buildYandexMaps($pid, 0, $this->title);
			
			// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
			$this->all_controller->buildModulesBlock($pid, 0, $faq['title'], $is_long_text);

			
			$faq['header_faq'] = $this->config->get('header_faq','site');
			$faq['ask_question_form'] = $this->html->render('faqs/ask_quest_form.html',$faq);
			
			// -- получение и обработка списка
			$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
			$faq_count = $this->config->get('faq_count', 'site');
			$faq_all_count = $this->faq->getFaqsCount($pid, !$out_valid);
			$faq['pagination'] = $this->pagination_controller->index_ajax($faq_all_count, $faq_count, $page, 'faq_ajax', ','.$pid);
			
			$faq['list'] = $this->faq->getFaqs($pid, $page-1, $faq_count, !$out_valid);
			if(!empty($faq['list'])) {
				$faq['start'] = 1;
				$template     = (empty($faq['template'])) ? 'layoutFaq' : $faq['template'];
				$template_num = ($template == 'layoutFaq2') ? '2' : '';
				$faq['list'][0]['first'] = true;
				foreach($faq['list'] as $i => &$item) {

					$item['num'] = $faq['start'] + $i;
					// $item['fioUser'] = $this->morph->get_all($item['fioUser']);
					// $item['fioUser'] = $item['fioUser']['r'];
					// $item['fioSpecialist'] = $this->morph->get_all($item['fioSpecialist']);
					// $item['fioSpecialist'] = $item['fioSpecialist']['r'];
					if(!empty($item['answer'])) {
						$item['dateAnswer'] = $this->date->format($item['dateAnswer']);
						
						if ( mb_strlen($item['answer'],'UTF-8')>400 ) {
							$item['short'] = mb_substr($item['answer'],0,400,'UTF-8');
							if (preg_match('/^(.*)\.\s/i', $item['short'], $match)) {
								$item['short'] = $match[0];
							}else {
								$item['short'] .= '...';
							};
							
							//Ссылка на полный ответ
							$item['full_answer'] = '/faq/one_view/'.$item['id'];
						}
						
						$item['isAnswer']   = $this->html->render('faqs/answer'.$template_num.'.html', $item);
					}
					$item['dateQuestion'] = $this->date->format5($item['dateQuestion']);
					$item['last'] = ($i+1 == count($faq['list'])) ? true : false;
				}
				$this->html->render('faqs/listFaq'.$template_num.'.html', $faq, 'faqList');
				if($template_num == '2') {
					$this->html->render('faqs/listFaqPrev2.html', $faq, 'faqListPrev');
				}
			} else if (empty($faq['text'])) $faq['text'] = '<p>&nbsp;</p>';
			
			// -- дополнительные модули
			$faq['galleryBlock'] = $this->all_controller->images($pid, 0);
			$faq['filesBlock']   = $this->all_controller->files($pid,  0);
			$this->layout='pages';
			// -- основной рендер
			$this->html->render('faqs/layoutFaq.html', $faq, 'content');
		}
		
		
		public function faq_ajax() {

			$pid = $_POST['pid'];
			$page = $_POST['page'];

			$res=array();
			$faq = $this->faq->getMainInfo($pid);
			$faq['config'] = unserialize($faq['config']);
			$out_valid     = (bool)$faq['config']['out_valid'];
			
			$faq_count = $this->config->get('faq_count', 'site');
			$faq_all_count = $this->faq->getFaqsCount($pid, !$out_valid);
			$faq['pagination'] = $this->pagination_controller->index_ajax($faq_all_count, $faq_count, $page, 'faq_ajax', ','.$pid);
			
			$faq['list'] = $this->faq->getFaqs($pid, $page-1, $faq_count, !$out_valid);
			if(!empty($faq['list'])) {
				$faq['start'] = (($page-1) * $this->count) + 1;
				$template     = (empty($faq['template'])) ? 'layoutFaq' : $faq['template'];
				$template_num = ($template == 'layoutFaq2') ? '2' : '';
				foreach($faq['list'] as $i => &$item) {
					$item['num'] = $faq['start'] + $i;
					if(!empty($item['answer'])) {
						$item['dateAnswer'] = $this->date->format($item['dateAnswer']);
						
						if ( mb_strlen($item['answer'],'UTF-8')>400 ) {
							$item['short'] = mb_substr($item['answer'],0,400,'UTF-8');
							if (preg_match('/^(.*)\.\s/i', $item['short'], $match)) {
								$item['short'] = $match[0];
							}else {
								$item['short'] .= '...';
							};
							
							//Ссылка на полный ответ
							$item['full_answer'] = '/faq/one_view/'.$item['id'];
						}
						
						$item['isAnswer']   = $this->html->render('faqs/answer'.$template_num.'.html', $item);
					}
					$item['dateQuestion'] = $this->date->format5($item['dateQuestion']);
					$item['last'] = ($i+1 == count($faq['list'])) ? true : false;
				}
				$res['faqList'] = $this->html->render('faqs/listFaq'.$template_num.'.html', $faq);
				if($template_num == '2') {
					$res['faqListPrev'] = $this->html->render('faqs/listFaqPrev2.html', $faq);
				} else $res['faqListPrev'] = '';
			}
			echo json_encode($this->html->render('faqs/listFaq'.$template_num.'.html', $faq));
			die();
			// die(json_encode($res));
		}
		
		//Просмотр одного вопроса
		public function one_view($id) {
			// -- получаем данные по разделу
			$faq = $this->faq->getFaq((int)$id);
			if(empty($faq)) {
				$this->main_controller->page_404();
				return false;
			}
			
			$this->active_main_id = $faq['pid'];
			
			$faqm = $this->faq->getMainInfo($faq['pid']);
			$faqm['config'] = unserialize($faqm['config']);
			$faq['pageTitle'] = $faqm['title_page'];
			$template     = (empty($faqm['template'])) ? 'layoutFaq' : $faqm['template'];
			$template_num = ($template == 'layoutFaq2') ? '2' : '';
			
			if(!empty($faq['answer'])) {
				$faq['dateAnswer'] = $this->date->format($faq['dateAnswer']);
				$faq['isAnswer']   = $this->html->render('faqs/answer'.$template_num.'.html', $faq);
			}
			$faq['dateQuestion'] = $this->date->format($faq['dateQuestion']);
			
			// -- основной рендер
			$this->html->render('faqs/one_faq.html', $faq, 'content');
		}

		public function sendQuestion() {
			// -- валидация на каптчу
			// if($_POST['captcha'] !== $this->session->get('captcha_faq')) {
			// 	$this->session->set('alert', 'Вы не верно ввели код с картинки');
			// 	$this->url->redirect('::referer');
			// }
			session_start();
			$pid = (int)$_POST['pid'];

			if (((!empty($_POST['capcha'])) && ($_POST['capcha'] != $_SESSION['captcha_cap'])) || (empty($_POST['capcha']))) {
				$this->session->set('alert', 'Вы ввели неверный код');
				$this->url->redirect('::referer');
			}
			
			// -- подготовка данных
			$faq = array(
				'ip'           => (!empty($_SERVER['HTTP_X_FORWARED_FOR'])) ? $_SERVER['HTTP_X_FORWARED_FOR'] : $_SERVER['REMOTE_ADDR'],
				'fioUser'      => (!empty($_POST['fio']))  ? trim($_POST['fio'])  : false,
				// 'email'        => (!empty($_POST['email']))    ? trim($_POST['email'])    : false,
				// 'phone'        => (!empty($_POST['phone']))    ? trim($_POST['phone'])    : false,
				'question'     => (!empty($_POST['question'])) ? trim($_POST['question']) : false,
				'dateQuestion' => $this->date->sql_format(time(), true),
				'feedback'     => (int)$_POST['feedback'],
				'pid'          => $pid,
				// 'company'	   => (!empty($_POST['company']))  ? trim($_POST['company'])  : false,
			);
			
			// -- валидация на заполненность
			// if(!$faq['fioUser'] || !$faq['email']) {
			if(!$faq['fioUser']) {
				$this->session->set('alert', 'Некоторые поля были не заполнены');
				$this->url->redirect('::referer');
			}
			
			// -- извлечение конфига
			$config = $this->faq->getConfig($pid);

			// -- режим "вопросы добавляются сразу"
			if(!empty($config['out_valid'])) {
				$faq['active'] = 1;
			}
			
			if(!($id = $this->db->insert('faq', $faq))) {
				$this->session->set('alert', 'Возникла ошибка при добавлении вопроса');
				$this->url->redirect('::referer');
			}
			$_POST['id'] = $id;
			
			// -- режим "отправляем админу уведомление"
			$config['send_notice'] = $this->config->get('contact_email', 'site');
			if(!empty($config['send_notice'])) {
				$to_email = $this->config->get('contact_email', 'site');
				$_POST['domain']       = $this->config->get('domain', 'site');
				$_POST['dateQuestion'] = $this->date->format(date('Y-m-d H:i:s'));
				$letter = $this->html->render('letters/faq_admin.html', $_POST);
				$this->mail->send_mail($to_email, $letter);
			}
			
			// OK
			$this->session->set('alert', 'Ваш вопрос успешно добавлен');
			$this->url->redirect('::referer');
		}
	}
?>
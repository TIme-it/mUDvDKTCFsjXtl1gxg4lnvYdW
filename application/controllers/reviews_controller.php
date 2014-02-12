<?php
	class reviews_controller extends application_controller {

		protected $path; 
		protected $count; 
		private $module_id = 14;
		
		public function __construct() {
			parent::__construct();
			$this->path  = $this->config->get('reviews', 'files');
			$this->count = $this->config->get('reviews_count','site');
		}

		public function index($pid) {

			// -- получаем данные по разделу
			$reviews = $this->reviews->getMainInfo($pid);
			if(empty($reviews)) {
				$this->main_controller->page_404();
				return false;
			}
			
			if ($reviews['module'] <> $this->module_id) {
				$this->main_controller->page_404();
				return false;
			}
			
			$reviews['config'] = unserialize($reviews['config']);
			$out_valid     = (bool)$reviews['config']['out_valid'];
			
			// -- помечаем активный раздел
			$this->active_main_id = $pid;
			
			// -- SEO
			$this->html->tpl_vars['meta_description'] = $reviews['description'];
			$this->html->tpl_vars['meta_keywords']    = $reviews['keywords'];
			
			$reviews['pageTitle'] = $reviews['title_page'];
			$this->title .= ' | '.$reviews['pageTitle'];
			
			// -- текст страницы
			$reviews['text'] = '';
			if(file_exists($this->path.$pid.'_volume.txt')) {
				$reviews['text'] = file_get_contents($this->path.$pid.'_volume.txt');
			}
			$is_long_text = (mb_strlen($reviews['text'], 'UTF-8') > 300);
			
			
			
			// -- добавляем Яндекс.Карты, если нужно
			$this->all_controller->buildYandexMaps($pid, 0, $this->title);
			
			// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
			$this->all_controller->buildModulesBlock($pid, 0, $reviews['title'], $is_long_text);

			
			$reviews['header_reviews'] = $this->config->get('header_reviews','site');
			$reviews['ask_question_form'] = $this->html->render('reviews/ask_quest_form.html',$reviews);
			
			// -- получение и обработка списка
			$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
			$reviews_count = $this->config->get('reviews_count', 'site');
			$reviews_all_count = $this->reviews->getReviewsCount($pid, !$out_valid);
			$reviews['pagination'] = $this->pagination_controller->index_ajax($reviews_all_count, $reviews_count, $page, 'reviews_ajax', ','.$pid);
			
			$reviews['list'] = $this->reviews->getReviews($pid, $page-1, $reviews_count, !$out_valid);
			if(!empty($reviews['list'])) {
				$reviews['start'] = 1;
				$template     = (empty($reviews['template'])) ? 'layoutReviews' : $reviews['template'];
				$template_num = ($template == 'layoutReviews2') ? '2' : '';
				$reviews['list'][0]['first'] = true;
				foreach($reviews['list'] as $i => &$item) {

					$item['num'] = $reviews['start'] + $i;
					$item['dateQuestion'] = $this->date->format5($item['dateQuestion']);
					$item['last'] = ($i+1 == count($reviews['list'])) ? true : false;
				}
				$this->html->render('reviews/listReviews'.$template_num.'.html', $reviews, 'reviewsList');
				if($template_num == '2') {
					$this->html->render('reviewss/listreviewsPrev2.html', $reviews, 'reviewsListPrev');
				}
			} else if (empty($reviews['text'])) $reviews['text'] = '<p>&nbsp;</p>';
			
			// -- дополнительные модули
			$reviews['galleryBlock'] = $this->all_controller->images($pid, 0);
			$reviews['filesBlock']   = $this->all_controller->files($pid,  0);
			$this->layout='pages';
			// -- основной рендер
			$this->html->render('reviews/layoutReviews.html', $reviews, 'content');
		}
		
		
		public function reviews_ajax() {

			$pid = $_POST['pid'];
			$page = $_POST['page'];

			$res=array();
			$reviews = $this->reviews->getMainInfo($pid);
			$reviews['config'] = unserialize($reviews['config']);
			$out_valid     = (bool)$reviews['config']['out_valid'];
			
			$reviews_count = $this->config->get('reviews_count', 'site');
			$reviews_all_count = $this->reviews->getReviewsCount($pid, !$out_valid);
			$reviews['pagination'] = $this->pagination_controller->index_ajax($reviews_all_count, $reviews_count, $page, 'reviews_ajax', ','.$pid);
			
			$reviews['list'] = $this->reviews->getReviews($pid, $page-1, $reviews_count, !$out_valid);
			if(!empty($reviews['list'])) {
				$reviews['start'] = (($page-1) * $this->count) + 1;
				$template     = (empty($reviews['template'])) ? 'layoutReviews' : $reviews['template'];
				$template_num = ($template == 'layoutReviews2') ? '2' : '';
				foreach($reviews['list'] as $i => &$item) {
					$item['num'] = $reviews['start'] + $i;
					$item['dateQuestion'] = $this->date->format5($item['dateQuestion']);
					$item['last'] = ($i+1 == count($reviews['list'])) ? true : false;
				}
				$res['reviewsList'] = $this->html->render('reviews/listReviews'.$template_num.'.html', $reviews);
				if($template_num == '2') {
					$res['reviewsListPrev'] = $this->html->render('reviews/listReviewsPrev2.html', $reviews);
				} else $res['reviewsListPrev'] = '';
			}
			echo json_encode($this->html->render('reviews/listReviews'.$template_num.'.html', $reviews));
			die();
			// die(json_encode($res));
		}
		
		public function sendQuestion() {
			// -- валидация на каптчу
			// if($_POST['captcha'] !== $this->session->get('captcha_reviews')) {
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
			$reviews = array(
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
			// if(!$reviews['fioUser'] || !$reviews['email']) {
			if(!$reviews['fioUser']) {
				$this->session->set('alert', 'Некоторые поля были не заполнены');
				$this->url->redirect('::referer');
			}
			
			// -- извлечение конфига
			$config = $this->reviews->getConfig($pid);

			// -- режим "вопросы добавляются сразу"
			if(!empty($config['out_valid'])) {
				$reviews['active'] = 1;
			}
			
			if(!($id = $this->db->insert('reviews', $reviews))) {
				$this->session->set('alert', 'Возникла ошибка при добавлении отзыва');
				$this->url->redirect('::referer');
			}
			$_POST['id'] = $id;
			
			// -- режим "отправляем админу уведомление"
			$config['send_notice'] = $this->config->get('contact_email', 'site');
			if(!empty($config['send_notice'])) {
				$to_email = $this->config->get('contact_email', 'site');
				$_POST['domain']       = $this->config->get('domain', 'site');
				$_POST['dateQuestion'] = $this->date->format(date('Y-m-d H:i:s'));
				$letter = $this->html->render('letters/reviews_admin.html', $_POST);
				$this->mail->send_mail($to_email, $letter);
			}
			
			// OK
			$this->session->set('alert', 'Ваш отзыв успешно добавлен');
			$this->url->redirect('::referer');
		}
	}
?>
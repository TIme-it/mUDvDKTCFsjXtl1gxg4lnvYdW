<?php
	class all_controller extends application_controller {

		// -- вывод текстовой страницы на печать
		public function onprint($mid, $id) {
			$page = $this->all->getPageOnPrint($mid, $id);
			$path = $this->config->get($this->all->getTable($mid), 'files');
			if(empty($page)) {
				$this->url->redirect('::referer');
			}
			$page['date'] = '<p align="right">'.$this->date->format2($page['date']).'</p>';
			if(file_exists($path.$id.'.txt')) {
				$page['text'] = file_get_contents($path.$id.'.txt');
			}
			
			$title = htmlspecialchars_decode($this->config->get('title_browser','site'));
			$this->html->render('head/head_print.html', array('site_title' => $title), 'head');
			echo $this->html->render('layouts/layout_print.html', $page);
			die();
		}
	
		// -- получение списка картинкок
		public function images($pid, $mid, $page = 1, $limit = false) {
			$imgcount = $this->all->getImagesCount($pid, $mid);

			$count = $this->config->get('images_count','site');
			$image['pagination'] = $this->pagination_controller->index_ajax($imgcount, $count, $page, 'images', ','.$pid.','.$mid);
			
			/* $image['imageList1'] = $this->all->getImages1($pid, $mid, $page-1, 4);			
			 if(empty($image['imageList1'])) return false;
			
			$image['imageList'] = $this->all->getImages($pid, $mid, $page-1,4);			
			if(empty($image['imageList'])) return false;*/
			 $image['imageList1'] = $this->all->getImages1($pid, $mid, 0, 200);	
			 if(!empty($image['imageList1'])){
			 	foreach ($image['imageList1'] as $i => &$item) {
			 		$item['max_count'] = count($image['imageList1']);
			 		$item['curr_count'] = $i+1;
			 	}
			 }	
			 if(empty($image['imageList1'])) return false;
			
			$image['imageList'] = $this->all->getImages($pid, $mid, 0,5);			
			if(empty($image['imageList'])) return false;
		
			
			//Отображение заголовка галереи
			switch ($mid) {
				case 2: $has_header = $this->db->get_one('SELECT gallery_header FROM news WHERE id='.$pid); break;
				case 8: $has_header = $this->db->get_one('SELECT gallery_header FROM catalog WHERE id='.$pid); break;
				default: $has_header = false;
			}
			
			if ($has_header === false) {
				$has_header = $this->db->get_one('SELECT gallery_header FROM main WHERE id='.$pid.' AND module='.$mid);
			}
			if ($has_header) {
				$header_photo = $this->config->get('header_photo', 'site');
				$image['header_photo'] = (!empty($header_photo)) ? '<h2>'.$header_photo.'</h2>' : '';
			}
			
			foreach($image['imageList'] as $i => &$item) {
				$item['b_width']  = (empty($item['b_width'])) ? 820 : $item['b_width']  + 20;
				$item['b_height'] = (empty($item['b_width'])) ? 620 : $item['b_height'] + 20;
				$item['is_active'] = ($i == 0) ? true : false;
			}
			
			$image['id'] = $image['imageList'][0]['id'];
			
			$type = $this->base->getFotogallery($pid, $mid, $limit);
			
			/*switch($type) {
				default: // -- с затемнением экрана (бывш. ajax_gallery)
					$image['width']  = $this->config->get('width_small','images').'px';
					$image['height'] = $this->config->get('height_small','images').'px';
					
					$image['ajax']   = 'class="ajaxGallery"';
					$this->html->render('photo/layoutPhoto1.html', $image, 'ajaxGalleryBlock');
					$result = $this->getLayoutPhoto($image, $type);
					// -- ?show={id} - обработка URL для отображения конкретной картинки
					if(!empty($_GET['show'])) {
						$result .= '<script type="text/javascript">$(document).ready(function() { showImg('.(int)$_GET['show'].'); });</script>';
					}
					return '<a name="galereya"></a>'.$result;
				case 2: // -- стандартная (бывш. gallery)
					return $this->getLayoutPhoto($image, $type);
				case 3: // -- стандартная с прокруткой (бывш. gallery_scroll)
					$image['height'] = $image['imageList1'][0]['l_height'].'px';
					return $this->html->render('photo/layoutPhoto1.html', $image);
			}*/
				return $this->html->render('photo/layoutPhoto1.html', $image);
		}
		
		// -- получение списка картинкок
		public function images_ajax($pid, $mid, $page = 1) {
			$imgcount = $this->all->getImagesCount($pid, $mid);
			
			$count = $this->config->get('images_count','site');
			$image['pagination'] = $this->pagination_controller->index_ajax($imgcount, $count, $page, 'images_ajax', ','.$pid.','.$mid);
			
			$image['imageList1'] = $this->all->getImages1($pid, $mid, $page-1, 16);			
			 if(empty($image['imageList1'])) return false;
			 
			$image['imageList'] = $this->all->getImages($pid, $mid, $page-1, $count);			
			if(empty($image['imageList'])) return false;
			
			//Отображение заголовка галереи
			switch ($mid) {
				case 2: $has_header = $this->db->get_one('SELECT gallery_header FROM news WHERE id='.$pid); break;
				case 8: $has_header = $this->db->get_one('SELECT gallery_header FROM catalog WHERE id='.$pid); break;
				default: $has_header = false;
			}
			
			if ($has_header === false) {
				$has_header = $this->db->get_one('SELECT gallery_header FROM main WHERE id='.$pid.' AND module='.$mid);
			}
			if ($has_header) {
				$header_photo = $this->config->get('header_photo', 'site');
				$image['header_photo'] = (!empty($header_photo)) ? '<h1>'.$header_photo.'</h1>' : '';
			}
			
			foreach($image['imageList'] as $i => &$item) {
				$item['b_width']  = (empty($item['b_width'])) ? 820 : $item['b_width']  + 20;
				$item['b_height'] = (empty($item['b_width'])) ? 620 : $item['b_height'] + 20;
			}
			
			$image['id'] = $image['imageList'][0]['id'];
			
			$type = $this->base->getFotogallery($pid, $mid);
			
			switch($type) {
				default: // -- с затемнением экрана (бывш. ajax_gallery)
					$image['width']  = $this->config->get('width_small','images').'px';
					$image['height'] = $this->config->get('height_small','images').'px';
					
					$image['ajax']   = 'class="ajaxGallery"';
					$this->html->render('photo/layoutPhoto1.html', $image, 'ajaxGalleryBlock');
					$result = $this->getLayoutPhoto($image, $type);
					// -- ?show={id} - обработка URL для отображения конкретной картинки
					if(!empty($_GET['show'])) {
						$result .= '<script type="text/javascript">$(document).ready(function() { showImg('.(int)$_GET['show'].'); });</script>';
					}
					die(json_encode($result));
				case 2: // -- стандартная (бывш. gallery)
					die(json_encode($this->getLayoutPhoto($image, $type)));
				case 3: // -- стандартная с прокруткой (бывш. gallery_scroll)
					$image['height'] = $image['imageList1'][0]['l_height'].'px';
					die(json_encode($this->html->render('photo/layoutPhoto1.html', $image)));
			}
		}		
		
		private function getLayoutPhoto($data, $type) {
			if($type == 2) {
				foreach($data['imageList'] as $i => &$item) {
					$item['onclick'] = 'onClick="window.open(\'/application/includes/img/b/'.$item['id'].'.jpg\',\'image\',\'resizable=yes,width='.$item['b_width'].',height='.$item['b_height'].'\'); return false;"';
				}
			}
			return $this->html->render('photo/layoutPhoto.html', $data);
		}

		//Desc: Получение списка файлов
		//Return: Возвращает шаблон
		// Стандартный шаблон layouts/files.html
		public function files($pid,$module_id) {
			$header_files = $this->config->get('header_files', 'site');
			$files['header_files'] = (empty($header_files)) ? '' : '<h2>'.$header_files.'</h2>';
			$files['files_list']   = $this->all->getFiles($pid,$module_id);
			if(!empty($files['files_list'])) {
				foreach($files['files_list'] as $key => &$item) {
					$ext_name     = $item['extension'];
					$item['date'] = $this->date->format2($item['date']);
					$item['ext']  = (file_exists(INCLUDES.'images'.DS.'ext'.DS.$ext_name.'.png')) ? $ext_name : 'nlo';
					$item['fullfilename'] = $item['filename'];
					if (mb_strlen($item['filename'], 'UTF-8') > 15) {
						$item['filename'] = mb_substr($item['filename'], 0, 12, 'UTF-8').'...';
					}
					$item['filesize'] = round($item['filesize']/1024).' кб.';
				}
				return $this->html->render('pages/files.html',$files);
			}
			return false;
		}
		
		// -- вывод видео YouTube
		public function video($pid, $mid) {
			$count = $this->all->getVideoCount($pid, $mid);
			if($count == 0) return '';
			
				$videos = $this->all->getVideo($pid, $mid);
				$first  = $videos[0];
				return $this->html->render('video/main.html', $first);
			
			//var_dump($count);
			//$header_files = $this->config->get('header_files', 'site');
			//$files['header_files'] = (empty($header_files)) ? '' : '<h2>'.$header_files.'</h2>';
			// $video['files_list']   = $this->all->getFiles($pid,$module_id);
			// if(!empty($files['files_list'])) {
				// foreach($files['files_list'] as $key => &$item) {
					// $ext_name     = $item['extension'];
					// $item['date'] = $this->date->format($item['date']);
					// $item['ext']  = (file_exists(INCLUDES.'images'.DS.'ext'.DS.$ext_name.'.gif')) ? $ext_name : 'nlo';
					// if(mb_strlen($item['filename'], 'utf-8') > 80) {
						// $item['filename'] = mb_substr($item['filename'], 0, 77, 'utf-8').'...';
					// }
					// $item['filesize'] = round($item['filesize']/1024).' Кб';
				// }
				// return $this->html->render('pages/files.html',$files);
			// }
			// return false;
		}

		public function video_js($id) {
			$video = $this->all->getVideoOne($id);
			echo $this->html->render('/video/main.js', $video);
			die();
		}
		
		//Desc: ссылка скачать файл
		//Return: посылает header для скачки файла
		public function download($id) {
			$info = $this->all->getFileInfo($id);
			//var_dump($info);
			$this->file->download($info,$this->config->get('file','files'));
			
			die();
		}
		
		public function gallery_ajax($id,$pid) {
			$image['image_list'] = $this->all->getImages($pid,1);
			$image['id'] = $id;
			$image['height'] = $image['image_list'][0]['l_height'].'px';
			die($this->html->render('layouts/ajax_gallery_open.html',$image));
			exit();
		}
		
		// -- модификация текста: замена текстовых меток на контейнер Яндекс.Карты
		// -- метки в тексте могут быть, могут не быть (в этом случае карты вставляются в конец текста)
		public function buildYandexMaps($pid, $mid, $title = '') {
			$map['map_list'] = $this->all->getMaps($pid, $mid);
			if(empty($map['map_list'])) return false;
			
			$this->html->tpl_vars['yandexmaps'] = '<div class="map_wrap">';
			foreach($map['map_list'] as $i => &$item) {
				// -- $item['map_title'] - собственно метка
				$prepare = '<div class="maps" id="map_'.$item['map_id'].'"></div>';
				
				$this->html->tpl_vars['yandexmaps'] .= '<p>'.$prepare.'</p>';
				$item['placemark_list'] = trim($this->html->render('placemark/item.js', $item));
			}
			if (empty($title)) $map['site_title'] = htmlspecialchars_decode($this->config->get('title_browser','site'));
			else $map['site_title'] = $title;
			$this->html->tpl_vars['yandexmaps'] .= '</div>';
			
			$map['mapkey'] = $this->config->get($_SERVER['SERVER_NAME'], 'mapkeys'); // -- ключ
			$this->html->render('head/head_yandex.html', $map, 'head');
		}
		
		// -- формирования данных для блока "модули"
		public function buildModulesBlock($pid, $mid, $title, $is_long_text = true, $hid_model_id = false) {

			// -- сквозные параметры
			$header_subsection = $this->config->get('header_subsection', 'site');
			$header_feedback   = $this->config->get('header_feedback',   'site');
			
			$page = $this->all->getModulesBlockInfo($pid, $mid);

			$page['pid'] = (int)$pid;
			$page['mid'] = $mid;
			$page['print_word'] = $this->config->get('print_word',        'site');
			
			// -- добавляем "обратная связь", если нужно
			if(!empty($page['feedback'])) {
				$page['header_feedback'] = $header_feedback;
				$this->html->render('pages/feedback.html', $page, 'feedbackBlock');
				// -- добавляем возможность отправить файл, если нужно
				if(!empty($page['sendfile'])) {
					$this->html->render('pages/feedback_file.html', $page, 'feedbackBlock');
				}
			}
			
			
			if ( !empty($page['source']) && ($url = $this->checkurl($page['source']))==0 )
				$page['source'] = '<a href="'.$url.'">'.$page['source'].'</a>';
			
			if (!empty($page['date'])) {
				$ts = (!is_numeric($page['date'])) ? strtotime($page['date']) : $page['date'];
				$page['date'] = $this->date->format3($ts);
			}
			if(!$is_long_text) unset($page['print']);				//Нужно ли??
			
			if ( !empty($page['source']) || !empty($page['print']) || ($page['is_show_date']) )
				$this->html->render('pages/text_info.html',$page,'text_info');
			
			// -- добавляем "подразделы", если это нужно
			if(!empty($page['subsection'])) {
				$subsection['list'] = $this->menu->getSubsection($pid);
				if(!empty($subsection['list'])) {
					$subsection['title_subsection'] = '';
					if(!empty($header_subsection)) {
						$subsection['title_subsection'] = '<h2>'.str_replace('%NAME%', $title, $header_subsection.'</h2>');
					}
					$this->html->render('pages/subsection.html', $subsection, 'subsectionBlock');
				}
			}
		}
		
		// -- обработка $_POST от формы подмодуля "Отзывы на странице" и редирект на рефёрер
		public function recalls() {
			// -- валидация на каптчу
			if($this->session->get('captcha_recalls') !== $_POST['captcha']) {
				$this->session->set('alert', 'Вы не верно ввели код с картинки');
				$this->url->redirect('::referer');
			}
			// -- форматирование входящих данных
			$data['fio']   = (!empty($_POST['fio'])   && trim($_POST['fio'])   != '') ? strip_tags(trim($_POST['fio']))   : false;
			$data['email'] = (!empty($_POST['email']) && trim($_POST['email']) != '') ? strip_tags(trim($_POST['email'])) : false;
			$data['text']  = (!empty($_POST['text'])  && trim($_POST['text'])  != '') ? strip_tags(trim($_POST['text']))  : false;
			$data['pid']   = (!empty($_POST['pid']))   ? (int)$_POST['pid']     : false;
			
			// -- валидация на незаполненность
			if(!$data['fio'] || !$data['email'] || !$data['text'] || !$data['pid']) {
				$this->session->set('alert', 'Некоторые поля были не заполнены');
				$this->url->redirect('::referer');
			}
			// -- добавление в БД
			if(!$this->db->insert('recalls', $data)) {
				$this->session->set('alert', 'При добавлении вопроса произошла ошибка');
				$this->url->redirect('::referer');
			}
			// -- ОК
			$this->session->set('alert', 'Ваш вопрос был успешно добавлен');
			$this->url->redirect('::referer');
		}

		// -- обработка формы подмодуля "Обратная связь" + отправка файла (если это включено)
		public function feedback() {
			// -- валидация на каптчу
			// if($this->session->get('captcha_feedback') !== $_POST['captcha']) {
			// 	$this->session->set('alert', 'Вы не верно ввели код с картинки');
			// 	$this->url->redirect('::referer');
			// }
			session_start();
			if ((!empty($_POST['capcha'])) && ($_POST['capcha'] != $_SESSION['captcha_cap'])) {
				$this->session->set('alert', 'Вы ввели неверный код');
				$this->url->redirect('::referer');
			}

			// -- форматирование входящих данных
			$data['surname']   = (!empty($_POST['surname'])   && trim($_POST['surname'])   != '') ? strip_tags(trim($_POST['surname']))         : false;
			$data['name']   = (!empty($_POST['name'])   && trim($_POST['name'])   != '') ? strip_tags(trim($_POST['name']))         : false;
			$data['secname']   = (!empty($_POST['secname'])   && trim($_POST['secname'])   != '') ? strip_tags(trim($_POST['secname']))         : false;
			$data['email'] = (!empty($_POST['email']) && trim($_POST['email']) != '') ? strip_tags(trim($_POST['email']))       : false;
			$data['question'] = (!empty($_POST['question']) && trim($_POST['question']) != '') ? strip_tags(trim($_POST['question']))       : false;
			$data['phone']  = (!empty($_POST['phone'])  && trim($_POST['phone'])  != '') ? nl2br(strip_tags(trim($_POST['phone']))) : false;

			// -- валидация на незаполненность
			if(!$data['surname'] || !$data['name'] || !$data['secname'] || !$data['email'] || !$data['question']) {
				$this->session->set('alert', 'Некоторые поля были не заполнены');
				$this->url->redirect('::referer');
			}
			// -- отправка письма
			$sended = false;
			$letter = $this->html->render('letters/feedback.html', $data);
			$email  = $this->config->get('feedback_email', 'site');
			if(isset($_FILES['file']['error'])) {
				if(isset($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name'])) {
					$sended = $this->mail->send_mail($email, $letter, false, array($_FILES['file']['name'] => $_FILES['file']['tmp_name']));				
				} else{
					$sended = $this->mail->send_mail($email, $letter);
				}
			} else {
				$sended = $this->mail->send_mail($email, $letter);
			}
			// -- формирование сообщения + редирект
			$message = $sended ? 'Ваше сообщение было успешно отправлено' : 'При отправке сообщения произошла ошибка';
			$this->session->set('alert', $message);
			$this->url->redirect('::referer');
		}

		// -- обработка формы "Записаться"
		public function signup() {
			session_start();
			if ((!empty($_POST['capcha'])) && ($_POST['capcha'] != $_SESSION['captcha_cap'])) {
				$this->session->set('alert', 'Вы ввели неверный код');
				$this->url->redirect('::referer');
			}

			// -- форматирование входящих данных
			$data['fio']   = (!empty($_POST['fio'])   && trim($_POST['fio'])   != '') ? strip_tags(trim($_POST['fio']))         : false;
			$data['email'] = (!empty($_POST['email']) && trim($_POST['email']) != '') ? strip_tags(trim($_POST['email']))       : false;
			$data['phone']  = (!empty($_POST['phone'])  && trim($_POST['phone'])  != '') ? nl2br(strip_tags(trim($_POST['phone']))) : false;
			$data['course_title']  = (!empty($_POST['course_title'])  && trim($_POST['course_title'])  != '') ? nl2br(strip_tags(trim($_POST['course_title']))) : false;

			// -- валидация на незаполненность
			if(!$data['fio'] || !$data['email'] || !$data['phone']) {
				$this->session->set('alert', 'Некоторые поля были не заполнены');
				$this->url->redirect('::referer');
			}
			// -- отправка письма
			$sended = false;
			$letter = $this->html->render('letters/signup.html', $data);
			$email  = $this->config->get('feedback_email', 'site');
			if(isset($_FILES['file']['error'])) {
				if(isset($_FILES['file']['tmp_name']) && file_exists($_FILES['file']['tmp_name'])) {
					$sended = $this->mail->send_mail($email, $letter, false, array($_FILES['file']['name'] => $_FILES['file']['tmp_name']));				
				} else{
					$sended = $this->mail->send_mail($email, $letter);
				}
			} else {
				$sended = $this->mail->send_mail($email, $letter);
			}
			// -- формирование сообщения + редирект
			$message = $sended ? 'Ваше сообщение было успешно отправлено' : 'При отправке сообщения произошла ошибка';
			$this->session->set('alert', $message);
			$this->url->redirect('::referer');
		}

		public function footer_feedback(){
			// -- форматирование входящих данных
			$data['footer_fio']   = (!empty($_POST['footer_fio'])   && trim($_POST['footer_fio'])   != '') ? strip_tags(trim($_POST['footer_fio']))         : false;
			$data['footer_phone'] = (!empty($_POST['footer_phone']) && trim($_POST['footer_phone']) != '') ? strip_tags(trim($_POST['footer_phone']))       : false;
			// -- валидация на незаполненность
			if(!$data['footer_fio'] || !$data['footer_phone']) {
				$this->session->set('alert', 'Некоторые поля были не заполнены');
				$this->url->redirect('::referer');
			}
			// -- отправка письма
			$sended = false;
			$letter = $this->html->render('letters/footer_feedback.html', $data);
			$email  = $this->config->get('feedback_email', 'site');

			$sended = $this->mail->send_mail($email, $letter);

			// -- формирование сообщения + редирект
			$message = $sended ? 'Ваш заказ был успешно отправлен' : 'При отправке заказа произошла ошибка';
			$this->session->set('alert', $message);
			$this->url->redirect('::referer');
		}
		
		
		
	
		// проверяет URL и возвращает: 
		// * +1, если URL пуст 
		// 		if (checkurl($url)==1) echo "пусто" 
		// * -1, если URL не пуст, но с ошибками 
		// 		if (checkurl($url)==-1) echo "ошибка" 
		// * строку (новый URL), если URL найден и отпарсен 
		// 		if (checkurl($url)==0) echo "все ок" 
		// либо if (strlen(checkurl($url))&gt;1) echo "все ок" 
		// 
		// Если протокола не было в URL, он будет добавлен ("http://") // 
		function checkurl($url) { 
			// режем левые символы и крайние пробелы 
			$url=trim(preg_replace("/[^\x20-\xFF]/","",@strval($url))); 
			// если пусто - выход 
			if (strlen($url)==0) return 1; 
			//проверяем УРЛ на правильность 
			if (!preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}". "(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|". "org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?". "!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&amp;". "?+=\~/-]*)?(?:#[^ '\"&amp;&lt;&gt;]*)?$~i",$url,$ok)) return -1; 
			// если не правильно - выход 
			// если нет протокала - добавить 
			if (!strstr($url,"://")) $url="http://".$url; 
			// заменить протокол на нижний регистр: hTtP -&gt; http 
			$url=preg_replace("~^[a-z]+~ie","strtolower('\\0')",$url); 
			return $url; 
		}

		/*
		TODO: Сделать проверку на уникальность email'а, не добавлять дублей
		*/
		public function say_me() {
			$data['email'] = (!empty($_POST['email']) && trim($_POST['email']) != '') ? strip_tags(trim($_POST['email']))       : false;			
			// -- отправка письма
			$sended = false;
			$letter = $this->html->render('letters/say_me.html', $data);
			$email  = $this->config->get('feedback_email', 'site');
			$sended = $this->mail->send_mail($email, $letter);
			$this->all->save_say_me($data['email']);
			echo $sended;
			die();
		}
		
		public function addkursy($id = false) {
			if (!empty(self::$user_id))  {				
				$KursyUser	=	$this->catalog->getKursyUser($id, self::$user_id);
				if (empty($KursyUser)) {				
					$data	=	array(
						'catalog_id'	=>	(int)$id,
						'user_id'		=>	self::$user_id,						
					);
					$id = $this->db->insert('catalog_users', $data);
					
					$user = $this->profile->getUser(self::$user_id);
					$user['course_title']	=	$this->catalog->getTitleProduct((int)$id);
					$letter	=	$this->html->render('letters/signup_authuser.html', $user);
					$to			=	$this->config->get('contact_email','site');
					$subject	=	'Запись на курсы';
					$this->mail->send_mail($to, $letter, $subject);
					
				}				
			} 			
			$this->url->redirect('::referer');			
		}		
		
		public function delkursy($id = false) {
			if (!empty(self::$user_id))  {				
				$this->db->delete('catalog_users', array('catalog_id' => $id, 'user_id' => self::$user_id));						
			} 			
			$this->url->redirect('::referer');			
		}
	}
?>
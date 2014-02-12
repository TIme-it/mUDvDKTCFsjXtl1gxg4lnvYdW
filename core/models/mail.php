<?php
	class mail extends application_controller {
		
		private $handler;
		
		public function __construct() {
			$domain = $this->config->get('domain','site');
			// -- используем класс PHPMailer
			require_once LIBS.'class.phpmailer.php';
			$this->handler = new PHPMailer();

			$this->handler->IsSMTP();
			$this->handler->Host          = 'smtp.yandex.ru';			
			$this->handler->SMTPAuth      = true;
			$this->handler->SMTPKeepAlive = true;
			$this->handler->SMTPSecure 	  = 'ssl'; 
			$this->handler->Port          = 465;
			$this->handler->Username      = 'mailer@time-it.ru';
			$this->handler->Password      = 'O8D51j1i';
			
			$this->handler->Subject = 'Письмо с сайта '.$domain;
		}
		
		// -- отправить письмо
		public function send_mail($to, $letter, $subject = false, $attaches = false, $config = false) {
			$this->clear();
		
			if(!empty($config) && is_array($config)) {
				foreach($config as $conf_h => $conf_v) {
					switch($conf_h) {
						case 'From':    $this->handler->SetFrom($conf_v[0], $conf_v[1]);    break;
						case 'ReplyTo':
							$this->handler->ClearReplyTos();
							$this->handler->AddReplyTo($conf_v[0], $conf_v[1]);
							break;
					}
				}
			}
		
			// -- перекрываем тему письма
			if(!empty($subject)) {
				$this->handler->Subject = $subject;
			}
			
			// -- формируем тело письма
			$this->handler->MsgHTML($letter);
			$this->handler->AltBody = strip_tags($letter);
			
			// -- добавляем прикрепленные файлы
			if(!empty($attaches)) {
				if(is_string($attaches)) {
					if(file_exists($attaches)) {
						$this->handler->AddAttachment($attaches);
					}
				} elseif(is_array($attaches)) {
					foreach($attaches as $name => $path) {
						if(file_exists($path)) {
							$this->handler->AddAttachment($path, $name);
						}
					}
				}
			}

			$this->handler->AddAddress($to);
			return $this->handler->Send();
		}
		
		// -- очистить от предыдущей отправки
		private function clear() {
			$this->handler->ClearAddresses();
			$this->handler->ClearAttachments();
		}
		
	}
?>
<?php
	class captcha extends application_controller {

		protected $font;     // -- название шрифта
		protected $possible; // -- строка разрешенных символов

		public function __construct() {
			$this->font     = 'monofont.ttf';
			$this->possible = '23456789';
		}

		public function show($name, $width = 100, $height = 40, $characters = 5) {
			// -- создаем холст
			$image = imagecreate($width, $height);
			if(!$image) {
				die('Cannot initialize new GD image stream');
			}
			
			// -- настройка цветов RGB
			$background_color = imagecolorallocate($image, 235, 232, 227);
			$text_color       = imagecolorallocate($image,  30,  30,  30);
			$noise_color      = imagecolorallocate($image, 121, 121, 121);
			
			// -- настройка "шумов"
			$noise_prc_p   = 0.1;   // -- default: 0.1
			$noise_prc_l   = 0.001; // -- default: 0.001
			
			// -- обработка данных
			$noise_p_count = $width * $height * $noise_prc_p;
			$noise_l_count = $width * $height * $noise_prc_l;
			
			// -- генерация точек шума
			for($i = 0; $i < $noise_p_count; $i++) {
				imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
			}

			// -- генерация линий шума
			for($i = 0; $i < $noise_l_count; $i++) {
				imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noise_color);
			}
			
			// -- вычисляем размер текстового блока
			$font_size = $height / $characters * 3.5;
			$code      = $this->code($characters);
			$textbox   = imagettfbbox($font_size, 0, 'application/includes/ttf/'.$this->font, $code) or die('Error in imagettfbbox function');
			
			// -- генерация текста
			$code      = '';
			$d = ($width - 15) / $characters;
			$x = ($width  - $d * $characters)/2;
			$y = ($height - $textbox[5])/2;
			$k = rand();
			for($i = 0; $i < $characters; $i++) {
				$shift_x = $i * $d;
				$shift_y = round(sin($i+$k+0.3)*6);
				$symbol = $this->get_symbol();
				imagettftext($image, $font_size, 0, $x+$shift_x, $y+$shift_y, $text_color, 'application/includes/ttf/'.$this->font , $symbol) or die('Error in imagettftext function');
				$code .= $symbol;
			}
			
			// -- сохраняем текст в сессии
			$this->session->set('captcha_'.$name, $code);

			// -- выводим в браузер
			header('Content-Type: image/jpeg charset=UTF-8');
			imagejpeg($image);
			imagedestroy($image);
		}

		private function code($characters) {
			$code = '';
			while($characters) {
				$code .= substr($this->possible, mt_rand(0, strlen($this->possible)-1), 1);
				$characters--;
			}
			return $code;
		}
		
		private function get_symbol() {
			return substr($this->possible, mt_rand(0, strlen($this->possible)-1), 1);
		}
		
	}
?>
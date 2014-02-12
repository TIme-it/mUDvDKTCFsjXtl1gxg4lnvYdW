<?php
	class image {
		
		private $logoPath;
		private $watermarkPath;
		private $logoPad;
		
		private $type = 'jpg';
		
		public function __construct() {
			// empty
		}
		
		public function analyze($image) {
			// -- перебираем функции чтения разных форматов (pjpeg: progressive-jpeg)
			$this->type = 'jpg';
			if(!($this->mainImage = @imageCreateFromJPEG($image))) {
				$this->type = 'png';
				if(!($this->mainImage = @imageCreateFromPNG($image))) {
					$this->type = 'gif';
					if(!($this->mainImage = @imageCreateFromGIF($image))) {
						return false;
					}
				}
			}
			$this->mainImageWidth  = ImageSX($this->mainImage);
			$this->mainImageHeight = ImageSY($this->mainImage);
			$this->newImage        = $this->mainImage;
			$this->newImageWidth   = $this->mainImageWidth;
			$this->newImageHeight  = $this->mainImageHeight;
			return array(
				'b_width'  => $this->mainImageWidth,
				'b_height' => $this->mainImageHeight
			);
		}
		
		public function rotate($arc) {
			if(empty($this->mainImage)) return false;
			$background = imagecolorallocate($this->mainImage, 255, 255, 255);
			$this->newImage = imagerotate($this->mainImage, $arc, $background);
			imagedestroy($this->mainImage);
			$this->mainImage = $this->newImage;
			return true;
		}

		public function resize($width = 0, $height = 0) {
			if(empty($this->newImageWidth) || empty($this->newImageHeight)) {
				return false;
			}
			if(!$width && !$height) {
				$width  = $this->newImageWidth;
				$height = $this->newImageHeight;
			} elseif(!$width && $height > 0) {
				$persents = $height / $this->newImageHeight;
				$width    = (int)($this->newImageWidth * $persents);
			} elseif($width > 0 && !$height) {
				$persents = $width / $this->newImageWidth;
				$height   = (int)($this->newImageHeight * $persents);
			} else {
				$per_w = $width  / $this->newImageWidth;
				$per_h = $height / $this->newImageHeight;
				if($per_w < $per_h) {
					$height = (int)($this->newImageHeight * $per_w);
				} else {
					$width  = (int)($this->newImageWidth * $per_h);
				}
			}
			$tmpImage = ImageCreateTrueColor($width, $height);
			if ($this->type == 'png') {
				imageAlphaBlending($tmpImage, false);
				imageSaveAlpha($tmpImage, true);
			}
			
			imagecopyresampled($tmpImage,$this->newImage,0,0,0,0,$width,$height,$this->newImageWidth,$this->newImageHeight);
			$this->newImage       = $tmpImage;
			$this->newImageWidth  = $width;
			$this->newImageHeight = $height;
		}

		public function toBrowser() {
			switch ($this->type) {
				case 'jpg': header('Content-type: image/jpeg'); imageJPEG($this->newImage);break;
				case 'png': header('Content-type: image/png'); imagePNG($this->newImage);break;
				case 'gif': header('Content-type: image/gif'); imageGIF($this->newImage);break;
			}
		}

		public function toFile($file, $quality = 80, $width = 0, $height = 0) {
			if(empty($this->newImage)) return false;
			if($width || $height) {
				$this->cutToSize($width, $height);
			}
			
			switch ($this->type) {
				case 'jpg': imageJPEG($this->newImage, $file, (int)$quality > 0 ? (int)$quality : 80);break;
				case 'png': imagePNG($this->newImage, $file);break;
				case 'gif': imageGIF($this->newImage, $file);break;
			}
			$this->reset();
		}
		
		public function toFileWR($file, $quality = 80) {
			if(empty($this->newImage)) return false;
			switch ($this->type) {
				case 'jpg': imageJPEG($this->newImage, $file, (int)$quality > 0 ? (int)$quality : 80);break;
				case 'png': imagePNG($this->newImage, $file);break;
				case 'gif': imageGIF($this->newImage, $file);break;
			}
		}

		function cutToSize($width = false, $height = false) {
			if(!$width && !$height) return false;
			if(!$width || !$height) {
				$this->resize($width, $height);
				return true;
			}
			// -- определение по какому параметру подгонять
			$persents = $width / $this->newImageWidth;
			if($persents * $this->newImageHeight > $height) {
				$this->resize($width);
				$tmpImage = ImageCreateTrueColor($width, $height);
				
				if ($this->type == 'png') {
					imageAlphaBlending($tmpImage, false);
					imageSaveAlpha($tmpImage, true);
				}
				
				// imagecopyresampled($tmpImage,$this->newImage,0,0,0,(int)($persents*$this->mainImageHeight-$height)/2,$width,$height,$width,$height);
				imagecopyresampled($tmpImage,$this->newImage,0,0,0,0,$width,$height,$width,$height);
				$this->newImage       = $tmpImage;
				$this->newImageWidth  = $width;
				$this->newImageHeight = $height;
				return true;
			}
			$this->resize(0,$height);
			$persents=$height/$this->newImageHeight;
			$tmpImage = ImageCreateTrueColor($width,$height);
			
			if ($this->type == 'png') {
				imageAlphaBlending($tmpImage, false);
				imageSaveAlpha($tmpImage, true);
			}
			
			// imagecopyresampled($tmpImage,$this->newImage,0,0,(int)(($persents*$this->newImageWidth-$width)/2),0,$width,$height,$width,$height);	
			imagecopyresampled($tmpImage,$this->newImage,0,0,0,0,$width,$height,$width,$height);
			$this->newImage       = $tmpImage;
			$this->newImageWidth  = $width;
			$this->newImageHeight = $height;
			return true;
		}

		public function reset() {
			$this->newImage       = $this->mainImage;
			$this->newImageWidth  = $this->mainImageWidth;
			$this->newImageHeight = $this->mainImageHeight;
		}

		public function addLogo($position = 'RB') {
		/*  $position - определяет позицию логотипа на фотографии. возможнв варианты:
			LB,BL     - нижний левый угол
			RB,BR     - нижний правый угол
			LT,TL     - верхний девый угол
			RT,TR     - верхний правый угол
			CT,CN,C   - центр */
		$logo       = imageCreateFromPng($this->logoPath);
		$logoWidth  = imageSX($logo);
		$logoHeight = imageSY($logo);
		switch($position) {
			case 'LB': case 'BL':
				ImageCopyResampled($this->newImage,$logo,$this->logoPad,($this->newImageHeight-$logoHeight-$this->logoPad),0,0,$logoWidth,$logoHeight,$logoWidth,$logoHeight);
				return true;
			case 'RB': case 'BR':
				ImageCopyResampled($this->newImage,$logo,($this->newImageWidth-$logoWidth-$this->logoPad),($this->newImageHeight-$logoHeight-$this->logoPad),0,0,$logoWidth,$logoHeight,$logoWidth,$logoHeight);
				return true;
			case 'LT': case 'TL':
				ImageCopyResampled($this->newImage,$logo,$this->logoPad,$this->logoPad,0,0,$logoWidth,$logoHeight,$logoWidth,$logoHeight);
				return true;
			case 'RT': case 'TR':
				ImageCopyResampled($this->newImage,$logo,($this->newImageWidth-$logoWidth-$this->logoPad),$this->logoPad,0,0,$logoWidth,$logoHeight,$logoWidth,$logoHeight);
				return true;
			case 'CT': case 'CN': case 'C':
				ImageCopyResampled($this->newImage,$logo,(int) ($this->newImageWidth-$logoWidth)/2,(int)($this->newImageHeight-$logoHeight)/2,0,0,$logoWidth,$logoHeight,$logoWidth,$logoHeight);
				return true;
		}
		return false;
	}

		public function addWatermark() {
			$watermark       = imageCreateFromPng($this->watermarkPath);
			$watermarkWidth  = imageSX($watermark);
			$watermarkHeight = imageSY($watermark);
			ImageCopyResampled($this->newImage,$watermark,(int) ($this->newImageWidth-$watermarkWidth)/2,(int)($this->newImageHeight-$watermarkHeight)/2,0,0,$watermarkWidth,$watermarkHeight,$watermarkWidth,$watermarkHeight);
		}
		
	}
?>
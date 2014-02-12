<?php
	class addmodules_controller extends application_controller {
				
		// -- генерируем блок для "Фотогалереи"
		public function generateImagesBlock($pid, $mid) {
		
			$count = $this->all->getCountItems($pid, $mid, 'images');
			$data['id']           = $pid;
			$data['module_id']    = $mid;
			$data['images_count'] = $count ? '(Добавлено <i>'.$count.'</i> фото)'  : '';
			$data['table_name']   = $this->base->getTableName($mid);
		
			switch ($mid) {
				case 2: $has_header = $this->db->get_one('SELECT gallery_header FROM news WHERE id='.$pid); break;
				case 8: $has_header = $this->db->get_one('SELECT gallery_header FROM catalog WHERE id='.$pid); break;
				default: $has_header = false;
			}
			if ($has_header === false) {
				$has_header = $this->db->get_one('SELECT gallery_header FROM main WHERE id='.$pid.' AND module='.$mid);	
			}
			$data['gallery_header'] = ($has_header) ? 'checked' : '';
			
			$data['fotogallery']  = $this->base->getFotogallery($pid, $mid);
			switch($data['fotogallery']) {
				case 1: $data['selected_gal_1'] = 'selected="selected"'; break; // -- "с затемнением экрана"
				case 2: $data['selected_gal_2'] = 'selected="selected"'; break; // -- "стандартная"
				case 3: $data['selected_gal_3'] = 'selected="selected"'; break; // -- "стандартная с прокруткой"
			}
			
			return $this->html->render('submodules/images.html', $data);
		}
		
		// -- генерируем блок для "Фото на страницу"
		public function generatePhotosBlock($pid, $mid) {
			$count = $this->all->getCountItems($pid, $mid, 'photos');
			$data['id']           = $pid;
			$data['module_id']    = $mid;
			$data['photos_count'] = $count ? '(Добавлено <i>'.$count.'</i> фото)'  : '';
			
			
			$sql  = 'SELECT * FROM photos WHERE pid = '.$pid.' AND module_id = '.$mid.' ORDER BY sort, id';
			$list = $this->db->get_all($sql);
			if(!empty($list)) {
				$c = sizeof($list);
				foreach($list as $i => &$item) {
					$item['url'] = '/application/includes/uploadIMG/'.$item['id'].'.'.$item['extension'];
					if ( ($i+1) < $c ) $item['zap'] = ',';
				}
	
				$tiny_image = $this->html->render('lists/tiny_image.html', array('listing' => $list));
				$this->file->toFile(APPLICATION_ADMIN.'includes'.DS.'js'.DS.'tiny'.DS.'lists'.DS.'image_list.js', $tiny_image, 'w+', 0755, false);
			}
			
			return $this->html->render('submodules/photos.html', $data);
		}
		
		// -- генерируем блок для "Файлы на страницу"
		public function generateFilesBlock($pid, $mid) {
			$count = $this->all->getCountItems($pid, $mid, 'files');
			$data['id']           = $pid;
			$data['module_id']    = $mid;
			$data['files_count']  = $count  ? '(Добавлено <i>'.$count.'</i> файлов)' : '';
			return $this->html->render('submodules/files.html', $data);
		}
		
		// -- генерируем блок для "Видео на страницу"
		public function generateVideosBlock($pid, $mid) {
			$count = $this->all->getCountItems($pid, $mid, 'videos', 'state = 1');
			$data['id']           = $pid;
			$data['module_id']    = $mid;
			$data['videos_count'] = $count ? '(Добавлено <i>'.$count.'</i> файлов)'  : '';
			$data['back_url']     = 'http://'.$_SERVER['HTTP_HOST'].'/admin/video/upload/'.base64_encode($_SERVER['REQUEST_URI']).'/';
			$data['back_url']     = urlencode($data['back_url']);
			
			return $this->html->render('submodules/videos.html', $data);
		}
		
		public function update_gallery_header($pid, $mid, $value) {
			switch ($mid) {
				case 2: {
					$rec = $this->db->get_one('SELECT pid FROM news WHERE id='.$pid);
					if ($rec !== false) {
						$sql = 'UPDATE news SET gallery_header='.(int)$value.' WHERE id='.(int)$pid;
						$this->db->query($sql);
						die();
					}
					break;
				}
				case 8: {
					$rec = $this->db->get_one('SELECT pid FROM catalog WHERE id='.$pid);
					if ($rec !== false) {
						$sql = 'UPDATE catalog SET gallery_header='.(int)$value.' WHERE id='.(int)$pid;
						$this->db->query($sql);
						die();
					}
					break;
				}
			}
			
			$sql = 'UPDATE main SET gallery_header='.(int)$value.' WHERE id='.(int)$pid.' AND module='.(int)$mid;
			$this->db->query($sql);
			die();
		}
		
	}
?>
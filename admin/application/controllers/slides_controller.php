<?php
	class slides_controller extends application_controller {
		
		public $path;
		
		public function __construct() {
			$this->path = $this->config->get('slides', 'files');
		}
		public function index(){
			$data['list'] =  $this->slides->GetSlides();
			$this->html->render('slides/index.html',$data,'content_path');
		}
		public function add_slide(){
			$data= array();
			if(!empty($_POST)){
				$data  = array(
					'link'		=>	trim($_POST['link']),
					'title' 	=>	$_POST['title'],	
					'note' 		=>	$_POST['note'],
				);
			}
			$id = $this->db->insert('slides', $data);
			if(!file_exists($this->path.$id)){
				mkdir($this->path.$id);
			}

			if(isset($_FILES['slide_pos1']['tmp_name']) && file_exists($_FILES['slide_pos1']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['slide_pos1']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['slide_pos1']['tmp_name'], $this->path.$id.'/1.png')) {
						$this->image->analyze($this->path.$id.'/1.png');
						$this->image->ToFile($this->path.$id.'/1.png', 80, $this->config->get('img_width','slide_pos1'), $this->config->get('img_height','slide_pos1'));
					}
				}
			}
			if(isset($_FILES['slide_pos2']['tmp_name']) && file_exists($_FILES['slide_pos2']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['slide_pos2']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['slide_pos2']['tmp_name'], $this->path.$id.'/2.png')) {
						$this->image->analyze($this->path.$id.'/2.png');
						$this->image->ToFile($this->path.$id.'/2.png', 80, $this->config->get('img_width','slide_pos2'), $this->config->get('img_height','slide_pos2'));
					}
				}
			}
			if(isset($_FILES['slide_pos3']['tmp_name']) && file_exists($_FILES['slide_pos3']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['slide_pos3']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['slide_pos3']['tmp_name'], $this->path.$id.'/3.png')) {
						$this->image->analyze($this->path.$id.'/3.png');
						$this->image->ToFile($this->path.$id.'/3.png', 80, $this->config->get('img_width','slide_pos3'), $this->config->get('img_height','slide_pos3'));
					}
				}
			}
			$this->url->redirect('::referer');
		}
		public function delete_slide($id){

			$this->db->delete('slides', (int)$id);
			
			for ($i = 1; $i < 4; $i++) { 
				if (file_exists($this->path.$id.'/'.$i.'.png')){
					unlink($this->path.$id.'/'.$i.'.png');	
				}
			}
			
			if (file_exists($this->path.$id)){
				rmdir($this->path.$id);	
			} 

			$this->url->redirect('::referer');
		}
		public function edit_slide($id=false){

			if(!empty($_POST)){
				$data  = array(
					'link'		=>	trim($_POST['link']),
					'title' 	=>	$_POST['title'],	
					'note' 		=>	$_POST['note'],
				);
				$this->db->update('slides',$data, $id);
				$this->session->set('alert','Данные успешно изменены');

			}

			
			if(!file_exists($this->path.$id)){
				mkdir($this->path.$id);
			}
			
			if(isset($_FILES['slide_pos1']['tmp_name']) && file_exists($_FILES['slide_pos1']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['slide_pos1']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['slide_pos1']['tmp_name'], $this->path.$id.'/1.png')) {
						$this->image->analyze($this->path.$id.'/1.png');
						$this->image->ToFile($this->path.$id.'/1.png', 80, $this->config->get('img_width','slide_pos1'), $this->config->get('img_height','slide_pos1'));
					}
				}
			}
			if(isset($_FILES['slide_pos2']['tmp_name']) && file_exists($_FILES['slide_pos2']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['slide_pos2']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['slide_pos2']['tmp_name'], $this->path.$id.'/2.png')) {
						$this->image->analyze($this->path.$id.'/2.png');
						$this->image->ToFile($this->path.$id.'/2.png', 80, $this->config->get('img_width','slide_pos2'), $this->config->get('img_height','slide_pos2'));
					}
				}
			}
			if(isset($_FILES['slide_pos3']['tmp_name']) && file_exists($_FILES['slide_pos3']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['slide_pos3']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['slide_pos3']['tmp_name'], $this->path.$id.'/3.png')) {
						$this->image->analyze($this->path.$id.'/3.png');
						$this->image->ToFile($this->path.$id.'/3.png', 80, $this->config->get('img_width','slide_pos3'), $this->config->get('img_height','slide_pos3'));
					}
				}
			}

			
			unset($data);
			
			$data = $this->slides->GetOneSlide($id);
			$data['title'] = preg_replace('/"/', '&quot;', $data['title']);
			
			$data['slide_pos1'] = (file_exists($this->path.$id.'/1.png'))?'<a href="/application/includes/slides/'.$id.'/1.png" target="_blank">Смотреть</a> <a href="/admin/slides/deleteImg/'.$id.'/1/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			$data['slide_pos2'] = (file_exists($this->path.$id.'/2.png'))?'<a href="/application/includes/slides/'.$id.'/2.png" target="_blank">Смотреть</a> <a href="/admin/slides/deleteImg/'.$id.'/2/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			$data['slide_pos3'] = (file_exists($this->path.$id.'/3.png'))?'<a href="/application/includes/slides/'.$id.'/3.png" target="_blank">Смотреть</a> <a href="/admin/slides/deleteImg/'.$id.'/3/" onclick="if(!confirm(\'Вы действительно хотите удалить картинку?\')) return false;">Удалить</a>':'';
			
			$this->html->render('slides/slide.html',$data,'content_path');
		}

		public function deleteImg($id, $num){
			if(file_exists($this->path.$id.'/'.(int)$num.'.png')){
				unlink($this->path.$id.'/'.(int)$num.'.png');
				$this->session->set('alert','Изображение #'.(int)$num.' было успешно удалено');
			}
			else {
				$this->session->set('alert','При удалении произошла ошибка. Обратитесь к администратору.');
			}
			$this->url->redirect('::referer');
		}
		
		// public function index() {
			
		// 	$sql  = 'SELECT * FROM slides ORDER BY sort';
		// 	$data['list'] = $this->db->get_all($sql);
		// 	if(!empty($data['list'])) {
		// 		foreach($data['list'] as $i => &$item) {
		// 			$item['tr_class'] = ($i % 2) ? 'even' : 'odd';
		// 			$item['visible_checked'] = (!empty($item['visible'])) ? 'checked="checked"' : '';
		// 		}
		// 	}
		
		// 	$this->html->render('slides/index.html', $data, 'content_path');
		// }
		
		// public function item($id = 0) {
			
		// 	$sql = 'SELECT * FROM slides WHERE id = '.(int)$id;
		// 	$data = $this->db->get_row($sql);
		// 	if(empty($data)) {
		// 		$data['id']		= 0;
		// 		$data['header'] = 'Новый слайд';
		// 	} else {
		// 		$data['header'] = $data['title'];
		// 		$data['visible_checked'] = (!empty($data['visible'])) ? 'checked="checked"' : '';
		// 		if(file_exists($this->path.$data['id'].'.jpg')) {
		// 			$data['image'] = 
		// 				'<a href="/application/includes/slides/'.$data['id'].'.jpg" target="_blank">посмотреть</a> | '.
		// 				'<a href="/'.$this->admin_dir.'/slides/delete_image/'.$data['id'].'/" onclick="return confirm(\'Вы уверены?\');">удалить</a>';
		// 		}
		// 	}
			
		// 	$this->html->render('slides/item.html', $data, 'content_path');
		// }
		
		// public function save() {
		
		// 	if(empty($_POST['title'])) {
		// 		$this->url->redirect('::referer');
		// 	}
			
		// 	$slide = array(
		// 		'title'		=> $_POST['title'],
		// 		'visible'	=> (int)$_POST['visible']
		// 	);
			
		// 	if(empty($_POST['id'])) {
		// 		$id = (int)$this->db->insert('slides', $slide);
		// 	} else {
		// 		$id = (int)$_POST['id'];
		// 		$this->db->update('slides', $slide, $id);
		// 	}
			
		// 	if(!empty($_FILES['image']['tmp_name']) && file_exists($_FILES['image']['tmp_name'])) {
		// 		$this->image->analyze($_FILES['image']['tmp_name']);
		// 		$this->image->toFile($this->path.$id.'.jpg', 80, 1250, 300);
		// 	}
			
		// 	$this->session->set('alert', ALERT_CHANGE_DATA);
		// 	$this->url->redirect('/'.$this->admin_dir.'/slides/');
		// }

	}
?>
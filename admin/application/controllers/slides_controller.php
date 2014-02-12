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
			if(isset($_FILES['new_slide']['tmp_name']) && file_exists($_FILES['new_slide']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['new_slide']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['new_slide']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','slides'), $this->config->get('img_height','slides'));
					}
				}
			}
			$this->url->redirect('::referer');
		}
		public function delete_slide($id){

			$this->db->delete('slides', (int)$id);
			
		
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
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

			
			if(isset($_FILES['new_slide']['tmp_name']) && file_exists($_FILES['new_slide']['tmp_name'])) {
				$file = $this->file->getFileInfo($_FILES['new_slide']['name']);
				if($file) {
					if(move_uploaded_file($_FILES['new_slide']['tmp_name'], $this->path.$id.'.jpg')) {
						$this->image->analyze($this->path.$id.'.jpg');
						$this->image->ToFile($this->path.$id.'.jpg', 80, $this->config->get('img_width','slides'), $this->config->get('img_height','slides'));
					}
				}
			}
			
			unset($data);
			$data = $this->slides->GetOneSlide($id);
			$data['title'] = preg_replace('/"/', '&quot;', $data['title']);
			$this->html->render('slides/slide.html',$data,'content_path');
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
<?php
	class visban_controller extends application_controller {
		
		public function show($category_id) {
			$result = '';
			$banner = $this->banner->getBanner($category_id);
			$banner['div_id'] = $category_id;
			if(empty($banner['link'])){
				$banner['link'] = 'javascript:void(0)';
				$banner['link_class'] = 'class="no_link"';
			}
			if(isset($banner['extension'])) {
				switch($banner['extension']) {
					case 'swf':
						return $this->html->render('banners/flash.html', $banner);
					case 'jpg':
					case 'jpeg':
					case 'gif':
					case 'png':
						return $this->html->render('banners/image.html', $banner);
				}
			}
			return '';
		}
		
		public function show_center($category_id, $center_id = 0) {
			$result = '';
			$banner = $this->banner->getBannerCenter($category_id, $center_id);
			$banner['div_id'] = $category_id;
			if(empty($banner['link'])){
				$banner['link'] = 'javascript:void(0)';
				$banner['link_class'] = 'class="no_link"';
			}
			if(isset($banner['extension'])) {
				switch($banner['extension']) {
					case 'swf':
						return $this->html->render('banners/flash.html', $banner);
					case 'jpg':
					case 'jpeg':
					case 'gif':
					case 'png':
						return $this->html->render('banners/image.html', $banner);
				}
			}
			return '';
		}
		
		/*public function js($id) {
			$banner = $this->banner->getOneBanner($id);
			$result = $this->html->render('banners/flash.js', $banner);
			header('Content-type: text/javascript');
			echo $result;
			die();
		}
		
		public function redir($id) {
			$this->url->redirect($this->banner->getLink($id));
		}*/
}
?>
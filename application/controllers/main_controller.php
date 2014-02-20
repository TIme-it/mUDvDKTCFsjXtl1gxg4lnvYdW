<?php
	class main_controller extends application_controller {
		
		public function index() {
			// -- SEO
			$this->html->tpl_vars['meta_keywords']    = $this->config->get('meta_keywords',    'site');
			$this->html->tpl_vars['meta_description'] = $this->config->get('meta_description', 'site');
			$this->html->tpl_vars['main_text'] = htmlspecialchars_decode($this->config->get('main_text', 'site'));
			

			/* SLIDES BLOCK BEGIN */

			$data['slides'] = $this->db->get_all('SELECT * FROM slides ORDER BY id ASC');
			if(!empty($data['slides'])){
				for($i= 0; $i<count($data['slides']); $i++){
					// $rand = rand(0, count($data['slides'])-1);
					$data['slides'][$i]['first'] = ($i == 0) ? true : false;
					$data['slides'][$i]['num'] = $i;
					$data['slides'][$i]['note'] = strip_tags($data['slides'][$i]['note']);
					$data['slides'][$i]['last'] = ($i == count($data['slides'])-1) ? true : false;
					$data['slides'][$i]['active'] = ($i == count($data['slides'])-2) ? true : false;
					// var_dump($data['slides']);
				}
				// die();
				$data['f_id']=$data['slides'][0]['id'];
				$data['slides'][0]['sel']=true;
			}
			$this->html->render('slider/slider.html', $data,'slider');

			/* SLIDES BLOCK END */

			/* ADDITIONAL TEXTS BEGIN*/ 
			$this->html->tpl_vars['bubble_text'] = htmlspecialchars_decode($this->config->get('bubble_text','site'));
			$this->html->tpl_vars['certificate_link'] = $this->config->get('certificate_link','site');
			/* ADDITIONAL TEXTS END*/ 

			/* PRODUCT BLOCK BEGIN */ 

			$cat_pid = 301;
			$data['catalog_url'] = $this->application_controller->get_url($cat_pid); 

			// По порядку: новинки, лидер и 2 популярных товара
			$data['product_list'] = $this->catalog->getNewestList($cat_pid);
			if(!empty($data['product_list'])){
				array_push($data['product_list'], $this->catalog->getLeadList($cat_pid));
			}
			$tmp = $this->catalog->getMostPopularList($cat_pid, 2);
			if(!empty($tmp)){
				foreach ($tmp as $i => &$item) {
					array_push($data['product_list'], $item);
				}
			}

			if(!empty($data['product_list'])){
				foreach ($data['product_list'] as $i => &$item) {
					$item['tchars'] = $this->catalog->getTechChars($item['pid'],$item['id']);
					if($this->config->get('active','chpu') == 1){
						$item['mid'] = $this->catalog->getMainIdProduct($item['id']);
						$item['url'] = $this->application_controller->get_url($item['mid']);
					}
					if (!empty($item['tchars'])){
						foreach ($item['tchars'] as $j => &$value) {
							$item['product_price'] = ($value['techchar_title'] == 'Цена') ? $value['techchar_value'] : false;
						}
					}
					$item['is_new'] = $item['is_new'] == 1 ? true : false;
					$item['is_leader'] = $item['is_leader'] == 1 ? true : false;

					if (mb_strlen($item['note'], 'UTF-8') > 280) {
						$item['note'] = mb_substr($item['note'], 0, 277, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...</p>';
					}
				}
			}
			$this->html->render('catalog/main_pop_product.html', $data,'main_pop_product');

			/* PRODUCT BLOCK END */ 

			$this->layout = 'main';
		}


		protected function format_to_page(&$array, $name, $controller){

			if(!empty($array[$name])){
				foreach ($array[$name] as $i => &$item) {
					$item['date'] = $this->date->format4($item['date']);
					$item['url'] = $this->$controller->get_url($item['id']);
					if (mb_strlen($item['note'], 'UTF-8') > 200) {
						$item['note'] = mb_substr($item['note'], 0, 197, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...</p>';
					}

					if (mb_strlen($item['title'], 'UTF-8') > 45) {
						$item['list_title'] = mb_substr($item['title'], 0, 43, 'UTF-8');
						$item['list_title'] = mb_substr($item['list_title'], 0, mb_strrpos($item['list_title'],' ', 'UTF-8'), 'UTF-8').'...';
					}
					else {
						$item['list_title'] = $item['title'];
					}
				}
			}
		}
		
		// -- HTTP 404
		public function page_404() {
			header('HTTP/1.0 404 Not Found');
			$this->html->render('main/page_404.html', array(), 'content');
		}

	}
?>
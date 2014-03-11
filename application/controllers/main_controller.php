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
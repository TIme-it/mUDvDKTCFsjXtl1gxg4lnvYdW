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
					// $data['slides'][$i]['active'] = ($i == count($data['slides'])-2) ? true : false;
					if($i == 2){
						$data['curr_id'] = $data['slides'][$i]['id'];
					}
					// var_dump($data['slides']);
				}
				
				// die();
				$data['f_id']=$data['slides'][0]['id'];
				$data['slides'][0]['sel']=true;
			}
			$this->html->render('slider/slider.html', $data,'slider');

			/* SLIDES BLOCK END */

			/* ACTIONS BLOCK BEGIN */

			$actions_id = $this->config->get('actions_id', 'site');
			$this->html->tpl_vars['actions_link'] = $this->config->get('actions_link', 'site');
			$this->html->tpl_vars['actions_list'] = $this->actions->getLast($actions_id, 1);
			
			/* ACTIONS BLOCK END*/

			/* NEWS BLOCK BEGIN */ 

			$news_id = $this->config->get('news_id', 'site');
			$this->html->tpl_vars['news_link'] = $this->config->get('news_link', 'site');
			$data['news_list'] = $this->news->getLast($news_id, 15);
			if(!empty($data['news_list'])){
				foreach ($data['news_list'] as $i => &$item) {
					$item['date'] = $this->date->format2($item['date']);
					$item['url'] = $this->news_controller->get_url($item['id']);

					if (mb_strlen($item['note'], 'UTF-8') > 500) {
						$item['note'] = mb_substr($item['note'], 0, 497, 'UTF-8');
						$item['note'] = mb_substr($item['note'], 0, mb_strrpos($item['note'],' ', 'UTF-8'), 'UTF-8').'...';
					}
				}
			}

			$this->html->tpl_vars['news_list'] = $data['news_list'];

			/* NEWS BLOCK END */

			/* REVIEWS BLOCK BEGIN */

			$reviews_id = $this->config->get('reviews_id', 'site');
			$this->html->tpl_vars['reviews_link'] = $this->config->get('reviews_link', 'site');
			$data['reviews_list'] = $this->reviews->getReviews($reviews_id, 0, 3, 1);
			// выделение жирным фамилии
			if(!empty($data['reviews_list'])){
				foreach ($data['reviews_list'] as $i => &$item) {
					$tmp = explode(' ', $item['fioUser']);
					$tmp[0] = '<strong>'.$tmp[0].'</strong>';
					$item['fioUser'] = '';
					for ($i=0; $i < count($tmp) ; $i++) { 
						$item['fioUser'] .= $tmp[$i].' '; 
					}
				}
			}
			
			$this->html->tpl_vars['reviews_list'] = $data['reviews_list'];
			
			/* REVIEWS BLOCK END */ 

			$this->layout = 'main';
		}
		
		// -- HTTP 404
		public function page_404() {
			header('HTTP/1.0 404 Not Found');
			$this->html->render('main/page_404.html', array(), 'content');
		}
		

				

	}
?>
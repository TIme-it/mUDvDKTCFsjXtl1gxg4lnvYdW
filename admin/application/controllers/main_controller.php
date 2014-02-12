<?php
	class main_controller extends application_controller {
		
		public function index() {
			$html = '<h2>Добро пожаловать в TimeITWeb!</h2>';
			$this->html->tpl_vars['content_path'] = $html;
		}
		
		public function page_404() {
			$this->url->redirect('::referer');
		}
	}
?>
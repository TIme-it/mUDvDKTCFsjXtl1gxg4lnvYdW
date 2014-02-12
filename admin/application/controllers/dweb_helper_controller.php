<?php
	class dweb_helper_controller extends libs_controller {
	
		public function index() {
			echo file_get_contents('http://api.direktline.ru/application/includes/help/blank.html');
			die();
		}
		
	}
?>
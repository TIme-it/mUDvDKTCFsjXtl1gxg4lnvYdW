<?php
	class url {
		
		static $url_vars;
		
		// -- ��������������� (2.00a)
		// -- ������ 2.02b:
		// -- 1. ������ �� ���������, �.�. ��������� �� ���� 
		// -- 2. ����������� ��������� ��������� ��������� (@$redirect)
		public function redirect($location, $redirect = '/') {
			if($location == '::referer') {
				$location = $_SERVER[(empty($_SERVER['HTTP_REFERER'])?'REQUEST_URI':'HTTP_REFERER')];
			}
			if($location == $_SERVER['REQUEST_URI']) {
				$location = $redirect;
			}
			header('Location: '.$location);
			die();
		}
		
		public function get($key) {
			return url::$url_vars[$key];
		}
		
		public function set($key,$value) {
			url::$url_vars[$key] = $value;  
		}
	}
?>
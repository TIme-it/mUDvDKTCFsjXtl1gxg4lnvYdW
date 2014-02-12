<?php
	class session {
		
		// ��������� ��������� ������
		public function __construct() {
			if(!session_id()) {
				session_start();
			}
		}
		
		// ������ �������� �� ����� � ������
		public function set($key, $value) {
			$_SESSION[$key] = $value;
		}
		
		// �������� �������� �� ����� �� ������
		public function get($key) {
			if(isset($_SESSION[$key])) {
				return $_SESSION[$key];
			}
			return false;
		}
		
		// ������� �������� �� ����� � ������
		public function del($key) {
			if(isset($_SESSION[$key])) {
				unset($_SESSION[$key]);
			}
		}
		
		public function is_set($key) {
			return isset($_SESSION[$key]);
		}
		
	}
?>
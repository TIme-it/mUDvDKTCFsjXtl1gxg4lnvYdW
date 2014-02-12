<?php
	class session {
		
		// запускаем обработку сессии
		public function __construct() {
			if(!session_id()) {
				session_start();
			}
		}
		
		// задать значение по ключу в сессию
		public function set($key, $value) {
			$_SESSION[$key] = $value;
		}
		
		// получить значение по ключу из сессии
		public function get($key) {
			if(isset($_SESSION[$key])) {
				return $_SESSION[$key];
			}
			return false;
		}
		
		// удалить значение по ключу в сессии
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
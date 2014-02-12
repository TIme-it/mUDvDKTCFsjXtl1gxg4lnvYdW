<?php
class popup_controller extends application_controller {
	
	//Метод показа блока
	public function index($controller, $method=false, $vars=false) {	
		if($controller == "map"){
			$this->layout = 'pages';
		}	
		if($controller == "search"){
			$this->layout = 'pages';
		}	
		if($controller == "catalog"){
			$this->layout = 'catalog';
			// -- помечаем активный раздел
			$this->active_main_id = $vars;
		}
		$controller = $controller.'_controller';
		$controller = $this->lib_add($controller);
		if(!empty($method)){
			call_user_func_array(array($controller, $method), array($vars));
		}
		elseif(empty($method)){
			call_user_func_array(array($controller, $this->config->get('index_method','system')), array($vars));
		}
		else {
			return false;
		}
	}
}
?>
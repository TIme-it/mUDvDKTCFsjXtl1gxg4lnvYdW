<?php
class uri_controller extends libs_controller {
	
	private $uri;
	private $controller;
	private $method;
	private $not_found;
	
	public function __construct() {
		$this->analyze();
		$this->start();
	}

	public function start() {
		$this->popup = $this->controller;
		$this->controller = $this->lib_add($this->controller);
		if((!$this->controller)) {
			$this->controller = $this->lib_add('main_controller');
			$this->method     = 'page_404';
		}

		$ur = explode('?', $_SERVER['REQUEST_URI']);
		if(($this->not_found !== '0') && ($ur[0] != '/') && ($this->config->get('active','chpu') == 1) && (!defined('APPLICATION_ADMIN')) && ($this->popup != "popup_controller") && ($this->popup != "catalog_controller")){
			$this->controller = $this->lib_add('main_controller');
			$this->method     = 'page_404';
		}
		
		if(method_exists($this->controller, '__before')) {
			call_user_func_array(array($this->controller,'__before'), array());
		}
		call_user_func_array(array($this->controller, $this->method), $this->vars);
		if(method_exists($this->controller, '__after')) {
			call_user_func_array(array($this->controller,'__after'), array());
		}
	}

	public function analyze() {
		$this->uri = $_SERVER['REQUEST_URI']; 
		// -- îòñåêàåì $_GET
		$this->uri = explode('?', $this->uri);
		$this->uri = $this->uri[0];
		
		
		if(preg_match('/\/$/', $this->uri)) {
			$this->uri = substr($this->uri,0,-1);
			$this->uri = substr($this->uri,1);
		} else $this->uri = substr($this->uri,1);
		
		if(false !== $this->uri) {
			$uri = explode('/',$this->uri);
			
			if($uri[0] == $this->config->get('admin_dir','system')) {
				array_shift($uri);
				if(!defined('APPLICATION_ADMIN')) define('APPLICATION_ADMIN', INDEX.$this->config->get('admin_dir','system').DS.'application'.DS);
				if(!defined('VIEWS_ADMIN')) define('VIEWS_ADMIN', INDEX.$this->config->get('admin_dir','system').DS.'application'.DS.'views'.DS);
				$this->controller =(empty($uri))?$this->config->get('main_controller','system'):array_shift($uri).'_controller';
				$this->method = (empty($uri) || !method_exists($this->controller,$uri[0])) ? $this->config->get('index_method','system'): array_shift($uri);
				$this->vars = $uri;
			}
			else {
				$chpu_active = $this->config->get('active','chpu');
				switch ($chpu_active) {
					case 0:
						$this->controller =(empty($uri))?$this->config->get('main_controller','system'):array_shift($uri).'_controller';
						$this->method = (empty($uri) || !method_exists($this->controller,$uri[0])) ? $this->config->get('index_method','system'): array_shift($uri);
						$this->vars = $uri;
						break;

					case 1:
						if($uri[0] == "popup"){
							$this->controller =(empty($uri))?$this->config->get('main_controller','system'):array_shift($uri).'_controller';
							$this->method = (empty($uri) || !method_exists($this->controller,$uri[0])) ? $this->config->get('index_method','system'): array_shift($uri);
							$this->vars = $uri;
							break;
						}
						 
						else{
							$url = array_reverse($uri,true);
							foreach ($url as $i => &$item) {
								if(!empty($module_id)){
									$tmp = $this->all->getMainPid($item);
									$url[$i] = $item;
									unset($item);
								}
								else {
									$module_id = $this->all->getMainAliasUrl($item);
									$alias = $item;
									$tmp = $this->all->getMainPid($item);
									$urla[] = $item;
								}
							}

							$controller_name = $this->all->getModuleName($module_id);
							if($controller_name == "link"){
								$link = $this->all->getLinkUrl($alias);
								$controller_name = preg_replace("/^.(.*).$/", "\\1", $link);
							}
							elseif($controller_name == "catalog" && count($urla) > 1){
								
								// определяем метод и проверяем на корректность ссылку
								$is_correct_url = $this->catalog->verify_url($urla[0]);
								if((!$is_correct_url)) {
									$this->controller = 'main_controller';
									$this->method     = 'page_404';
									$this->vars = false;
								}
								else {
									$this->not_found = $tmp;
									$this->controller = $controller_name.'_controller';
									$this->method = $this->catalog->getMethod($urla[0]);
									$this->vars = $urla;
								}


								// var_dump($this->controller);
								// var_dump($this->method);
								// var_dump($this->vars);
								// die();	
								if (($this->config->get('active','chpu') == 1) && (count($this->vars) == 1)){
									$temp[] = $this->all->getPagePid($alias);
									$this->vars = $temp;
								}
								break;
							}
							$this->not_found = $tmp;
							$this->controller = $controller_name.'_controller';
							$this->method = (empty($urla) || !method_exists($this->controller,$urla[0])) ? $this->config->get('index_method','system'): array_shift($urla);
							$this->vars = $urla;

							if (($this->config->get('active','chpu') == 1) && (count($this->vars) == 1)){
								$temp[] = $this->all->getPagePid($alias);
								$this->vars = $temp;
							}
							break;
						}
				}
				
			}
			
		} else {
			$this->controller = $this->config->get('main_controller','system');
			$this->method = $this->config->get('index_method','system');
			$this->vars = array();
		}
	
		$this->url->set('page',substr($this->controller,0,-11));
		$this->url->set('action',$this->method);
		$this->url->set('vars',$this->vars);

	}
}
?>
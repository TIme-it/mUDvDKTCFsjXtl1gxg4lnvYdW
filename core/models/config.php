<?php
class Config {

	private $config_array;
	//Работа с конфигами

	public function __construct() {
		require_once(CONFIG.'config.php');
		require_once(CONFIG.'config.inc.php');
		$this->config = $config;
	}

	public function get($key,$category = '') {
		if(empty($category)) {
			return (false !== array_key_exists($key,$this->config))?$this->config[$key]:false;
		} else { 
			return (false !== array_key_exists($key,$this->config[$category]))?$this->config[$category][$key]:false;
		}
	}

	public function get_array($category) {
		if(false === array_key_exists($category,$this->config)) return false;
		return $this->config[$category];
	} 
}
?>
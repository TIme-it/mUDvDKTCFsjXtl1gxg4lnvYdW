<?php
class libs_controller {
	protected static $libs = array();

	protected function __construct() {}

	public function __get($lib) {
		// -- учитываем хелпнеры
		if(!empty($lib) && $lib[0] == '_') {
			$lib = substr($lib, 1).'_helpner';
		}
		if(!isset(self::$libs[$lib])) {
			self::$libs[$lib] = $this->lib_add($lib);
		}
		return self::$libs[$lib];
	}

	protected function lib_add($lib) {
		if(!$lib || !class_exists($lib)) {
			return false;
		}
		$obj = new $lib();
		return $obj;
	}


}
?>
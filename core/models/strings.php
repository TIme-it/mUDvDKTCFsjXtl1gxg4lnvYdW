<?php
class strings {
	
	protected $translit_array = array(
		'а' => 'a','б' => 'b','в' => 'v',
		'г' => 'g','д' => 'd','е' => 'e',
		'ё' => 'e','ж' => 'j','з' => 'z',
		'и' => 'i','й' => 'i','к' => 'k',
		'л' => 'l','м' => 'm','н' => 'n',
		'о' => 'o','п' => 'p','р' => 'r',
		'с' => 's','т' => 't','у' => 'u',
		'ф' => 'f','х' => 'h','ц' => 'c',
		'ч' => 'ch','ш' => 'sh','щ' => 'csh',
		'ь' => '','ы' => 'y','ъ' => '',
		'э' => 'e','ю' => 'yu','я' => 'ya',
		'А'=>'A','Б'=>'B','В'=>'V',
		'Г' => 'G','Д' => 'D','E' => 'E',
		'Ё' => 'E','Ж' => 'J','З' => 'Z',
		'И' => 'I','Й' => 'I','К' => 'K',
		'Л' => 'L','М' => 'M','Н' => 'N',
		'О' => 'O','П' => 'P','Р' => 'R',
		'С' => 'S','Т' => 'T','У' => 'U',
		'Ф' => 'F','Х' => 'H','Ц' => 'C',
		'Ч' => 'CH','Ш' => 'SH','Щ' => 'CSH',
		'Ь' => '','Ы' => 'Y','Ъ' => '',
		'Э' => 'E','Ю' => 'YU','Я' => 'YA',
		' ' => '_');

	public function __construct() {}
	
	public function translit($str) {
		return strtr($str,$this->translit_array);
	}
}
?>
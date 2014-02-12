<?php

class XML {
	private $xml_data;

	public function __construct() {}
	
	function parse($file,$tag,$ret=0) {
		$file = file_get_contents($file);
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser,$file,$vals);
		xml_parser_free($xml_parser);
		$current_key = $i = 0;
		$attributes = $array = array();
		foreach ($vals as $key=>$val) {
			if (($val['tag'] == $tag) && ($val['type']=='open')) $current_key = $key;
			elseif(($val['type']=='complete') && (!empty($val['value']))) $array[$i][$val['tag']] = $val['value'];
			elseif(($val['type']=='complete') && (!empty($val['attributes']))) $attributes[$i][$val['tag']] = $val['attributes'];
			elseif(($val['tag']==$tag) && ($val['type']=='close')) $i++;
			else continue;
			}
		if ($ret==1) return $attributes;
		else return $array;
	}
}

?>
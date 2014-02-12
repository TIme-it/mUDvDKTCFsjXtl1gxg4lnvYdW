<?php
	class slides extends app_model {
	
		// -- ðåæèì ðàáîòû:
		// -- 1: âñå ôàéëû äîáàâëÿåì â zip + dump.sql
		// -- 2: âñå ôàéëû ñêëèâàåì + sql + åñëè âîçìîæíî äîâàâëÿåì â zip
		public function GetSlides()
		{
			$sql = 'SELECT * FROM slides';
			return $this->db->get_all($sql);
		}
		public function GetOneSlide($id){
			$sql = 'SELECT * FROM slides WHERE id = '.(int)$id;
			return $this->db->get_row($sql);
		}
		
	}
?>
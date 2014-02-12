<?php
	class role extends app_model {
		
		public function getAdminEmail($center_id) {
		$sql = 'SELECT au.email FROM auth_user au 
				LEFT JOIN auth_rights ar ON ar.role_id = au.role_id 
				WHERE ar.action_id = 6 AND ar.param1 = '.(int)$center_id;
		return $this->db->get_all_one($sql);
	}
		
	}
?>
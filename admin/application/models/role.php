<?php
	class role extends app_model {
		
		//Выборка пользователя по id
		public function getUser($id) {
			$sql = 'SELECT au.*, ar.title FROM auth_user au
					LEFT JOIN auth_role ar ON au.role_id = ar.id
					WHERE au.id = '.$id;
			return $this->db->get_row($sql);
		}
		
		//Выборка пользователя по логину
		public function getUserByLogin($login) {
			$sql = 'SELECT au.*, ar.title FROM auth_user au
					LEFT JOIN auth_role ar ON au.role_id = ar.id
					WHERE au.login = "'.$login.'" LIMIT 1';
			return $this->db->get_row($sql);
		}
		
		
		//Выборка роли и прав роли
		public function getRole($role_id) {
			$sql = 'SELECT * FROM auth_role
					WHERE id = '.(int)$role_id;
			$res = $this->db->get_row($sql);
			return $res;
		}
		
		//Выборка прав роли (для запоминания при авторизации)
		public function getRoleRights($role_id) {
			$sql = 'SELECT ar.action_id, ar.param1 FROM auth_rights ar
					LEFT JOIN auth_action aa ON aa.id = ar.action_id
					WHERE ar.role_id = '.$role_id;
			$tmp = $this->db->get_all($sql);
			$res = array();
			if (!empty($tmp)) {
				foreach ($tmp as &$v) {
					if ( !isset($v['param1']) ) $res[$v['action_id']] = 1;
					else $res[$v['action_id']][$v['param1']] = 1;
				}
			}
			return $res;
		}
		
		//======================
		
		//Проверка существования логина
		public function HasLogin($login) {
			$sql = 'SELECT 1 FROM auth_user
					WHERE login = '.$this->db->escape($login).' LIMIT 1';
			return $this->db->get_one($sql);
		}
		
		//Проверка существования логина кроме того, чей id указан
		public function HasLoginNew($id, $login) {
			$sql = 'SELECT 1 FROM auth_user
					WHERE login = '.$this->db->escape($login).' AND id<>'.(int)$id.' LIMIT 1';
			return $this->db->get_one($sql);
		}
		
		//Проверка существования названия роли
		public function HasRoleTitle($title) {
			$sql = 'SELECT 1 FROM auth_role
					WHERE title = '.$this->db->escape($title).' LIMIT 1';
			return $this->db->get_one($sql);
		}
		
		//Проверка существования названия роли кроме той, чей id указан
		public function HasRoleTitleNew($id, $title) {
			$sql = 'SELECT 1 FROM auth_role
					WHERE title = '.$this->db->escape($title).' AND id<>'.(int)$id.' LIMIT 1';
			return $this->db->get_one($sql);
		}
		
		//Выборка количества пользователей с каждой ролью
		public function getRoleUserCount() {
			$sql = 'SELECT ar.*, tmp.count FROM auth_role ar
							LEFT JOIN
							(SELECT au.role_id, COUNT(*) count FROM auth_user au
							GROUP BY au.role_id) AS tmp
							ON ar.id=tmp.role_id';
			$tmp = $this->db->get_all($sql);
			$res = array();
			if (!empty($tmp))
				foreach ($tmp as $v) {
					$res[$v['id']] = (int)$v['count'];
				}
			return $res;
		}
		
		//=============================
		
		//Выборка дочерних пользователей
		public function getSubUsers($pid, $only_ids = false) {
			$res = array();
			$this->getSubUsersLevel($pid, $res, $only_ids);
			return $res;
		}
		
		private function getSubUsersLevel($pid, &$res, $only_ids) {
			if ($only_ids) {
				$sql = 'SELECT au.id FROM auth_user au
						LEFT JOIN auth_role ar ON au.role_id = ar.id
						LEFT JOIN auth_user au2 ON au2.id = au.pid
						WHERE au.pid = '.(int)$pid.'
						ORDER BY au.id';
				$tmp = $this->db->get_all_one($sql);
			} else {
				$sql = 'SELECT au.*, au2.login creator, ar.title FROM auth_user au
						LEFT JOIN auth_role ar ON au.role_id = ar.id
						LEFT JOIN auth_user au2 ON au2.id = au.pid
						WHERE au.pid = '.(int)$pid.'
						ORDER BY au.id';
				$tmp = $this->db->get_all($sql);
			}
			
			if (!empty($tmp)) {
				foreach ($tmp as &$v) {
					$this->getSubUsersLevel($v['id'], $res, $only_ids);
				}
				$res = array_merge($res,$tmp);
			}
		}
		
		
		//Выборка дочерних ролей
		public function getSubRoles($pid, $only_ids = false) {
			$res = array();
			$this->getSubRolesLevel($pid, $res, $only_ids);
			return $res;
		}
		
		private function getSubRolesLevel($pid, &$res, $only_ids) {
			if ($only_ids) {
				$sql = 'SELECT ar.id FROM auth_role ar
						LEFT JOIN auth_role ar2 ON ar2.id = ar.pid
						WHERE ar.pid = '.(int)$pid.'
						ORDER BY ar.title';
				$tmp = $this->db->get_all_one($sql);
			} else {
				$sql = 'SELECT ar.*, ar2.title creator FROM auth_role ar
						LEFT JOIN auth_role ar2 ON ar2.id = ar.pid
						WHERE ar.pid = '.(int)$pid.'
						ORDER BY ar.title';
				$tmp = $this->db->get_all($sql);
			}
			
			if (!empty($tmp)) {
				foreach ($tmp as &$v) {
					$this->getSubRolesLevel($v['id'], $res, $only_ids);
				}
				$res = array_merge($res,$tmp);
			}
		}
		
		//============================================================
		
		//Список всех страниц (из указанного множества access, если он задан)
		public function getPages($access = array()) {
			$sql = 'SELECT id, title 
					FROM main 
					WHERE active=1 '.( empty($access) ? '' : 'AND id IN('.join(',',$access).') ' ).
					'ORDER BY tree';
			$res = $this->db->get_all($sql);
			return empty($res) ? array() : $res;
		}
		
		//Список id страниц, на которые есть доступ
		public function getAccessPages($role_id) {
			if ($role_id == 0)	//Для главного админа
				$sql = 'SELECT id FROM main ORDER BY tree';
			else $sql = 'SELECT param1 FROM auth_rights WHERE action_id=2 AND role_id='.$role_id;
			
			$res = $this->db->get_all_one($sql);
			return empty($res) ? array(0) : $res;
		}
		
	}
?>
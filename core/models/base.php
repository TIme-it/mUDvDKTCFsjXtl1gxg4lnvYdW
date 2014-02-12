<?php
	class base extends libs_controller {
		
		// -- �������� ��� ������� �� id ������
		public function getTableName($mid) {
			$sql = 'SELECT name FROM module WHERE id = '.(int)$mid;
			return ($mid <= 1) ? 'main' : $this->db->get_one($sql);
		}
		
		// -- �������� �������� ��������� �����������
		public function getFotogallery($pid, $mid) {
			$table = $this->getTableName($mid);
			$sql   = 'SELECT fotogallery FROM '.$table.' WHERE id = '.(int)$pid;
			return (int)$this->db->get_one($sql);
		}
		
		public function del_keys($data, $keys) {
			$arr = $data;
			if(!is_array($keys)) return false;
			foreach($keys as $v) {
				if(isset($arr[$v])) unset($arr[$v]);
			}
			return $arr;
		}
		
		// -- ������� ����������: �����, ��.�����, ��.�.�����, ��.�.�����, ����������� 2��� 10��
		function declension($count, $one, $many, $vmany, $is_dec = true) {
			if($is_dec && $count > 10 && $count < 20) return $count.' '.$vmany;
			if($count % 10 == 1) return $count.' '.$one;
			if(in_array($count % 10, array(2,3,4))) return $count.' '.$many;
			return $count.' '.$vmany;
		}

	}
?>
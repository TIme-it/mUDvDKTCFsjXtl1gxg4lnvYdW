<?php
	class db extends application_controller {
		
		private $db;
		private $host;
		private $user;
		private $pwd;
		private $link;
		
		public function __construct() {
			$this->host = $this->config->get('host', 'db');
			$this->db   = $this->config->get('db',   'db');
			$this->user = $this->config->get('user', 'db');
			$this->pwd  = $this->config->get('pwd',  'db');
			$this->connect();
		}
		
		private function connect() {
			if(!($this->link = mysqli_connect($this->host, $this->user, $this->pwd,$this->db)) && DEBUG) {
				throw new Exception('<p>Невозможно соединиться с базой данных</p><p>'.mysqli_error($this->link).'</p>');
			}
			if(!mysqli_select_db($this->link,$this->db) && DEBUG) {
				throw new Exception('<p>Невозможно выбрать базу данных</p><p>'.mysqli_error($this->link).'</p>');
			}
		
			mysqli_set_charset ($this->link , $this->config->get('charset','db') );
			return $this->link;
		}
		
		public function query($sql) {
			$query = mysqli_query($this->link, $sql);
			if(!$query && DEBUG) {
				throw new Exception('<p><strong>Ошибка в SQL:</strong></p><p>'.$sql.'</p><p>'.mysqli_error($this->link).'</p>');
			}
			return $query;
		}
		
		public function get_one($sql, $pos = 0) {
			$query = $this->query($sql);
			if(!$query || !mysqli_num_rows($query)) {
				return false;
			}
			$result = mysqli_fetch_row($query);
			return $result[$pos];
		}
		
		public function get_all($sql) {
			$query = $this->query($sql);
			if(!$query || !mysqli_num_rows($query)) {
				return false;
			}
			$result = array();
			while($row = mysqli_fetch_assoc($query)) {
				$result[] = $row;
			}
			return $result;
		}
		
		public function get_all_one($sql) {
			$query = $this->query($sql);
			if(!$query || !mysqli_num_rows($query)) {
				return false;
			}
			$result = array();
			while($row = mysqli_fetch_row($query)) {
				if(isset($row[0])) {
					$result[] = $row[0];
				}
			}
			return $result;
		}
		
		public function get_row($sql, $keys = true) {
			$query = $this->query($sql);
			if(!$query || !mysqli_num_rows($query)) {
				return false;
			}
			return ((bool)$keys ? mysqli_fetch_assoc($query) : mysqli_fetch_row($query));
		}
		
		public function insert($table, $data) {
			$keys  = join(',', array_keys($data));
			$vals  = join(',', $this->escape(array_values($data)));
			$res_q = $this->query('INSERT INTO '.$table.'('.$keys.') VALUES ('.$vals.')');
			return ($res_q ? mysqli_insert_id($this->link) : false);
		}

		public function update($table, $data, $expr) {
			$data = $this->sql_prepare($data, ',');
			$expr =(is_array($expr))?$this->sql_prepare($expr,' AND '):'id='.$this->escape($expr);
			
			$sql = 'UPDATE '.$table.' SET '.$data.' WHERE ('.$expr.')'; 

			return $this->query($sql);
		}
				
		public function delete($table, $data) {
			if(is_array($data)) {
				if(!count($data)) return false;
				$sql = array();
				foreach($data as $key => &$val) {
					$sql[] = $key.' = '.$this->escape($val);
				}
				$sql = 'DELETE FROM '.$table.' WHERE '.implode(' AND ', $sql);
			} else {
				$sql = 'DELETE FROM '.$table.' WHERE id ='.$data;
			}
			return $this->query($sql);
		}
		
		public function count_rows($sql) {
			$query = $this->query($sql);
			$rows = mysqli_num_rows($query);
			return ($rows>0)?$rows:false;
		}
		
		public function mysqli_next_id($table) {
			$sql = 'SHOW TABLE STATUS LIKE "'.$table.'"';
			$result = $this->get_row($sql);
			return $result['Auto_increment'];
	}
		
		public function sql_export() {
			$i=0;
			$sql = 'SHOW TABLES';
			$tables = $this->get_all($sql);
			$dump = '';
			while($i<count($tables)) {
				$sql = 'SHOW CREATE TABLE `'.$tables[$i]['Tables_in_'.$this->db].'`';
				$table = $this->get_row($sql,false);
				$dump .= $table[1].";\n\n";
				$insert = $this->get_all('SELECT * FROM '.$tables[$i]['Tables_in_'.$this->db]);
				$k=0;
				while($k<count($insert)) {
					if(!empty($insert[$k])) {
						$keys = join(',',array_keys($insert[$k]));
						$vals = join(',',$this->escape(array_values($insert[$k])));
						$dump .="INSERT INTO ".$tables[$i]['Tables_in_'.$this->db]."(".$keys.") VALUES(".$vals.");\n";
					}
					$k++;
				}
				$dump.="\n";
				$i++;
			}
			return $dump;
		} 
		
		private function sql_prepare($data,$glue) {
			$vals = array();
			foreach($data as $key=>$val) {
				$vals[] = $key.'='.$this->escape($val);
			}
			return join($glue,$vals);
		}
		
		public function escape($str) {
			if(is_array($str)) {
				foreach($str as &$val) {
					$val = $this->escape($val); 
				}
			} 
			return (is_array($str)) ? $str : '"'.mysqli_real_escape_string($this->link,trim($str)).'"';
		}
		
		public function __toString() {
			return $this->db;
		} 

	}
?>
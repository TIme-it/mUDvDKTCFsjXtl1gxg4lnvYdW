<?php
	class backup extends app_model {
	
		// -- режим работы:
		// -- 1: все файлы добавляем в zip + dump.sql
		// -- 2: все файлы скливаем + sql + если возможно довавляем в zip
		private $mode = 1;
		
		public function getBackupList() {
			$result = array();
			$dir_ptr = dir(BACKUPS);
			while($file_name = $dir_ptr->read()) {
				if($file_name != '.htaccess') {
					$file_path = BACKUPS.$file_name;
					if(is_file($file_path)) {
						$result[] = array(
							'file_name' => $file_name,
							'file_size' => filesize($file_path),
						);
					}
				}
			}
			return $result;
		}
		
		public function build_backup($backup_name) {
			switch($this->mode) {
				case 1: return $this->build_backup_zip($backup_name);
				case 2: return $this->build_backup_data($backup_name);
			}
			return false;
		}
		
		public function build_backup_zip($backup_name) {
			if(!class_exists('ZipArchive')) return true;
		
			// -- подготовка данных
			$sql_dump = $this->db->sql_export();
			$fs_dump  = $this->get_fs_arr(INDEX);
			
			$backup_zip_path = BACKUPS.$backup_name.'.zip';
			$zip = new ZipArchive();
			if(!$zip->open($backup_zip_path, ZIPARCHIVE::CREATE)) {
				return false;
			}
			$zip->addFromString('dump.sql', $sql_dump);
			$this->add_backup_zip($zip, $fs_dump);
			$zip->close();
			return true;
		}
		
		public function build_backup_data($backup_name) {
			// -- подготовка данных
			$sql_dump      = $this->db->sql_export();
			$fs_dump       = $this->get_fs_arr(INDEX);
			$fs_dump['s']  = strlen($sql_dump);
			$fs_dump_ser   = serialize($fs_dump);
			$fs_dump_ser_c = strlen($fs_dump_ser);
			
			// -- создаем backup-файл
			$backup_path = BACKUPS.$backup_name.'.bin';
			$fp = fopen($backup_path, 'w');
			fwrite($fp, (string)strlen($fs_dump_ser_c));
			fwrite($fp, (string)$fs_dump_ser_c);
			fwrite($fp, $fs_dump_ser);
			fwrite($fp, $sql_dump);
			$this->add_backup_data($fp, $fs_dump, INDEX);
			fclose($fp);
			
			if(!class_exists('ZipArchive')) return true;
			
			// -- используем ZipArchive
			$backup_zip_path = $backup_path.'.zip';
			$zip = new ZipArchive();
			if(!$zip->open($backup_zip_path, ZIPARCHIVE::CREATE)) {
				return false;
			}
			$zip->addFile($backup_path, $backup_name.'.bin');
			$zip->close();
			unlink($backup_path);
			return true;
		}
		
		// -- возращает структуру файловой системы от тек. пути
		private function get_fs_arr($dir_path, &$fs = false) {
			$is_return = false;
			if(empty($fs)) {
				$fs = array();
				$is_return = true;
			}
			$dir_ptr = dir($dir_path);
			while($file_name = $dir_ptr->read()) {
				if(!in_array($file_name, array('.','..'))) {
					$file_path = $dir_path.$file_name;
					if(is_file($file_path)) {
						$fs['f'][$file_name] = filesize($file_path);
					} elseif(is_dir($file_path)) {
						$fs['d'][$file_name] = array();
						$this->get_fs_arr($file_path.DS, $fs['d'][$file_name]);
					}
				}
			}
			return ($is_return) ? $fs : true;
		}
		
		// -- записываем содержимое файлов
		private function add_backup_data(&$fp, &$fs, $path) {
			if(!empty($fs['f'])) {
				foreach($fs['f'] as $file_name => $file_size) {
					$file_path = $path.$file_name;
					if($file_size > 0 && file_exists($file_path)) {
						fwrite($fp, file_get_contents($file_path));
					}
				}
			}
			if(!empty($fs['d'])) {
				foreach($fs['d'] as $dir_name => &$childs) {
					if($dir_name != 'backups') {
						$this->add_backup_data($fp, $fs['d'][$dir_name], $path.$dir_name.DS);
					}
				}
			}
		}
		
		// -- записываем содержимое файлов в zip
		private function add_backup_zip(&$zip, &$fs, $path = '') {
			if(!empty($fs['f'])) {
				foreach($fs['f'] as $file_name => $file_size) {
					$file_path = $path.$file_name;
					if(file_exists(INDEX.$file_path)) {
						$zip->addFile(INDEX.$file_path, $file_path);
					}
				}
			}
			if(!empty($fs['d'])) {
				foreach($fs['d'] as $dir_name => &$childs) {
					if($dir_name != 'backups') {
						$this->add_backup_zip($zip, $fs['d'][$dir_name], $path.$dir_name.DS);
					}
				}
			} else {
				$zip->addEmptyDir(substr($path, 0, -1));
			}
		}
		
	}
?>
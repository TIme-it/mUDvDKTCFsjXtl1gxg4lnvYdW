<?php
	class backup_controller extends application_controller {
				
		public function index() {
			@mkdir(INDEX.'backups');
			
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
				
			// -- список файлов backup'a
			$data['list'] = $this->backup->getBackupList();
			$temp[] = array();
			if(!empty($data['list'])) {
				foreach($data['list'] as $i => &$item) {
					$time = explode('_',substr($item['file_name'],0,19));
					$time = strtotime(vsprintf('%4d-%02d-%02d %02d:%02d:%02d', $time));
					$item['file_size'] = $this->getFormatFileSize($item['file_size']);
					$item['time']      = join('.',array_reverse(explode('_',substr($item['file_name'],0,10))));
					$item['file_name_url'] = join('',explode('_',substr($item['file_name'],0,19)));
					$temp[$time] = $item;
				}
			}
			krsort($temp);
			unset($temp[0]);
			$data['list'] = array_values($temp);
			unset($temp);
			$this->html->render('backup/index.html', $data, 'content_path');
		}
		
		public function build() {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			$today = date('Y_m_d');
			$data['list'] = $this->backup->getBackupList();
			if(!empty($data['list'])) {
				foreach($data['list'] as $i => &$item) {
					if(substr($item['file_name'],0,10) == $today) {
						$this->session->set('alert', 'Резервная копия за сегодня уже создана');
						$this->url->redirect('/admin/backup/');
					}
				}
			}
			$this->trash_controller->clear_files();
			$this->backup->build_backup(date('Y_m_d_H_i_s'));
			$this->session->set('alert', 'Резервная копия была успешно создана');
			$this->url->redirect('/admin/backup/');
		}
		
		public function delete($time_dump) {
			//Проверяем права
			$role_id = $this->session->get('role_id');
			$user_id = $this->session->get('admin');
			if ( ($user_id !== 0) || ($role_id !== 0) ) $this->role_controller->AccessError();
			
			$data['list'] = $this->backup->getBackupList();
			if(!empty($data['list'])) {
				foreach($data['list'] as $i => &$item) {
					$dump = join('',explode('_',substr($item['file_name'],0,19)));
					if($time_dump == $dump) {
						$path = BACKUPS.$item['file_name'];
						if(file_exists($path)) unlink($path);
					}
				}
			}
			$this->url->redirect('/admin/backup/');
		}
		
		public function download($time_dump) {
			// -- возможность скачать архив заблокирована
			$this->url->redirect('::referer');
			
			$data['list'] = $this->backup->getBackupList();
			if(!empty($data['list'])) {
				foreach($data['list'] as $i => &$item) {
					$dump = join('',explode('_',substr($item['file_name'],0,19)));
					if($time_dump == $dump) {
						$path = BACKUPS.$item['file_name'];
						if(file_exists($path)) {
							$file = explode('.', $item['file_name']);
							$ext  = $file[count($file)-1];
							unset($file[count($file)-1]);
							$file = implode('.', $file);
							$info = array(
								'id'        => $file,
								'filename'  => $file,
								'filesize'  => filesize($path),
								'filetype'  => 'application/x-zip-compressed',
								'extension' => $ext,
							);
							$this->file->download($info, BACKUPS);
							die();
						}
					}
				}
			}
			$this->url->redirect('/admin/backup/');
		}
		
		private function getFormatFileSize($file_size) {
			$arr_sizes = array('байт','Кб','Мб','Гб');
			$i = 0;
			while($file_size > 1000) {
				$file_size /= 1024;
				$i++;
			}
			return round($file_size, 2).' '.$arr_sizes[$i];
		}
		
	}
?>
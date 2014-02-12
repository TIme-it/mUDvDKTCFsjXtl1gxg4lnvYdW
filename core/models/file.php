<?php
	class file extends application_controller {

		// TODO переписать метод delete
		
		protected $allow_extensions;
		
		public function __construct() {
			$this->disallow_extensions = array(
				'php', 'pl', 'exe', 'dll', 'so', 'pu', 'sql', 'js'
			);
		}
		
		public function upload($name, $tmp_name, $filename) {
			switch($name) { 
				case 'img' :       $this->path = $this->config->get('img', 'files');         break;
				case 'image':      $this->path = $this->config->get('images', 'files');      break;
				case 'image_main': $this->path = $this->config->get('images_main', 'files'); break;
				case 'file':       $this->path = $this->config->get('file', 'files');        break;
				default: 
					$this->path = $this->config->get($name, 'files');
					if(empty($this->path)) {
						$this->path = $this->config->get('upload', 'files');
					}
					break;
			}
			if(!in_array($this->getExt($filename), $this->disallow_extensions)) {
				return move_uploaded_file($tmp_name, $this->path.$filename);
			}
			return false;
		}

		public function download($info, $upload_path) {
			$file_path = $upload_path.$info['id'].'.'.$info['extension'];
			if(file_exists($file_path)) {
				header("Pragma: public"); 
				header("Expires: 0"); 
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
				header("Cache-Control: private", false);
				header("Content-Type: ".$info['filetype']); 
				header("Content-Disposition: attachment; filename=".str_replace(' ','_',$info['filename']).'.'.$info['extension']); 
				header("Content-Transfer-Encoding: binary"); 
				header("Content-Length: ".$info['filesize']);

				readfile($file_path);
				die();
			}
			$this->url->redirect('::referer');
		}
		
		public function delete($name,$id) {
			if($name == 'user_pic') $this->path = $this->config->get('up_pics');
			elseif($name == 'company_pic') $this->path = $this->config->get('up_companies');
			if (file_exists($this->path.$id.'.jpg')) unlink($this->path.$id.'.jpg');
		}
		
		public function getFileInfo($filename) {
			if(in_array($this->getExt($filename), $this->disallow_extensions)) return false;
			return array(
				'extension' => $this->getExt($filename),
				'filename'  => $filename
			);
		}
		
		public function wget($filename,$link) {
			$fx = fopen($filename,"w+");
			$file = file_get_contents($link);
			fputs($fx,$file);
			fclose($fx);
		}
		
		public function toFile($file, $content, $mode = 'w+', $attr = 0755, $has_protect = true) {
			if($has_protect && in_array($this->getExt($file), $this->disallow_extensions)) return false;
			$fx = fopen($file, $mode, $attr);
			fputs($fx, $content);
			fclose($fx);
		}
		
		public function getExt($file_name) {
			$parts = explode('.', $file_name);
			return $parts[count($parts) - 1];
		}
		
	}
?>
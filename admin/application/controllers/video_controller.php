<?php
	class video_controller extends application_controller {
		
		const TOKEN_REFRESH = 1800;
		
		private $youtube_auth = null;
		private $auth_token   = null;
		private $auth_name    = null;
		private $develop_key  = null;
		
		
		public function __construct() {
			// -- получаем <developer_key>
			$this->develop_key = $this->config->get('dkey', 'ytube');
			
			// -- получаем <authentication_token> если его нет или он устарел
			$yt_auth = $this->session->get('yt_auth');
			if(empty($yt_auth['time']) || $yt_auth['time'] < time() - self::TOKEN_REFRESH) {
				$user = $this->config->get('user', 'ytube');
				$pass = $this->config->get('pass', 'ytube');
				$yt_auth['token'] = $this->auth($user, $pass);
				$yt_auth['time']  = time();
				$this->session->set('yt_auth', $yt_auth);
			}
			$this->auth_token = explode("\n", $yt_auth['token']);
			$this->auth_name  = $this->auth_token[1];
			$this->auth_name  = explode("=", $this->auth_name);
			$this->auth_name  = mb_strtolower($this->auth_name[1]);
			$this->auth_token = explode("=", $this->auth_token[0]);
			$this->auth_token = $this->auth_token[1];
		}
		
		public function upload($ref) {
			if(!empty($_GET['id']) && !empty($_GET['uid']) && !empty($_GET['status']) && $_GET['status'] == '200') {
				$video = array(
					'link'   => $_GET['id'],
					'date'   => date('Y-m-d H:i:s'),
					'state'  => '1',
					'width'  => '480',
					'height' => '385',
				);
				$this->db->update('videos', $video, (int)$_GET['uid']);
			}
			$this->session->set('alert', 'Видео было успешно загружено');
			// -- редирект
			$to = empty($ref) ? '/admin/' : base64_decode($ref);
			header('Location: '.$to);
			die();
		}
		
		// -- аутентификация на стороне YouTube
		private function auth($user, $pass) {
			$url    = 'https://www.google.com/youtube/accounts/ClientLogin';
			$data[] = 'Email='.urlencode($user);
			$data[] = 'Passwd='.urlencode($pass);
			$data[] = 'service='.urlencode('youtube');
			$data[] = 'source='.urlencode('YouTube Video Module');
			$data = join('&', $data);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
			$youtube_auth = curl_exec($curl);
			curl_close($curl);
			return $youtube_auth;
		}

		public function get_upload_data($data) {
			$data = '<?xml version="1.0"?>'."\r\n".
					'<entry xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:yt="http://gdata.youtube.com/schemas/2007">'."\r\n".
					'	<media:group>'."\r\n".
					'		<media:title type="plain">'.htmlspecialchars($data['title']).'</media:title>'."\r\n".
					'		<media:description type="plain">'.htmlspecialchars($data['desc']).'</media:description>'."\r\n".
					'		<media:category scheme="http://gdata.youtube.com/schemas/2007/categories.cat">'.htmlspecialchars($data['cat']).'</media:category>'."\r\n".
					'	</media:group>'."\r\n".
					'</entry>'."\r\n";
			if($fp = fsockopen("gdata.youtube.com", 80, $errno, $errstr, 30)) {
				$request  = "POST /action/GetUploadToken HTTP/1.1\r\n";
				$request .= "Host: gdata.youtube.com\r\n";
				$request .= "Content-Type: application/atom+xml; charset=UTF-8\r\n";
				$request .= "Content-Length: ".strlen($data)."\r\n";
				$request .= "Authorization: GoogleLogin auth=".$this->auth_token."\r\n";
				$request .= "X-GData-Key: key=".$this->develop_key."\r\n";
				
				$request .= "\r\n";
				$request .= $data;
				
				socket_set_timeout($fp, 10);

				fputs($fp, $request, strlen($request));
				$response  = '';
				while($add = fread($fp, 1024)) {
					$response .= $add;
				}
				fclose($fp);
				if(!empty($response)) {
					preg_match('/<url>(.*)<\/url>/U', $response, $data);
					if(!empty($data[1])) {
						$return['url'] = $data[1];
					}
					preg_match('/<token>(.*)<\/token>/U', $response, $data);
					if(!empty($data[1])) {
						$return['token'] = $data[1];
					}
					return empty($return) ? false : $return;
				}
			}
			return false;
		}
		
		public function get_token() {
			$video['title'] = empty($_POST['title']) ? false : trim($_POST['title']);
			$video['desc']  = empty($_POST['desc'])  ? false : trim($_POST['desc']);
			$video['cat']   = empty($_POST['cat'])   ? false : trim($_POST['cat']);
			$json['result'] = 'error';
			if($video['title'] && $video['desc'] && $video['cat']) {
				$response = $this->get_upload_data($video);
				if(!empty($response)) {
					$json = $response;
					$video_data = array(
						'pid'       => (int)$_POST['pid'],
						'module_id' => (int)$_POST['mid'],
						'title'     => $video['title'],
						'state'     => '0'
					);
					$id = $this->db->insert('videos', $video_data);
					$json['id']     = $id;
					$json['result'] = 'ok';
				}
			}
			echo json_encode($json);
			die();
		}
		
		public function delete($id) {
			$link = $this->db->get_one('SELECT link FROM videos WHERE id = '.$id);
			if($link) {
				// -- удаляем на стороне YouTube
				if($fp = fsockopen("gdata.youtube.com", 80, $errno, $errstr, 30)) {
					$request  = "DELETE /feeds/api/users/".$this->auth_name."/uploads/".$link." HTTP/1.1\r\n";
					$request .= "Host: gdata.youtube.com\r\n";
					$request .= "Content-Type: application/atom+xml\r\n";
					$request .= "Authorization: GoogleLogin auth=".$this->auth_token."\r\n";
					$request .= "X-GData-Key: key=".$this->develop_key."\r\n\r\n";
					socket_set_timeout($fp, 10);
					fputs($fp, $request, strlen($request));
					fclose($fp);
					$this->db->delete('videos', $id);
				}
			}
			die();
		}

	}
?>
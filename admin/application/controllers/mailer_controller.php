<?php
	class mailer_controller extends application_controller {

		
		
		public function index(){
			$data = array();
			$this->html->render('mailer/index.html',$data,'content_path');
		}
		public function view_group(){
			$data['groups']= $this->mailer->getGroups();

			$this->html->render('mailer/view_groups.html',$data,'content_path');
		}
		public function edit_group($id=false){
		

			
			if(!empty($_POST)){
				if(!empty($_POST['id'])){
					$data['title'] = $_POST['title'];
					$id = $_POST['id'];
					$this->db->update('mail_groups',$data,(int)$_POST['id']);
				}
				else{
					$data['title'] = $_POST['title'];
					$id = $this->db->insert('mail_groups', $data);
				}
				if(!empty($_POST['users_group'])){
					foreach ($_POST['users_group'] as $key => &$users) {
						
						
						$user['id'] =(int) $users;
						$user['email'] = $this->mailer->getUserMail($user['id']);
					
						$this->db->query('INSERT IGNORE INTO users_mail_group VALUES('.(int)$id.','.(int)$user['id'].',1)');
					}

				}
				if(!empty($_POST['emails'])){
					$mails = explode(" ", $_POST['emails']);

					foreach ($mails as $key => &$item) {
							$this->db->query('INSERT IGNORE INTO users_mail_group VALUES('.(int)$id.',0,'.$this->db->escape($item).')');
					}
				}
				$this->url->redirect('/admin/mailer/edit_group/'.$id);				
			}
			else{
				
				if(!empty($id)){
					$data['users_and_mails'] = $this->mailer->getGroupInfo($id);
					$data['emails']="";
					//var_dump($data['users_and_mails']);
					//die();
					if(!empty($data['users_and_mails'])){
						foreach ($data['users_and_mails'] as $key => &$u_m) {
							if($u_m['id']!=0){
								$data['prelist'][] = $u_m;
							}
							else{
								$data['emails'].= $u_m['zero_mail'].' ';
							}
						}
					}
				

					$data['title'] = $this->mailer->getGroupTitle($id);
					
					$data['id'] = $id;
					
					$this->html->render('mailer/edit_group.html',$data,'content_path');
				}
				else{
					$data = array();
				}
					//$data = array();
					$data['user_list'] = $this->mailer->getAllUsers();
					$this->html->render('mailer/edit_group.html',$data,'content_path');
				
			}


		}
		public function create_task(){
			$mails = array();
			
			if(!empty($_POST)){
				$task_data['title'] = $_POST['title'];
				if(!empty($_POST['emails'])){
					$mails = explode(" ", $_POST['emails']);
					/*foreach ($mails as $key => &$item) {
							$this->db->query('INSERT IGNORE INTO users_mail_group VALUES('.(int)$id.',0,'.$this->db->escape($item).')');
					}*/
				}
				if(!empty($_POST['all'])){
						$users = $this->mailer->getAllUsers();
						foreach ($users as $i => &$item) {
							$mails[] = $item['email'];
						}
					}
				else{
						if(!empty($_POST['users_group'])){
							foreach ($_POST['users_group'] as $key => &$users) {
							
							
								$user['id'] =(int) $users;
						//	$user['email'] = 
						
								$mails[] = $this->mailer->getUserMail($user['id']);
							}

						}
					}
					
				if(!empty($_POST['groups'])){
						$data['users_and_mails'] = $this->mailer->getGroupInfo((int)$_POST['groups']);
						if(!empty($data['users_and_mails'])){
							foreach ($data['users_and_mails'] as $key => &$u_m) {
								if($u_m['id']!=0){
									$mails[] = $this->mailer->getUserMail($u_m['id']);
								}
								else{
									$mails[] = $u_m['zero_mail'];
								}
							}
						}
					//	$mails[] = $this->mailer->getUserMail($user['id']);
					}


				
			}

			if(!empty($mails)){
				$task_data['count'] = count($mails);
				$task_data['status'] = 0;
			}
			else{
				$task_data['status'] = 2;
				$task_data['count'] = 0;				
			}
			
			$task_data['date']  = time();
			if(!empty($_POST['priority'])){
				$letter['priority'] = $_POST['priority'];
				$task_data['priority'] = $_POST['priority'];

			}
			else
			{
				$letter['priority'] = 1;
				$task_data['priority'] = 1;
			}


			if(!empty($_POST)){
				$task_id = $this->db->insert('mail_task',$task_data);
			}
			if((!empty($task_id))&&(!empty($mails))){
				
				$letter['text'] = $_POST['text'];
				$letter['task_id'] = $task_id;
				$letter['try_count'] = (int)$_POST['try_count'];
				$letter['status']    = 0;

				foreach ($mails as $key => $email) {
					$letter['email'] = $email;
					$this->db->insert('letters',$letter,true);
				}
				$this->url->redirect('/admin/mailer/view_tasks/');
			}
			else{
				$data['groups'] = $this->mailer->getGroups();
				$this->html->render('mailer/create_task.html',$data,'content_path');
			}
			
		}
		public function view_tasks(){
			$data['task_list'] = $this->mailer->getTasks();
			$this->html->render('mailer/view_tasks.html',$data,'content_path');
		}
		public function view_one_task($id){
			$send_count = 0;
			$fail_count = 0;
			$w8_count   = 0;
			$data['letters'] = $this->mailer->getLettersByTask($id);
			if(!empty($data['letters'])){
				foreach ($data['letters'] as $i => &$item) {
					switch ($item['status']) {
						case '0':
							$item['state'] = "в очереди";
							$w8_count++;
							break;
						case '1':
							$item['state'] = "отправлено";
							$send_count++;
							break;
						case '2':
							$fail_count++;
							$item['state'] = "не отправлено";
							break;	
						
						default:
							# code...
							break;
					}
				}
			}
			
			$this->html->render('mailer/view_one_task.html',$data,'content_path');
		}
		public function autocomplete_users(){
			// Версия для fcbkcomplete
			$data = array();
			if (!empty($_GET['tag']) && (strlen($_GET['tag']) > 3)) {	
				$data['str']=$_GET['tag'];							
				$users = $this->mailer->getUsersResults($data,1,10);
				$data = array();
				if (!empty($users)) {
					foreach($users['list'] as $user){
						$data[] = array(
							'value' => $user['sname'].' '.$user['fname'],
							'key' => $user['id']
						);
					}
				}
				$answer = array(
					'status' => 'ok',
					'res' => $data
				);
			} else {
				$answer = array(
					'status' => 'error',
					'res' => 'Некорректный запрос'
				);
			}
			die(json_encode($data));
		}
		public function send(){
			$letters = $this->mailer->getLetters(100);
			foreach ($letters as $i => &$one_letter) {
				switch ($one_letter['try_count']) {
					case '1':
						if(!$this->mail->send_mail($one_letter['email'],$one_letter['text'],$one_letter['title'])){
							$new['try_count'] = 0;
							$new['status'] = 2;
							$this->db->update('letters', $new, $one_letter['id']);
						}
						else{
							$new['try_count'] = $one_letter['try_count']-1;
							$new['status'] = 1;
							$this->db->update('letters', $new, $one_letter['id']);
						}
						break;
					
					default:
						if(!$this->mail->send_mail($one_letter['email'],$one_letter['text'],$one_letter['title'])){
							$new['try_count'] = $one_letter['try_count']-1;
						
							$this->db->update('letters', $new, $one_letter['id']);
						}
						else{
							$new['try_count'] = $one_letter['try_count']-1;
							$new['status'] = 1;
							$this->db->update('letters', $new, $one_letter['id']);
						}	
						break;
				}
				
			}
		}
	}
?>
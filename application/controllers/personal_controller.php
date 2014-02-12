<?php
	class personal_controller extends application_controller {
		
		private $path;
		private $characters;
		private $module_id = 9;
		
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('personal', 'files');
			$this->characters = array(
				'Отдел'             => 'department',
				'Должность'         => 'post',
				'Городской телефон' => 'phone',
				'Рабочий телефон'   => 'workPhone',
				'Мобильный телефон' => 'mobilePhone',
				'E-mail'            => 'email',
				'ICQ'               => 'icq',
				'Адрес'             => 'address',
				'О себе'            => 'about',
			);
		}
		
		public function index($pid, $id = false) {
			// -- данные о разделе
			$info = $this->personal->getMainInfo($pid);
			if(empty($info)) {
				$this->main_controller->page_404();
				return false;
			}
			
			$this->active_main_id = $pid;
			
			// SEO-блок
			$this->html->tpl_vars['meta_description'] = $info['description'];
			$this->html->tpl_vars['meta_keywords']    = $info['keywords'];
			
			if(empty($id)) {
				$personal['title_page'] = $info['title_page'];
				
				// -- текст страницы
				$personal['text'] = '';
				if(file_exists($this->path.$pid.'_volume.txt')) {
					$personal['text'] = file_get_contents($this->path.$pid.'_volume.txt');
				}
				$is_long_text = (mb_strlen(strip_tags($personal['text']), 'UTF-8') > 300);
				
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($pid, 0);
				
				// -- добавляем данные для блока "модули" (распечатать, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($pid, 0, $info['title'], $is_long_text);
				
				
				if(file_exists($this->path.$pid.'_main.jpg')) {
					$personal['company_img'] = '<h2>Наша компания</h2><img src="/application/includes/personal/'.$pid.'_main.jpg" class="companyFoto" />';
				}
				
				// -- директор
				$dir_id = 0;
				if(file_exists($this->path.$pid.'.ser')) {
					$dir_info = unserialize(file_get_contents($this->path.$pid.'.ser'));
					if(!empty($dir_info['id'])) {
						$dir_id = $dir_info['id'];
						$dir_info['img'] = '<img src="/application/includes/images/no_personal_big.jpg" width="200" />';
						if(file_exists($this->path.$dir_id.'_b.jpg')) {
							$dir_info['img'] = '<img src="/application/includes/personal/'.$dir_id.'_b.jpg" width="200" />';
						}
						$direktor = $this->personal->getOnePerson($dir_id);
						$dir_info = array_merge($dir_info, $direktor);
						$dir_info['list_char'] = array();
						foreach($this->characters as $title => $name) {
							if(!empty($dir_info[$name])) {
								$dir_info['list_char'][] = array(
									'lc_title' => $title.':',
									'lc_value' => $dir_info[$name]
								);
							}
						}
						$personal['direktorBlock'] = $this->html->render('personal/direktorWord.html', $dir_info);
					}
				}
				
				// -- выбор шаблона
				$template = (empty($info['template'])) ? 'personalList' : $info['template'];

				// -- список персонала
				$personal['personalList'] = '';
				$personal_list = $this->personal->getPersonalList($pid, $dir_id);
				if(!empty($personal_list)) {
					foreach($personal_list as $department => &$department_item) {
						$personal['personalList'] .= (empty($department) ? '' : '<h2>'.$department.'</h2>');
						foreach($department_item['list'] as $i => &$item) {
							// -- черезполосица
							$item['each_odd'] = ($i % 2) ? 'odd' : 'each';
							// -- ссылка на визитную карточку
							if(!empty($item['about'])) {
								$item['link_pref'] = '<a href="/personal/'.$pid.'/'.$item['id'].'/">';
								$item['link_post'] = '</a>';
							}
							// -- портрет
							$item['img'] = '<img src="/application/includes/images/no_personal.jpg" width="60" height="80" title="'.$item['fio'].'" />';
							if(file_exists($this->path.$item['id'].'.jpg')) {
								$item['img'] = '<img src="/application/includes/personal/'.$item['id'].'.jpg" class="photo" width="60" height="79" title="'.$item['fio'].'" />';
							}
							// -- уникально для шаблона
							switch($template) {
								case 'personalList':
									$item['desc1']  = $item['phone'];
									$item['desc1'] .= (empty($item['email'])) ? '' : ((empty($item['desc1']) ? '' : ', ').'<a href="mailto:'.$item['email'].'">'.$item['email'].'</a>');
									break;
							}
						}
						$personal['personalList'] .= $this->html->render('personal/'.$template.'.html', $department_item);
					}
				}
				unset($personal_list);
				
				// -- дополнительные модули
				$personal['galleryBlock'] = $this->all_controller->images($pid, 0);
				$personal['filesBlock']   = $this->all_controller->files($pid,  0);
				// var_dump($personal);
				// die('<');
				$this->html->render('personal/main.html', $personal, 'content');
			} else {
				// -- подробно о персоне
				$person = $this->personal->getOnePerson($id);
				
				$person['text'] = '';
				$is_long_text = (mb_strlen(strip_tags($person['text']), 'UTF-8') > 300);
				// -- добавляем Яндекс.Карты, если нужно
				$this->all_controller->buildYandexMaps($id, $this->module_id);
				// -- добавляем данные для блока "модули" (распечатать, отзывы, вакансии, об. связь, подразделы)
				$this->all_controller->buildModulesBlock($id, $this->module_id, $person['fio'], $is_long_text);
				unset($person['print']);
				
				// -- контактные данные
				foreach($this->characters as $title => $name) {
					if(!empty($person[$name])) {
						$person['list_char'][] = array(
							'lc_title' => $title.':',
							'lc_value' => $person[$name]
						);
					}
				}
				
				// -- фото
				$person['img'] = '<img src="/application/includes/images/no_personal_big.jpg" width="200" />';
				if(file_exists($this->path.$id.'.jpg')) {
					$person['img'] = '<img src="/application/includes/personal/'.$dir_id.'_b.jpg" width="200" />';
				}
				
				$person['gallery_block'] = $this->all_controller->images($id, $this->module_id);
				$person['files_block']   = $this->all_controller->files($id,  $this->module_id);
				
				$this->html->render('personal/person.html', $person, 'content');
			}
		}
		
	}	
?>
<?php
	class all_controller extends application_controller {
		
		// -- генерация блока "Настройки"
		public function generateSettingsBlock($pid, $mid) {
			$data['directories']    = $this->getStructSelect($pid);
			$data['module_list']    = $this->main->getModuleList($mid);
			$data['inmenu_checked'] = $this->main->getInMenuChecked($pid);

			$data['url_display'] = 'style="display:none"';
			if($mid == 6) {
				$data['link'] = $this->db->get_one('SELECT link FROM main WHERE id = '.(int)$pid);
				$data['url_display'] = '';
			}
			
			return $this->html->render('submodules/settings.html', $data);
		}
		public function rus2translit($string)
			{ 
			$string = str_replace(" ", '_',$string);
			$converter = array(
		        'а' => 'a',   'б' => 'b',   'в' => 'v',
		        'г' => 'g',   'д' => 'd',   'е' => 'e',
		        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
		        'и' => 'i',   'й' => 'y',   'к' => 'k',
		        'л' => 'l',   'м' => 'm',   'н' => 'n',
		        'о' => 'o',   'п' => 'p',   'р' => 'r',
		        'с' => 's',   'т' => 't',   'у' => 'u',
		        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
		        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
		        'ы' => 'y',   
		        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
		 
		        'А' => 'A',   'Б' => 'B',   'В' => 'V',
		        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
		        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
		        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
		        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
		        'О' => 'O',   'П' => 'P',   'Р' => 'R',
		        'С' => 'S',   'Т' => 'T',   'У' => 'U',
		        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
		        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
		        '?' => '', '%' => '', '!' => '', '«' => '', '»' => '', ' ' => '_',
		          'Ы' => 'Y',  '"' =>'', 'ь'=> '', 'Ъ'=>'', 'Ь'=>'', 'ъ'=>'', '.'=>'', ','=>'',
		        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
		        ':'=>'','&'=>'',
		    );
		  
			return $str = iconv ( "UTF-8", "UTF-8//IGNORE", strtr ( $string, $converter ) );  
		    
		   
		}
		// -- генерация блока "Модули"
		public function generateModulesBlock($pid, $mid, $isset_base = true, $isset_sub = true) {
			$page = ($pid) ? $this->all->getModulesBlockInfo($pid, $mid) : array();

			// -- подготовка данных
			if($isset_base) {
				// -- сквозные параметры
				$page['print_word']     = $this->config->get('print_word',     'site');
				$page['feedback_email'] = $this->config->get('feedback_email', 'site');
				
				if(!empty($page['print']) || ($pid == 0 && in_array($mid, array(2)))) {
					$page['print_checked'] = 'checked="checked"';
					$page['print_display'] = 'class="inline_display"';
				}
				// $page['feedback_list'] = $this->all->getFeedBackTypes();
				// if(!empty($page['feedback'])) {
				// 	if(!empty($page['feedback_list'])){
				// 		foreach ($page['feedback_list'] as $i => &$item) {
				// 			if((int)$item['id']==(int)$page['feedback']){
				// 				$item['selected'] = true;

				// 			}
				// 		}
				// 	}
					
				// }
			}
			if($isset_sub && !empty($page['subsection'])) {
				$page['subsection_checked'] = 'checked="checked"';
			}
			if(!empty($page['feedback'])){	
				$page['feedback_checked'] = ($page['feedback'] == 1) ? 'checked="checked"': false;
			}
			// -- выбор шаблона
			if($isset_base && $isset_sub) {
				return $this->html->render('submodules/modules_base_sub.html', $page);
			} elseif($isset_base) {
				return $this->html->render('submodules/modules_base.html', $page);
			} elseif($isset_sub) {
				return $this->html->render('submodules/modules_sub.html', $page);
			}
			return '';
		}
		
		// -- получаем структуру сайта и формируем <select>
		private function getStructSelect($id) {
			$structure = $this->main->getDirectoriesTree();
			$html = '';
			$pid  = $this->db->get_one('SELECT pid FROM main WHERE id = '.(int)$id);
			$html .= '<select name="pid">';
			$html .= '<option value="0"'.($pid == 0?' selected="selected"':'').'>Корень</option>';
			$this->getStructSelectReq($structure, $html, $id, $pid, 0);
			$html .= '</select>';
			return $html;
		}
		
		private function getStructSelectReq(&$structure, &$html, $id, $pid, $deep) {
			if(empty($structure)) return false;
			$pref = str_repeat('&mdash;', $deep + 1).'&nbsp;';
			foreach($structure as $i => &$item) {
				if($item['id'] != $id) {
					$html .= '<option value="'.$item['id'].'"'.($item['id'] == $pid?' selected="selected"':'').'>'.$pref.$item['title'].'</option>';
					if(!empty($item['childs'])) {
						$this->getStructSelectReq($item['childs'], $html, $id, $pid, $deep + 1);
					}
				}
			}
		}

	}
?>
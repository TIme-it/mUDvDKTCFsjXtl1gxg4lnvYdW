<?php
	// -- вспомогательный контоллер для работы с Яндекс.Картами	
	class maps_controller extends application_controller {
		
		private $alert = '';
		
		private $ALERT_INSERT_NEW   = 'Новый пункт на карту добавлен';
		private $ALERT_ERROR_DOUBLE = 'Невозможно добавить метку:\nметка с текущей широтой и долготой уже существует';
		private $ALERT_UPDATE       = 'Метка на карте изменена';
		
		
		//Создание карты
		public function create_map($pid, $mid, $title) {
			$map_data = array(
				'pid'       => (int)$pid,
				'module_id' => (int)$mid,
				'title'     => urldecode($title),
			);
			$map_id = $this->db->insert('maps', $map_data);
			die(json_encode($map_id));
		}
		
		//Удаление карты
		public function delete_map($id) {
			$this->db->delete('maps_placemarks', array('pid' => $id));
			$this->db->delete('maps', $id);
			die();
		}
		
		//Редактирование карты
		public function edit_map($id) {
			$marks['marks_list'] = $this->all->getPlacemarks($id);
			$marks['mapid'] = $id;
			$html = $this->html->render('submodules/yandex_maps_edit.html', $marks);
			
			die(json_encode($html));
		}
		
		//Возвращает форму добавления метки
		public function getMarkNew() {
			$html = $this->html->render('submodules/yandex_create_mark.html');
			die(json_encode($html));
		}
		
		//Возвращает форму добавления метки
		public function getMarkEdit() {
			$html = $this->html->render('submodules/yandex_edit_mark.html');
			die(json_encode($html));
		}
		
		//Сохранение точки
		public function saveMark() {
			$data = array(
				'title' => $_POST['title'],
				'note' => $_POST['note'],
				'latitude' => $_POST['lat'],
				'longitude' => $_POST['lon'],
			);
			$this->db->update('maps_placemarks', $data, (int)$_POST['id']);
			die();
		}
		
		
		//Сохранение точки
		public function addMark() {
			$data = array(
				'pid' => (int)$_POST['pid'],
				'title' => $_POST['title'],
				'note' => $_POST['note'],
				'latitude' => $_POST['lat'],
				'longitude' => $_POST['lon'],
			);
			$id = $this->db->insert('maps_placemarks', $data);
			die(json_encode($id));
		}
		
		
		public function delete_mark($id) {
			$this->db->delete('maps_placemarks', $id);
			die();
		}
		
		
		// -- метод построения блока Яндекс.Карты для отображения в шаблонах
		public function generateBlock($pid, $mid) {
			$html = '';
			$data = array(
				'pid'   => $pid,
				'mid' => $mid,
				'map_list'  => $this->all->getMaps($pid, $mid)
			);
			if(!empty($data['map_list'])) {
				// -- получаем id всех карт в разделе
				$map_id_arr = array();
				foreach($data['map_list'] as $i => &$item) {
					$map_id_arr[] = $item['id'];
				}
				
				// -- список меток
				$data['placemark_list'] = $this->all->getPlacemarks($map_id_arr);
				if(!empty($data['placemark_list'])) {
					foreach($data['placemark_list'] as $i => &$item) {
						$item['title_map'] = $this->db->get_one('SELECT title FROM maps WHERE id = '.$item['pid']);
					}
				}
			}
			return $this->html->render('submodules/yandex_maps.html', $data);
		}
		
		// -- общий метод завершения большинства других ajax-методов: вывод подмодуля "Яндекс.Карт"
		private function showMapsBlock($pid, $mid) {
			echo $this->generateBlock($pid, $mid, true);
			echo $this->html->render('layouts/wysiwyg_ajax.html', array());
			if($this->alert != '') {
				echo '<script type="text/javascript">alert("'.$this->alert.'");</script>';
			}
			die();
		}
		
	}
?>
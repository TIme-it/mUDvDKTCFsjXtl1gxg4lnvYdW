<?php
	// -- класс-сосед каталога (basket_controller)
	class basket_controller extends application_controller {
		protected $path;
		protected $module_id = 8;
		protected $order_count = 5; // -- кол-во заказов на одной странице
			
		public function __construct() {
			parent::__construct();
			$this->path = $this->config->get('catalog', 'files');
		}
		
		// -- главная страница раздела
		public function index() {
			$this->layout = 'pages';
			// if (empty(self::$user_id)) {
			// 	$this->main_controller->page_404();
			// 	return false;
			// }
			
			$labels = array();
			$basket = $this->session->get('basket');

			// -- пуста корзина или нет?
			if(empty($basket)) {
				$labels['text'] = '<span>Корзина пуста</span>';
			} else {
				$products = $this->getBasketData($basket);
				if(empty($products['list'])) {
					$labels['text'] = '<span>Корзина пуста</span>';
				} else {
					foreach($products['list'] as $i => &$item) {
						// -- маленькая картинка
						$item['img_src'] = '/application/includes/images/no_photo.jpg';
						if($this->path.'s/'.$item['id'].'.jpg') {
							$item['img_src'] = '/application/includes/catalog/s/'.$item['id'].'.jpg';
						}
					}
					$labels['text'] = $this->html->render('basket/index_table.html', $products, 'container');
				}
			}
			// -- основной рендер
			// $this->html->tpl_vars['left_data'] = $this->html->render('layouts/facebook.html',array());	
			// $this->html->tpl_vars['left_data'] .= $this->calendar_controller->block();	
			// $this->html->tpl_vars['left_data'] .= $this->html->render('layouts/left_links.html',array());
			$this->html->render('basket/index.html', $labels, 'container');
		}
		
		// -- форма для вводка контактов
		public function order() {
			$this->layout='pages';
			// -- если мы зарегины, тогда переводим сразу на отправку данных
			// if(!empty(self::$user_id)) {
			// 	$this->url->redirect('/basket/send_order');
			// }
			// -- если мы не зарегины, просим ввести контакты
			$basket   = $this->session->get('basket');
			if(empty($basket)) $this->url->redirect('/basket/');
			$products = $this->getBasketData($basket);
			if(empty($products['list'])) $this->url->redirect('/basket/');
				
			$order['price_sum'] = $products['price_sum'];
			
			// -- основной рендер
			$this->html->render('basket/order.html', $order, 'container');
		}
		
		// -- подверждени заказа и отправка уведомления админу
		public function send_order() {
			// -- валидация формы, если мы не аутентифицированы
			// if(empty(self::$user_id) && empty($_POST['email']) && empty($_POST['phone'])) {
			// 	// -- не все данные заполнены
			// 	$this->session->set('alert', 'Некоторые поля были не заполнены');
			// 	$this->url->redirect('::referer');
			// }
			
			$basket = $this->session->get('basket');
			if(empty($basket)) {
				$this->session->set('alert', 'Невозможно совершить заказ, т.к. корзина пуста');
				$this->url->redirect('/basket/');
			}
			
			// -- данные корзины
			$basket_data = $this->getBasketData($basket);
			// -- формирование и сохранение данных о заказе в БД
			$order = array(
				'total_sum' => (float)preg_replace('/[^0-9\.]+/','',$basket_data['price_sum']),
				// 'user_id'   => self::$user_id,
				'time'      => time(),
				'ip'        => (empty($_SERVER['REMOTE_ADDR']) ? '0.0.0.0' : $_SERVER['REMOTE_ADDR']),
			);
			$order_id = $this->db->insert('order_history', $order);
			if(empty($order_id)) {
				$this->session->set('alert', 'При сохранении заказа произошла ошибка. Обратитесь к администратору сайта за помощью.');
				$this->url->redirect('/basket/');
			}
			foreach($basket_data['list'] as $i => &$prod) {
				$prod_data = array(
					'pid'        => $order_id,
					'title'      => $prod['title'],
					// 'catalog_id' => $prod['id'],
					'count'      => $prod['count'],
					'price'      => (float)preg_replace('/[^0-9\.]+/','',$prod['price']),
					'price_all'  => (float)preg_replace('/[^0-9\.]+/','',$prod['price_all']),
				);
				$this->db->insert('order_history_prod', $prod_data);
			}
			
			// -- формирование и отправка письма о заказе
			$basket_data['domain'] = $this->config->get('domain', 'site');
			if(empty(self::$user_id)) {
				$basket_data['name_type'] = 'Имя';
				$basket_data['name']      = $_POST['fio'];
				$basket_data['email']     = $_POST['email'];
				$basket_data['phone_row'] = '<tr><td><b>Телефон</b>:</td><td>'.$_POST['phone'].'</td></tr>';
			} 
			// else {
			// 	$user_data = $this->profile->getUser(self::$user_id);
			// 	$basket_data['name_type'] = 'Логин';
			// 	$basket_data['name']      = '<a href="http://'.$basket_data['domain'].'/profile/'.self::$user_id.'/">'.$user_data['login'].'</a>';
			// 	$basket_data['email']     = $user_data['email'];
			// 	$basket_data['phone_row'] = $user_data['phone_code'].' '.$user_data['phone'];
			// }
			
			$basket_data['date']   = date('d.m.Y');
			$basket_data['time']   = date('H:i:s');
			$basket_data['domain'] = $this->config->get('domain', 'site');
			
			$to      = $this->config->get('contact_email', 'site');
			$subject = 'На сайте '.$basket_data['domain'].' '.date('d.m.Y в H:i:s').' был совершен заказ ('.$basket_data['price_sum'].')';
			$letter  = $this->html->render('letters/catalog_order.html', $basket_data, false, true);
			
			
			$this->mail->send_mail($to, $letter, $subject);
			
			// -- отчиска корзины
			$this->session->del('basket');
			
			// -- редирект на страницу о успешном проведении заказа
			$this->url->redirect('/basket/confirm/');
		}
		
		// -- заказ подтвержден
		public function confirm() {
			$this->layout = 'pages';
			// if ( empty(self::$user_id) ) {
			// 	$this->main_controller->page_404();
			// 	return false;
			// }
			
			// $this->html->tpl_vars['left_data'] = $this->html->render('layouts/facebook.html',array());	
			// $this->html->tpl_vars['left_data'] .= $this->calendar_controller->block();	
			// $this->html->tpl_vars['left_data'] .= $this->html->render('layouts/left_links.html',array());
			
			$confirm['text'] = htmlspecialchars_decode($this->config->get('text_conf_order', 'site'));
			$this->html->render('basket/confirm.html', $confirm, 'container');
		}
		
		// -- история заказов
		public function history($order_id = 0) {
			// -- это раздел только для авторизовананых
			if(!self::$user_id) {
				$this->html->render('profile/login.html', array(), 'content');
				return true;
			}
			
			// $this->html->tpl_vars['left_data'] = $this->html->render('layouts/facebook.html',array());	
			// $this->html->tpl_vars['left_data'] .= $this->calendar_controller->block();	
			// $this->html->tpl_vars['left_data'] .= $this->html->render('layouts/left_links.html',array());
			
			// -- краткий список заказов
			if(empty($order_id)) {
				$page    = empty($_GET['page']) ? 0 : (int)$_GET['page']-1;
				$content = '<div style="margin-left: 30px">';
				$content .= '<h1>История заказов</h1>';
				

				$history_all_count = $this->catalog->getOrdersCount(self::$user_id);
				$history['pagination'] = $this->pagination_controller->index($history_all_count, $this->order_count);
				
				$history['list'] = $this->catalog->getOrders(self::$user_id, $this->order_count, $page);
				if(empty($history['list'])) {
					$content .= '<p>Вы пока ещё не сделали ни одного заказа</p>';
				} else {
					foreach($history['list'] as $i => &$item) {
						$item['num']       = sprintf('%04d', $item['id']);
						$item['date']      = $this->date->format($item['time']);
						$item['total_sum'] = $this->catalog->getPrice($item['total_sum']);
					}
					
					
					$content .= $this->html->render('basket/history_table.html', $history);
				}
				
				$content .= '<p><a href="/profile/">вернуться в профиль</a></p>';
				$content .= '</div>';
				
				$this->html->tpl_vars['content'] = $content;
			// -- подробно о конкретном заказе
			} else {
				$order['list'] = $this->catalog->getOrder(self::$user_id, $order_id);
				if(empty($order['list'])) {
					// -- вероятно, это не наш заказ
					$this->url->redirect('/basket/history/');
				}
				foreach($order['list'] as $i => &$item) {
					$item['price']     = $this->catalog->getPrice($item['price']);
					$item['price_all'] = $this->catalog->getPrice($item['price_all']);
				}
				$order['order_num'] = sprintf('%04d', $order['list'][0]['order_id']);
				$order['total_sum'] = $this->catalog->getPrice($order['list'][0]['total_sum']);
				$this->html->render('basket/history_order.html', $order, 'content');
			}
		}
		
		// -- ajax-метод: заносим товар в корзину
		public function add($id = 0) {
			// if ( empty(self::$user_id) ) {
			// 	$this->main_controller->page_404();
			// 	return false;
			// }
			
			$product = $this->catalog->getSubfamilyBasket($id);
			if(empty($product)) die(json_encode(array('result'=>false)));
			$basket = $this->session->get('basket');
			if(empty($basket)) $basket = array();
			if(empty($basket[$product['id']])) {
				$basket[$product['id']] = 0;
			}
			$basket[$product['id']]++;
			$this->session->set('basket', $basket);
			die(json_encode(array('result'=>true,'basket_block'=>$this->renderBasketBlock())));
		}
		
		// -- удаляем из корзины
		public function del($id = 0) {
			if(!empty($id)) {
				$basket = $this->session->get('basket');
				if(!empty($basket[$id])) {
					unset($basket[$id]);
					$this->session->set('basket', $basket);
				}
			}
			$this->url->redirect('::referer');
		}

		// -- добавляем в корзину через ajax
		public function addAjax($id = 0) {
			$product = $this->catalog->getSubfamilyBasket($id);
			if(empty($product)) die(json_encode(array('result'=>false)));
			$basket = $this->session->get('basket');
			if(empty($basket)) $basket = array();
			if(empty($basket[$product['id']])) {
				$basket[$product['id']] = 0;
			}
			$basket[$product['id']]++;
			$this->session->set('basket', $basket);
			if(!empty($basket)){
				$data = $this->getBasketData($basket);
			}

			echo $this->html->render('catalog/basket.html', $data);
			// echo $this->renderBasketBlock();
			die();
		}

		// -- удаляем из корзины через ajax
		public function delAjax($id = 0) {
			if(!empty($id)) {
				$basket = $this->session->get('basket');
				if(!empty($basket[$id])) {
					unset($basket[$id]);
					$this->session->set('basket', $basket);
				}
			}
			if(!empty($basket)){
				$data = $this->getBasketData($basket);
			}
			else{
				$data['result'] = '<span>В корзине нет товаров</span>';
			}
			echo $this->html->render('catalog/basket.html', $data);
			// echo $this->renderBasketBlock();
			die();
		}
		
		// -- изменяем кол-во товара
		public function count($action = 'hz', $id = 0) {
			if(($action == 'p' || $action == 'm') && !empty($id)) {
				$basket = $this->session->get('basket');
				if(!empty($basket[$id])) {
					switch($action) {
						case 'p': $basket[$id]++; break;
						case 'm': $basket[$id]--; break;
					}
				}
				$this->session->set('basket', $basket);
			}
			$this->url->redirect('/basket/');
		}
		
		// -- рендерим сквозной блок корзины
		public function renderBasketBlock() {
			// $basket = $this->session->get('basket');
			// if(!empty($basket)){
			// 	$data = $this->getBasketData($basket);
			// }
			// else{
			// 	$data['result'] = '<span>В корзине нет товаров</span>';
			// }

			// дополнительные услуги в корзине и их обработка
			$checking = $this->session->get('checking');
			$data['check_list'] = $this->catalog->getVisibleAddServices();
			if(!empty($data['check_list'])){
				foreach ($data['check_list'] as $i => &$item) {
					if (($checking[$item['id']]) != 'false' && (!empty($checking[$item['id']]))){
						$item['check_ajax'] = 'checked="checked"';
					}
					else {
						$item['check_ajax'] = '';
					}
				}
			}

			// var_dump($data);
			// die();
			// $result = '<span>В корзине нет товаров</span>';

			// if(!empty($basket)) {
			// 	$result = '<span>В корзине товаров: '.(int)array_sum($basket).' шт.</span>';
			// }

			return $this->html->render('catalog/basket.html', $data);
			// return $result;
		}
		
		// -- получаем данные о товарах из сессии корзины
		private function getBasketData($basket) {
			$products         = array();
			$products['list'] = array_keys($basket);
			$sql  = 'SELECT id, title, price, mod_id FROM subfamily '.
					'WHERE id IN ('.join(',', $products['list']).')';
			$products['list'] = $this->db->get_all($sql);
			if(!empty($products['list'])) {
				// -- подготовка данных для отображения в таблице
				$products['price_sum'] = 0;
				foreach($products['list'] as $i => &$item) {
					// -- расчет и представление стоимости
					$item['count']          = $basket[(int)$item['id']];
					$item['price_all']      = $item['price'] * $item['count'];
					$products['price_sum'] += $item['price_all'];
					$item['mod_title'] = $this->catalog->getModsTitle($item['mod_id']);
					$item['type_id'] = $this->catalog->getOneModsForType($item['mod_id']);
					// $item['price']          = $this->catalog->getPrice($item['price']);
					// $item['price_all']      = $this->catalog->getPrice($item['price_all']);
				}
				// $products['price_sum'] = $this->catalog->getPrice($products['price_sum']);
			}
			// var_dump($products);
			// die();
			return $products;
		}

		public function BasketAjax(){
			$basket = $this->session->get('basket');
			$basket[$_POST['id']] = $_POST['count'];
			$this->session->set('basket',$basket);
			die();
		}
		
		public function BasketCheckingAjax(){
			$checking = $this->session->get('checking');
			if (empty($checking)){
				$this->session->set('checking', $checking);
			}
			$checking = $this->session->get('checking');
			$checking[$_POST['id']] = $_POST['status'];
			$this->session->set('checking', $checking);
			die();
		}
	}
?>
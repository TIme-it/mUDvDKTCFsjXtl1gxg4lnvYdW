<?php
	class Date {
		
		public $month_names;
		
		public function __construct() {
			$this->month_names = array(
				1  => 'января',
				2  => 'февраля',
				3  => 'марта',
				4  => 'апреля',
				5  => 'мая',
				6  => 'июня',
				7  => 'июля',
				8  => 'августа',
				9  => 'сентября',
				10 => 'октября',
				11 => 'ноября',
				12 => 'декабря',
			);
			$this->month_names_ = array(
				1  => 'Январь',
				2  => 'Февраль',
				3  => 'Март',
				4  => 'Апрель',
				5  => 'Май',
				6  => 'Июнь',
				7  => 'Июль',
				8  => 'Август',
				9  => 'Сентябрь',
				10 => 'Октябрь',
				11 => 'Ноябрь',
				12 => 'Декабрь',
			);
		}
		
		public function today($format = true) {
			return ($format === true)?date('d').' '.$this->month(time()): date('d.m.Y');
		}

		public function format($ts) {
			if(!is_numeric($ts)) $ts = strtotime($ts);
			if(!$ts) return false;

			$today = date('d.m.y');
			$date = date('d.m.y', $ts);
			$time = date('H:i', $ts);
			$time = ($time=='00:00')? '' : ', '.$time;
			
			if($today == $date) return 'сегодня'.$time;
			if(($today - $date) == 1) return 'вчера'.$time;

			return date('j', $ts).' '.$this->month($ts).' '.date('Y',$ts).$time;
		}
		
		public function format2($ts) {
			if(!is_numeric($ts)) $ts = strtotime($ts);
			if(!$ts) return false;

			$date = date('d.m.y', $ts);
			
			return date('j', $ts).' '.$this->month($ts).' '.date('Y',$ts);
		}
		
		public function format3($ts) {
			if(!is_numeric($ts)) $ts = strtotime($ts);
			if(!$ts) return false;

			return date('d.m.Y', $ts);
		}
		

		public function format4($ts) {
			if(!is_numeric($ts)) $ts = strtotime($ts);
			if(!$ts) return false;
			return date('d | m', $ts);
		}
		
		public function format5($ts) {
			if(!is_numeric($ts)) $ts = strtotime($ts);
			if(!$ts) return false;

			return date('d.m.y', $ts);
		}
		
		public function format_list($ts) {
			return date('d.m.Y', strtotime($ts));
		}

		public function month($ts) {
			return $this->month_names[date('n',$ts)];
		}
		
		public function current_month($format = true) {
			return $format ? date('n') : $this->month_names_[date('n')];
		}
		
		public function next_month($format = true) {
			return $format ? (date('n') + 1) : $this->month_names[(date('n')+1)];
		}
		
		public function is_birth($ts) {
			if(!is_numeric($ts)) $ts = strtotime($ts);
			if(!$ts) return false;
			
			$date = date("d m",$ts); 

			return ($date == date("d m",time())) ? true : false;
		}
		
		public function sql_format($ts = false, $is_full = false) {
			if($ts === false)    $ts = time();
			if(!is_numeric($ts)) $ts = strtotime($ts);
			
			return date($is_full ? 'Y-m-d H:i:s' : 'Y-m-d', $ts);
		}

	}
?>
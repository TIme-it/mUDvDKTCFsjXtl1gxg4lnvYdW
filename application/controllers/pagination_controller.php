<?php
class pagination_controller extends application_controller {
	private $step = 4;

	//Метод показа блока
	public function index($all_count, $count_on_page, $url='', $param='page', $label='' ) {
		$page_count = ceil($all_count / $count_on_page);
		
		if ($page_count<2) return;
		$cur_page = (empty($param)) ? 1 : $param;
			$page['page_end'] = $page_count;
		$page['prev'] = ($cur_page>1) ? ($cur_page-1) : 1;
		$page['next'] = ($cur_page<$page_count) ? ($cur_page+1) : $page_count;
		
		$start_page = $cur_page-$this->step;
		if ($start_page <= 1) $start_page = 1;
		else {
			$page['start_mnog'] = 1;
		}
		
		$end_page = $cur_page+$this->step;
		if ($end_page >= $page_count) $end_page = $page_count;
		else {
			$page['end_mnog'] = 1;
			$page['page_end'] = $page_count;
		}
		
		//============================================
		
		for ($i = $start_page; $i<=$end_page; $i++) {
			$data = array( 'num' => $i );
			if ($cur_page == $i) 
				$data['paging_class'] = 'current';
			$page['pages'][] = $data;
		}
		
		$page['url'] = $url;
		$page['param'] = $param;
		$page['label'] = $label;
			return $this->html->render('pagination/index.html',$page);
	}
	
	
	public function index_ajax($all_count, $count_on_page = 5, $cur_page=1, $func='', $params='', $add_href = '' ) {
		// var_dump($all_count);
		// var_dump($count_on_page);
		// var_dump($cur_page);
		// var_dump($func);
		// var_dump($params);
		// var_dump($add_href);

		$page_count = ceil($all_count / $count_on_page);
		$page['page_count'] = $page_count;
		if ($page_count<2) return;
		
		$cur_page = ($cur_page>=1) ? $cur_page : 1;
		
		$page['cur_page'] = $cur_page;
		$page['prev'] = ($cur_page>1) ? ($cur_page-1) : 1;
		$page['next'] = ($cur_page<$page_count) ? ($cur_page+1) : $page_count;
		
		$start_page = $cur_page-$this->step;
		if ($start_page < 4) $start_page = 1;
		else {
			// var_dump($start_page);
			$page['start_mnog'] = 1;
		}
		
		$end_page = $cur_page+$this->step;
		if ($end_page >= $page_count-2) $end_page = $page_count;
		else {
			$page['end_mnog'] = 1;
			$page['page_end'] = $page_count;
		}
		
		//============================================
		
		for ($i = $start_page; $i<=$end_page; $i++) {
			$data = array();
			if ($cur_page == $i) {
				$data['paging_class'] = 'sel';
				$data['num'] = '<div class="pagi_select"><span class="'.$data['paging_class'].'">'.$i.'</span></div>';
			} else {
				$data['num'] = '<div class="pagi_num"><a href="#"  onclick="return '.$func.'('.$i.''.$params.');">'.$i.'</a></div>';
			}
			$page['pages'][] = $data;
		}
		
		$page['add_href'] = $add_href;
		$page['func'] = $func;
		$page['params'] = $params;
		return $this->html->render('pagination/index_ajax.html',$page);
	}
}
?>
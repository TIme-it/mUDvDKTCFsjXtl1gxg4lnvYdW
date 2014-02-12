<?php
class map_controller extends application_controller {
	private $html_map ="";
	private $sitemap  ="";

	public function index() {

		// $map = $this->menu->getMenu(0, 3);
		$map_content['title_page'] = 'Карта сайта';
		$this->html_map = '';
		$this->map_generation();
		

		$map_content['text'] = $this->html_map;
		$this->layout='pages';
		$this->sitemap="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$this->sitemap.="<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
		$this->map_generation_xml();
		$this->sitemap.="</urlset>";

		$file="sitemap.xml";
		
		$fp = fopen($file, "w"); 
		
		fwrite($fp,$this->sitemap);
		
		fclose($fp);
		$this->html->render('pages/pages.html', $map_content, 'container');
	}

	//Desc: Выводим карту сайта рекурсивно вызываю этот метод
	// Return: Шаблон карты сайта
	private function recursive_map(&$arr) {
		if(empty($arr['list'])) return '';
		$html = '';
		foreach($arr['list'] as $i => &$item) {
			$url   = (!empty($item['link'])) ? $item['link'] : $item['url'];
			$html .= '<li><a href="'.$url.'">'.$item['title'].'</a>';
			if(!empty($item['childs']['list'])) {
				$html .= '<ul>'.$this->recursive_map($item['childs']).'</ul>';
			}
			$html .= '</li>';
		}
		return $html;
	}

	public function map_generation($pid = 0,$obj = false){

		$data['list'] = $this->map->get_attributes_from_main($pid);

		if(!empty($data['list'])){
			$this->html_map .= '<ul>';
			foreach ($data['list'] as $i => &$item) {
				if($item['mod_name']!='link'){
				
					
					
					$item['url'] = $this->application_controller->get_url($item['id']);
					// var_dump($item['id']);
					$this->html_map .= '<li><a href = "'.$item['url'].'">'.$item['title'].'</a>';
					$this->map_generation($item['id'],$item);
				}
				else{
					if($item['link']!='/catalog/'){
						$this->html_map .= '<li><a href = "'.$item['link'].'">'.$item['title'].'</a>';
						$this->map_generation($item['id'],$item);
					}
					else{
						/*каталог костыль блеать!*/
						$this->html_map .= '<li><a href = "'.$item['link'].'">'.$item['title'].'</a>';
						$this->map_generation($item['id'],$item);
						$data['types'] = $this->catalog->getTypes();
						if(!empty($data['types'])){
							$this->html_map .= '<ul>';
							foreach ($data['types'] as $j => &$type) {
								$type['url'] = $this->catalog_controller->get_url($type['id']);
								if($this->config->get('active','chpu') == 1){
									// $type['url'] = '/catalog/'.$type['url'];
								}
								$this->html_map .= '<li><a href = "'.$type['url'].'">'.$type['title'].'</a>';
								$data['modifications'] = $this->catalog->getModifications($type['id']);
								if(!empty($data['modifications'])){
										$this->html_map .= '<ul>';
									foreach ($data['modifications'] as $k => &$mod) {
										$mod['url'] = $this->catalog_controller->get_url($mod['type_id'],$mod['id']);
										if($this->config->get('active','chpu') == 1){
											// $mod['url'] = '/catalog/'.$mod['url'];
										}
										$this->html_map .= '<li><a href = "'.$mod['url'].'">'.$mod['title'].'</a>';
										$data['subfamily'] = $this->catalog->getSubfamily($mod['id']);
									;
										if(!empty($data['subfamily'])){
											$this->html_map .= '<ul>';
											foreach ($data['subfamily'] as $z => &$sumfamily) {
											$sumfamily['url'] = $this->catalog_controller->get_url($mod['type_id'],$mod['id'],$sumfamily['id']);
											if($this->config->get('active','chpu') == 1){
												// $sumfamily['url'] = '/catalog/'.$sumfamily['url'];
											}
											$this->html_map .= '<li><a href = "'.$sumfamily['url'].'">'.$sumfamily['title'].'</a></li>';
											
											}
											$this->html_map .= '</ul>';
										}
										$this->html_map .= '</li>';
									}
									$this->html_map .= '</ul>';
								}
								$this->html_map .= '</li>';
							
							}
							$this->html_map .= '</ul>';
						}
					}
				
				}
				
				
				
			}
			$this->html_map .= '</ul>';
			$this->html_map .= '</li>';
		}
		else{
			$this->getChilds($obj);
			$this->html_map .= '</li>';
		}
	

		
		
	}
	public function getChilds($item){
		$data['childs'] = $this->map->getAll($item['mod_name'],$item['id']);
		if(!empty($data['childs'])){
			$this->html_map .= '<ul>';
			$controller = $this->lib_add($item['mod_name'].'_controller');
			foreach ($data['childs'] as $i => &$child) {
				 $child['url'] = call_user_func_array(array($controller,'get_url'),array($child['id']));
				 $this->html_map .= '<li><a href = "'.$child['url'].'">'.$child['title'].'</a>';
			}
			$this->html_map .= '</ul>';
		}
	}
	public function map_generation_xml($pid = 0,$obj = false){

		$data['list'] = $this->map->get_attributes_from_main($pid);

		if(!empty($data['list'])){
			$this->html_map .= '<ul>';
			foreach ($data['list'] as $i => &$item) {
				if($item['mod_name']!='link'){
				
					
					
					$item['url'] = $this->application_controller->get_url($item['id']);
					// var_dump($item['id']);
					$this->sitemap.="<url>";
					$this->sitemap.="<loc>"."http://avtoholding-nn.ru".$item['url']."</loc>";
					$this->sitemap.="<lastmod>".gmdate('Y-d-m', strtotime(!empty($item['date']) ? $item['date'] : 1376291439 ))."</lastmod>";
					$this->sitemap.="</url>";
					// $this->html_map .= '<li><a href = "'.$item['url'].'">'.$item['title'].'</a>';
					$this->map_generation_xml($item['id'],$item);
				}
				else{
					if($item['link']!='/catalog/'){
						// $this->html_map .= '<li><a href = "'.$item['link'].'">'.$item['title'].'</a>';
						$this->sitemap.="<url>";
						$this->sitemap.="<loc>"."http://avtoholding-nn.ru ".$item['link']."</loc>";
						$this->sitemap.="<lastmod>".gmdate('Y-d-m', strtotime(!empty($item['date']) ? $item['date'] : 1376291439 ))."</lastmod>";
						$this->sitemap.="</url>";
						$this->map_generation_xml($item['id'],$item);
					}
					else{
						/*каталог костыль блеать!*/
						$this->html_map .= '<li><a href = "'.$item['link'].'">'.$item['title'].'</a>';
						$this->map_generation_xml($item['id'],$item);
						$data['types'] = $this->catalog->getTypes();
						if(!empty($data['types'])){
							$this->html_map .= '<ul>';
							foreach ($data['types'] as $j => &$type) {
								$type['url'] = $this->catalog_controller->get_url($type['id']);
								if($this->config->get('active','chpu') == 1){
									// $type['url'] = '/catalog/'.$type['url'];
								}
								$this->sitemap.="<url>";
								$this->sitemap.="<loc>"."http://avtoholding-nn.ru ".$type['url']."</loc>";
								if(!empty($type['date'])){
												$this->sitemap.="<lastmod>".gmdate('Y-d-m', strtotime($type['date']))."</lastmod>";
											}
											else{
											
												$this->sitemap.="<lastmod>".gmdate('Y-d-m', 1376291439)."</lastmod>";
											}
								$this->sitemap.="</url>";
								// $this->html_map .= '<li><a href = "'.$type['url'].'">'.$type['title'].'</a>';
								$data['modifications'] = $this->catalog->getModifications($type['id']);
								if(!empty($data['modifications'])){
										$this->html_map .= '<ul>';
									foreach ($data['modifications'] as $k => &$mod) {
										$mod['url'] = $this->catalog_controller->get_url($mod['type_id'],$mod['id']);
										if($this->config->get('active','chpu') == 1){
											// $mod['url'] = '/catalog/'.$mod['url'];
										}
										$this->sitemap.="<url>";
										$this->sitemap.="<loc>"."http://avtoholding-nn.ru ".$mod['url']."</loc>";
										if(!empty($mod['date'])){
												$this->sitemap.="<lastmod>".gmdate('Y-d-m', strtotime($mod['date']))."</lastmod>";
											}
											else{
											
												$this->sitemap.="<lastmod>".gmdate('Y-d-m', 1376291439)."</lastmod>";
											}
										$this->sitemap.="</url>";
										// $this->html_map .= '<li><a href = "'.$mod['url'].'">'.$mod['title'].'</a>';
										$data['subfamily'] = $this->catalog->getSubfamily($mod['id']);
									;
										if(!empty($data['subfamily'])){
											$this->html_map .= '<ul>';
											foreach ($data['subfamily'] as $z => &$sumfamily) {
											$sumfamily['url'] = $this->catalog_controller->get_url($mod['type_id'],$mod['id'],$sumfamily['id']);
											if($this->config->get('active','chpu') == 1){
												// $sumfamily['url'] = '/catalog/'.$sumfamily['url'];
											}
											$this->sitemap.="<url>";
											$this->sitemap.="<loc>"."http://lumiertm.ru".$sumfamily['url']."</loc>";
											if(!empty($sumfamily['date'])){
												$this->sitemap.="<lastmod>".gmdate('Y-d-m', strtotime($sumfamily['date']))."</lastmod>";
											}
											else{
											
												$this->sitemap.="<lastmod>".gmdate('Y-d-m', 1376291439)."</lastmod>";
											}
											
											$this->sitemap.="</url>";
											// $this->html_map .= '<li><a href = "'.$sumfamily['url'].'">'.$sumfamily['title'].'</a></li>';
											
											}
											// $this->html_map .= '</ul>';
										}
										// $this->html_map .= '</li>';
									}
									// $this->html_map .= '</ul>';
								}
								// $this->html_map .= '</li>';
							
							}
							// $this->html_map .= '</ul>';
						}
					}
				
				}
				
				
				
			}
			// $this->html_map .= '</ul>';
			// $this->html_map .= '</li>';
		}
		else{
			$this->getChilds_xml($obj);
			// $this->html_map .= '</li>';
		}
	

		
		
	}
	public function getChilds_xml($item){
		$data['childs'] = $this->map->getAll($item['mod_name'],$item['id']);
		if(!empty($data['childs'])){
			// $this->html_map .= '<ul>';
			$controller = $this->lib_add($item['mod_name'].'_controller');
			foreach ($data['childs'] as $i => &$child) {
				 $child['url'] = call_user_func_array(array($controller,'get_url'),array($child['id']));
				 $this->sitemap.="<url>";
				 $this->sitemap.="<loc>"."http://lumiertm.ru".$child['url']."</loc>";
				 $this->sitemap.="<lastmod>".gmdate('Y-d-m', strtotime(!empty($child['date']) ? $child['date'] : 1376291439))."</lastmod>";
				 $this->sitemap.="</url>";
				 // $this->html_map .= '<li><a href = "'.$child['url'].'">'.$child['title'].'</a>';
			}
			// $this->html_map .= '</ul>';
		}
	}
	public function xml_map_generation(){

	}
	
}
?>
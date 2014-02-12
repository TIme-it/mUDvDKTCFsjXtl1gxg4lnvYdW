<?php
	//Шаблонизатор. Список внутри списка не должен иметь такое же имя!!!
	//Возможные параметры:
	//	{item}		- 		подставляет одиночное значение поля item. Если на текущем уровне вложенности (внутри списка)
	//						такого элемента нет, то ищем на уровне выше и так до корня массива.
	//	{list}...{/list}	Список. подставляет внутреннее содержимое многократно. внутренние переменные заменяются
	//						с приоритетом от данного уровня до корневого.
	//	{?sub}...{/?sub}	Условный блок. Обрабатывается только если на текущем(!) уровне массива существует значение
	//						с ключом sub. Иначе, блок пропускается.
	//	{!sub}...{/!sub}	Условный блок. Противоположен блоку {?sub}...{/?sub}. Блок подставляется, если ключа на
	//						текущем(!) уровне массива нет.
	//	{#}					Итератор. Выводит номер итерации текущего списка.
	//	{##}				Итератор. Выводит номер итерации родительского списка.
	//	{#...#}				Итератор. Выводит номер итерации списка-родителя N-го уровня
	//	{?%N} ... {/?%N}	Условный итеративный блок. Срабатывает когда итератор кратен N.
	//	{!%N} ... {/!%N}	Условный итеративный блок. Срабатывает когда итератор не кратен N.
	//						ИТЕРАТОРЫ ДЛЯ ДЕЛЕНИЯ НА КОЛОНОКИ:
	//	{?+N} ... {/?+N}	Условный итеративный блок. Срабатывает когда (итератор > N) и (итератор кратен N).
	//	{!+N} ... {/!+N}	Условный итеративный блок. Срабатывает когда (итератор > N) и (итератор не кратен N).

class html {

	protected $tpl;				//Текст шаблона
	public $tpl_vars;	//Переменные шаблона

	protected $position;		//Позиция в тексте шаблоне
	protected $result;			//Результат работы шаблона
	protected $depthpath;		//Список имен списков
	protected $index;			//Список индексов списков
	
	
	public function __construct() {
		$this->tpl_path = (defined('APPLICATION_ADMIN'))? VIEWS_ADMIN : VIEWS;
		$this->tpl_vars = array();
	}
	
	public function render($template, $vars = array(), $out_var = '', $obfuscate = false) {
		$template = str_replace('/', DS, $template);
		
		if(file_exists($this->tpl_path.$template)) 
			$this->tpl = file_get_contents($this->tpl_path.$template);
		else {
			throw new Exception('<p>Шаблона нет: <strong>'.$template.'</strong></p>');
		}
		
		if (is_array($vars))
			$vars = array_merge($this->tpl_vars, $vars);	//Объединяем переданные переменные и исходные переменные

		$this->result = '';
		$this->index = array();
		$this->depthpath = array();
		$this->position = 0;
		$this->replace_var($this->tpl, $vars);

		if($obfuscate) $this->result = preg_replace('/\s*\r+/', '', $this->result);

		if(empty($out_var)) return $this->result;
		$this->tpl_vars[$out_var] = $this->result;
		return true;
	}
	
	private function obfuscate(&$data) {
		$data = preg_replace('/>\s+</', '><', $data);
	}
	
	//Обработка шаблона (основная часть)
	private function replace_var($template, $vars ) {
		do {
			$pos_var_begin = strpos($template,'{',$this->position);
			$pos_var_end = strpos($template,'}',$pos_var_begin);

			//Если в шаблоне нет переменных
			if ( ($pos_var_begin === false) || ($pos_var_end === false) ) {
				//Присоединяем остаток текста
				$this->result .= substr($template, $this->position);
				break;
			}

			//Имя переменной не пустое
			$var_name = substr($template, $pos_var_begin+1, $pos_var_end-$pos_var_begin-1);
			if (empty($var_name)) {
				$this->position = $pos_var_end+1;
				continue;
			}
			
			//Переменные, которые содержат недопустимые символы, вставляем как есть (для JS)
			if ( !preg_match('/^[a-z0-9?%+#!_\/]*$/i', $var_name) ) {
				$this->result .= substr($template, $this->position, $pos_var_begin-$this->position+1);
				$this->position = $pos_var_begin+1;
				continue;
			}

			//Присоединяем текст от текущей позиции до первого шаблона
			$this->result .= substr($template, $this->position, $pos_var_begin-$this->position);

			$this->position = $pos_var_end+1;
			$array_end = strpos($template,'{/'.$var_name.'}',$pos_var_begin);
			$cur_vars = $vars;
			
			if ( !$array_end ) {	//Указаная переменная не список
				//Создаем массив пути шаблонизатора
				$array_path = array();
				if (!empty($this->depthpath)) {			
					foreach ($this->depthpath as $i => $value) {
						array_push($array_path, $cur_vars);
						$cur_vars = $cur_vars[$value][$this->index[$i]];
					}
				}

				//Указанная переменная - итератор
				if ($var_name[0] == '#') {
					$index = count($this->index)-strlen($var_name);
					if ($index < 0) continue;
					$this->result .= $this->index[$index]+1;
					continue;
				}
				
				//Ищем переменную в массиве от ближайшего массива до корня
				while ( !isset($cur_vars[$var_name]) && !empty($array_path) ) {
					$cur_vars = array_pop($array_path);
				}

				//Если переменная найдена и она не массив, то заменяем её
				if ( isset($cur_vars[$var_name]) && !is_array($cur_vars[$var_name]) ) {
					$this->result .= $cur_vars[$var_name];
				} else {
					
				}
				continue;
			} else {				//Указанная переменная объявлена как список
				//Переходим к активному массиву
				if (!empty($this->depthpath)) {
					foreach ($this->depthpath as $i => $value) {
						$cur_vars = $cur_vars[$value][$this->index[$i]];
					}
				}

				//В активном массиве есть переменная и она - список?
				if ( isset($cur_vars[$var_name]) && is_array($cur_vars[$var_name]) ) {	
					//Делаем дочерний массив списка активным
					array_push($this->depthpath, $var_name);

					//Вырезаем часть шаблона для списка
					$newtemplate = substr($template, $this->position, $array_end-$this->position);

					//Заменяем список
					foreach ($cur_vars[$var_name] as $i => $value) {
						$this->position = 0;
						array_push($this->index, $i);		//Запоминаем индекс в списке
						$this->replace_var($newtemplate, $vars);
						array_pop($this->index);			//Удаляем индекс из списка
					}

					//Поднимаемся на уровень выше по массиву
					array_pop($this->depthpath);

					//Переносим текущую позицию в файле на конец списка
					$this->position = $array_end+strlen($var_name)+3;
					continue;
				} else {
					
					if ($var_name[1] == '%') {
						$index = end($this->index)+1;
						$dec = (int)substr($var_name, 2);
						if ( ($var_name[0] == '?') && ($index % $dec == 0) ||
							 ($var_name[0] == '!') && ($index % $dec != 0)) {
							//Вырезаем часть шаблона
							$newtemplate = substr($template, $this->position, $array_end-$this->position);

							$this->position = 0;
							$this->replace_var($newtemplate, $vars);
						}
						
						//Переносим текущую позицию в файле на конец списка
						$this->position = $array_end+strlen($var_name)+3;
						continue;
					}
					if ($var_name[1] == '+') {
						//Условный итеративный блок
						$index = end($this->index);
						$dec = (int)substr($var_name, 2);
						if ( ($var_name[0] == '?') && ($index >= $dec) && ($index % $dec == 0) ||
							 ($var_name[0] == '!') && ($index >= $dec) && ($index % $dec != 0)) {
							//Вырезаем часть шаблона
							$newtemplate = substr($template, $this->position, $array_end-$this->position);

							$this->position = 0;
							$this->replace_var($newtemplate, $vars);
						}
						
						//Переносим текущую позицию в файле на конец списка
						$this->position = $array_end+strlen($var_name)+3;
						continue;
					}
					
					//Если задан простой условный блок
					$variable = substr($var_name,1);
					if ( ( ($var_name[0] == '?') && (!empty($cur_vars[$variable])) ) ||
						 ( ($var_name[0] == '!') && empty($cur_vars[$variable]) )
						)
					{
						//Вырезаем часть шаблона для условного списка
						$newtemplate = substr($template, $this->position, $array_end-$this->position);

						$this->position = 0;
						$this->replace_var($newtemplate, $vars);
					}
					
					
					//Переносим текущую позицию в файле на конец списка
					$this->position = $array_end+strlen($var_name)+3;
					continue;
				}
			}
		} while (1);
	}
}
?>
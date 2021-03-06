<?php
	/*
		каждый элемент массива должен состоять из обязательных полей: title и slides
		имеются необязательные параметры:
			slides - слайды, которые выстраиваются в слайд-шоу: поля img и text
			childs - вложенные элементы(имеют такую же структуру)
	*/
	$helper = array(
		0 => array(
			'title'		=>	'Редактирование текста',
			'childs'	=>	array(
				0 => array(
					'title'		=>	'Как правильно вставить текст',
					'prefix'	=>	'01_01',
					'slides'	=>	array(
						array('text' => 'Копируем текст из Word, затем нажимаем кнопку "Вставить изWord"'),
						array('text' => 'Устанавливаем курсор в появившемся окне и нажимаем Ctrl+V'),
						array('text' => 'Текст вставлен'),
					),
				),
				1 => array(
					'title'		=>	'Абзац и перенос строки',
					'prefix'	=>	'01_02',
					'slides'	=>	array(
						array('text' => 'Открываем страницу и устанавливаем курсор в нужное место'),
						array('text' => 'Перенос текста при нажатии клавиши Enter'),
						array('text' => 'Перенос текста при нажатии клавиш Shift+Enter'),
					),
				),
				2 => array(
					'title'		=>	'Заголовки',
					'prefix'	=>	'01_03',
					'slides'	=>	array(
						array('text' => 'Без заголовка'),
						array('text' => 'Выделяем заголовок'),
						array('text' => 'Выбираем формат заголовка'),
						array('text' => 'Заголовок 1 уровня'),
					),
				),
				3 => array(
					'title'		=>	'Выравнивание текста',
					'prefix'	=>	'01_04',
					'slides'	=>	array(
						array('text' => 'Выделяем текст'),
						array('text' => 'Выбираем выравнивание по центру'),
						array('text' => 'Выравнивание по центру'),
					),
				),
				4 => array(
					'title'		=>	'Маркиророванные / нумерованные списки',
					'prefix'	=>	'01_05',
					'slides'	=>	array(
						array('text' => 'Выделяем текст'),
						array('text' => 'Выбираем "Маркированный список"'),
						array('text' => 'Маркированный список'),
						array('text' => 'Выбираем "Нумерованный список"'),
						array('text' => 'Нумерованный список'),
					),
				),
				5 => array(
					'title'		=>	'Ссылки текстовые',
					'prefix'	=>	'01_06',
					'slides'	=>	array(
						array('text' => 'Выделяем нужный текст'),
						array('text' => 'Выбираем "Вставить/редактировать ссылку"'),
						array('text' => 'В появившемся окне вводим адрес ссылки'),
						array('text' => 'Текстовая ссылка'),
						array('text' => 'Чтобы удалить ссылку, выделяем текстовую ссылку'),
						array('text' => 'Выбираем "Удалить ссылку"'),
						array('text' => 'Ссылка удалена'),
					),
				),
				6 => array(
					'title'		=>	'Ссылки графические',
					'prefix'	=>	'01_07',
					'slides'	=>	array(
						array('text' => 'Выделяем рисунок'),
						array('text' => 'Выбираем "Вставить/редактировать ссылку"'),
						array('text' => 'В появившемся окне вводим адрес ссылки и нажимаем "Вставить"'),
						array('text' => 'Графическая ссылка готова'),
					),
				),
			)
		),
		1 => array(
			'title'		=>	'Редактирование изображений',
			'childs'	=>	array(
				0 => array(
					'title'		=>	'Как вставить изображение',
					'prefix'	=>	'02_01',
					'slides'	=>	array(
						array('text' => 'Выбираем вкладку "Фото на страницу"'),
						array('text' => 'Нажимаем кнопку "Добавить файлы"'),
						array('text' => 'Выбираем нужное изображение и нажимаем "Открыть"'),
						array('text' => 'Нажимаем кнопку "Загрузить"'),
						array('text' => 'Изображение загружено'),
						array('text' => 'Устанавливаем курсор в нужную часть страницы'),
						array('text' => 'Выбираем "Вставить/редактировать изображение"'),
						array('text' => 'В открывшемся окне выбираем "Список изображений"'),
						array('text' => 'Выбираем нужное нам изображение и нажимаем кнопку "Вставить"'),
						array('text' => 'Изображение добавлено'),
					),
				),
				1 => array(
					'title'		=>	'Выравнивание изображений в тексте',
					'prefix'	=>	'02_02',
					'slides'	=>	array(
						array('text' => 'Выделяем картинку'),
						array('text' => 'Выравнивание по центру'),
						array('text' => 'Выравнивание вправо'),
						array('text' => 'Редактирование изображения'),
						array('text' => 'Вкладка "Внешний вид"'),
						array('text' => 'Выбираем выравнивание "Влево", затем нажимаем "Обновить"'),
						array('text' => 'К рисунку применено выравнивание влево'),
					),
				),
				2 => array(
					'title'		=>	'Фотогалерея',
					'prefix'	=>	'02_03',
					'slides'	=>	array(
						array('text' => 'Переходим к разделу "Дополнительные модули"'),
						array('text' => 'Выбираем вкладку "Фотогалерея"'),
						array('text' => 'Нажимаем кнопку "Добавить файлы"'),
						array('text' => 'Выбираем картинку и нажимаем "Открыть"'),
						array('text' => 'Картинка выбрана - нажимаем "Загрузить"'),
						array('text' => 'Картинка загружена'),
					),
				),
				3 => array(
					'title'		=>	'Как удалить изображение',
					'prefix'	=>	'02_04',
					'slides'	=>	array(
						array('text' => 'Выбрать изображение в тексте и нажать кнопку "Delete"'),
						array('text' => 'Картинка в тексте удалена'),
						array('text' => 'Выбираем вкладку "Фото на страницу"'),
						array('text' => 'Нажимаем кнопку "Удалить изображение"'),
						array('text' => 'Нажимаем кнопку "ОК"'),
						array('text' => 'Картинка удалена из раздела "Фото на страницу"'),
						array('text' => 'Выбираем вкладку "Фотогалерея"'),
						array('text' => 'Нажимаем кнопку "Удалить"'),
						array('text' => 'Нажимаем кнопку "ОК"'),
						array('text' => 'Картинка удалена из раздела "Фотогалерея"'),
					),
				),
			),
		),
		2 => array(
			'title'		=>	'Создание и редактирование таблиц',
			'childs'	=>	array(
				0 => array(
					'title'		=>	'Как вставить таблицу',
					'prefix'	=>	'03_01',
					'slides'	=>	array(
						array('text' => ''),
						array('text' => ''),
						array('text' => ''),
						array('text' => ''),
						array('text' => ''),
						array('text' => ''),
					),
				),
				1 => array(
					'title'		=>	'Текст и изображения в таблице',
					'prefix'	=>	'03_02',
					'slides'	=>	array(
						array('text' => 'Выбираем "Вставить новую таблицу"'),
						array('text' => 'Вводим необходимое число строк и столбцов'),
						array('text' => 'Таблица вставлена'),
						array('text' => 'Вводим текст в таблицу'),
						array('text' => 'Кликаем по таблице правой кнопкой мыши, выбираем "Свойства таблицы"'),
						array('text' => 'Выбираем Класс таблицы "Table"'),
						array('text' => ''),
						array('text' => ''),
						array('text' => ''),
						array('text' => ''),
					),
				),
				2 => array(
					'title'		=>	'Форматирование ячеек',
					'prefix'	=>	'03_03',
					'slides'	=>	array(
						array('text' => 'Выбираем нужную ячейку'),
						array('text' => 'Выбираем "Вставить/редактировать изображение"'),
						array('text' => 'Выбираем картинку'),
						array('text' => 'Нажимаем "Вставить"'),
						array('text' => 'Картинка вставлена'),
						array('text' => 'Выделяем картинку'),
						array('text' => 'Выбираем "Редактировать изображение"'),
						array('text' => 'Вкладка внешний вид'),
						array('text' => 'Меняем размер картинки'),
						array('text' => 'Размер картинки изменен'),
					),
				),
				3 => array(
					'title'		=>	'Как удалить таблицу',
					'prefix'	=>	'03_04',
					'slides'	=>	array(
						array('text' => 'Кликаем по таблице провай кнопкой мыши'),
						array('text' => 'Выбираем "Удалить таблицу"'),
						array('text' => 'Таблица удалена'),
					),
				),
			),
		),
		3 => array(
			'title'		=>	'Структура сайта',
			'childs'	=>	array(
				0 => array(
					'title'		=>	'Как создать новый раздел',
					'prefix'	=>	'04_01',
					'slides'	=>	array(
						array('text' => 'Нажимаем кнопку "Добавить страницу"'),
						array('text' => 'Новая страница'),
						array('text' => 'Вводим название страницы'),
						array('text' => 'Выбираем модуль - "Стандартна страница"'),
						array('text' => 'Выбираем Директорию - "Корень"'),
						array('text' => 'Нажимаем кнопку "Добавить"'),
						array('text' => 'Новый раздел добавлен'),
					),
				),
				1 => array(
					'title'		=>	'Как создать новую страницу',
					'prefix'	=>	'04_02',
					'slides'	=>	array(
						array('text' => 'Нажимаем "Добавить страницу'),
						array('text' => 'Вводим название страницы'),
						array('text' => 'Выбираем нужный нам модуль'),
						array('text' => 'Выбираем нужную нам директорию'),
						array('text' => 'Нажимаем кнопку "Добавить"'),
						array('text' => 'Новая страница добавлена в соответствующий раздел'),
					),
				),
				2 => array(
					'title'		=>	'Редактирование меню сайта',
					'prefix'	=>	'04_03',
					'slides'	=>	array(
						array('text' => 'Выбираем в мнею раздел "Структура"'),
						array('text' => 'Выбираем директорию сортировки'),
						array('text' => 'Выбираем нужный раздел и тянем мышкой на нужное место'),
						array('text' => 'Перемещаем раздел'),
						array('text' => 'Нажимаем "Сохранить"'),
						array('text' => 'Даные обновлены'),
						array('text' => 'Раздел перемещен'),
					),
				),
				3 => array(
					'title'		=>	'Удаление страницы',
					'prefix'	=>	'04_04',
					'slides'	=>	array(
						array('text' => 'Выбираем необходимую страницу'),
						array('text' => 'Прокручиваем страницу вниз и нажимаем на "Удалить страницу"'),
						array('text' => 'Нажимаем "ОК"'),
						array('text' => 'Страница перемещена в корзину, можно ее окончательно удалить или восстановить'),
					),
				),
			),
		),
		4 => array(
			'title'		=>	'Новости / статьи',
			'childs'	=>	array(
				0 => array(
					'title'		=>	'Как добавить новость / статью',
					'prefix'	=>	'05_01',
					'slides'	=>	array(
						array('text' => 'Выбираем раздел "Публикации/Новости и акции"'),
						array('text' => 'Прокручиваем страницу вниз и нажимаем кнопку "Добавить публикацию"'),
						array('text' => 'Страница добавления новой публикации'),
						array('text' => 'Вводим название страницы (заголовок публикации) и краткое описание'),
						array('text' => 'Нжимаем Обзор, выбираем характеризующую картинку и нажимаем кнопку открыть'),
						array('text' => 'Картинка добавлена'),
						array('text' => 'Нажимаем кнопку "Вставить из Word"'),
						array('text' => 'Вставляем текст из Word и нажимаем кнопку "Вставить"'),
						array('text' => 'Текст страницы вставлен'),
						array('text' => 'Нажимаем кнопку "Сохранить"'),
						array('text' => 'Новая публикация добавлена'),
					),
				),
				1 => array(
					'title'		=>	'Как удалить новость / статью',
					'prefix'	=>	'05_02',
					'slides'	=>	array(
						array('text' => 'Переходим в раздел Новости и акции (публикации)"'),
						array('text' => 'Прокручиваем страницу вниз до раздела "Список публикаций"'),
						array('text' => 'Отмечаем галочкой публикации, которые хотим удалить. Нажимаем кнопку "Удалить"'),
						array('text' => 'Нажимаем "ОК"'),
						array('text' => 'Публикация удалена'),
					),
				),
			),
		),
		5 => array(
			'title'		=>	'Файлы на странице',
			'prefix'	=>	'06_00',
			'slides'	=>	array(
				array('text' => 'Выбираем нужную страницу'),
				array('text' => 'Прокручиваем страницу вниз до раздела "Дополнительные модули"'),
				array('text' => 'Открываем вкладку "Файлы на страницу"'),
				array('text' => 'Нажимаем кнопку "Добавить файлы"'),
				array('text' => 'Выбираем нужный файл и нажимаем кнопку "Открыть"'),
				array('text' => 'Добавляем все нужные файлы и нажимаем кнопку "Загрузить"'),
				array('text' => 'Файлы загружены'),
			),
		),
		6 => array(
			'title'		=>	'Редактирование главной страницы',
			'prefix'	=>	'07_00',
			'slides'	=>	array(
				array('text' => 'Дополнительные модули - Главная страница'),
				array('text' => 'Выделяем участок текста который нужно удалить или заменить'),
				array('text' => 'Нажимаем кнопку "Вставить из Word"'),
				array('text' => 'Вставляем текст и нажимаем кнопку "Вставить"'),
				array('text' => 'Текст заменен'),
				array('text' => 'Выделяем текст и удаляем'),
				array('text' => 'Вводим новый текст'),
				array('text' => 'Нажимаем кнопку "Сохранить"'),
				array('text' => 'Данные изменены'),
			),
		),
	);
?>
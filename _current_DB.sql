-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 28 2014 г., 14:50
-- Версия сервера: 5.6.11
-- Версия PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `midpo`
--
CREATE DATABASE IF NOT EXISTS `midpo` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `midpo`;

-- --------------------------------------------------------

--
-- Структура таблицы `actions`
--

CREATE TABLE IF NOT EXISTS `actions` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alias` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `source` varchar(255) DEFAULT NULL,
  `fotogallery` varchar(100) DEFAULT 'simple',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `proceed` tinyint(1) NOT NULL DEFAULT '0',
  `is_show_date` tinyint(4) NOT NULL DEFAULT '1',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `print` tinyint(1) NOT NULL DEFAULT '1',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `actions`
--

INSERT INTO `actions` (`id`, `pid`, `title`, `note`, `date`, `alias`, `keywords`, `description`, `author`, `source`, `fotogallery`, `gallery_header`, `active`, `proceed`, `is_show_date`, `sendfile`, `print`, `feedback`) VALUES
(1, 42, 'Теория и практика дошкольного образования', '<p>Только в феврале и марте желающим пройти курсы профессиональной переподготовки по программе &laquo;Теория и практика дошкольного образования&raquo; стоимость обучения 18000 рублей (оплата ежемесячно).</p>', '2014-03-27 13:05:51', '', '', NULL, '', '', 'simple', 1, 1, 0, 0, 0, 0, 0),
(2, 42, 'Подпишись на курс "Строительство"', '<p>Только в феврале и марте желающим пройти курсы профессиональной переподготовки по программе &laquo;Теория и практика дошкольного образования&raquo; стоимость обучения 18000 рублей (оплата ежемесячно).</p>', '2014-03-27 13:22:59', '', '', NULL, '', '', 'simple', 1, 1, 0, 0, 0, 0, 0),
(3, 42, 'Каждый третий курс БЕСПЛАТНО!', '<p>Только в феврале и марте желающим пройти курсы профессиональной переподготовки по программе &laquo;Теория и практика дошкольного образования&raquo; стоимость обучения 18000 рублей (оплата ежемесячно).</p>', '2014-03-27 13:23:46', '', '', NULL, '', '', 'simple', 1, 1, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `alias` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `source` varchar(255) DEFAULT NULL,
  `fotogallery` varchar(100) DEFAULT 'simple',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `is_show_date` tinyint(4) NOT NULL DEFAULT '1',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `print` tinyint(1) NOT NULL DEFAULT '1',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `auth_action`
--

CREATE TABLE IF NOT EXISTS `auth_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'нигде не используется',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `auth_action`
--

INSERT INTO `auth_action` (`id`, `title`) VALUES
(1, 'Раздел администрирования'),
(2, 'Управление страницей'),
(3, 'Главная страница'),
(4, 'Добавление страницы'),
(7, 'Управление баннерами');

-- --------------------------------------------------------

--
-- Структура таблицы `auth_rights`
--

CREATE TABLE IF NOT EXISTS `auth_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `param1` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=421 ;

--
-- Дамп данных таблицы `auth_rights`
--

INSERT INTO `auth_rights` (`id`, `role_id`, `action_id`, `param1`) VALUES
(420, 1, 3, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_role`
--

CREATE TABLE IF NOT EXISTS `auth_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `auth_role`
--

INSERT INTO `auth_role` (`id`, `pid`, `title`) VALUES
(1, 0, 'test_role');

-- --------------------------------------------------------

--
-- Структура таблицы `auth_user`
--

CREATE TABLE IF NOT EXISTS `auth_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `login` varchar(255) CHARACTER SET utf8 NOT NULL,
  `pass` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `auth_user`
--

INSERT INTO `auth_user` (`id`, `pid`, `role_id`, `login`, `pass`, `email`) VALUES
(1, 0, 1, 'test', '75f78b2cc634b14cd69beab1a9b0ae89', 'admin@ya.ru');

-- --------------------------------------------------------

--
-- Структура таблицы `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `id` mediumint(2) NOT NULL AUTO_INCREMENT,
  `category_id` mediumint(2) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(4) NOT NULL DEFAULT 'swf',
  `date_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view_random` int(3) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `center_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Структура таблицы `banners_categories`
--

CREATE TABLE IF NOT EXISTS `banners_categories` (
  `id` mediumint(2) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `width` varchar(4) NOT NULL DEFAULT '',
  `height` varchar(4) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `catalog`
--

CREATE TABLE IF NOT EXISTS `catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL,
  `title` varchar(1500) NOT NULL DEFAULT '',
  `note` text,
  `hours` varchar(255) NOT NULL,
  `price_hour` float NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `fotogallery` tinyint(1) NOT NULL DEFAULT '1',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `print` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `taitle` varchar(255) DEFAULT '',
  `price_turn` float NOT NULL,
  `geo` tinyint(4) NOT NULL COMMENT '1 - автозаводский, 2 - центральный, 3 - комсомольский, 4 - по городу, 5 - по области',
  `fin_type` varchar(3) NOT NULL COMMENT '1 - нал, 2 - безнал',
  `type` tinyint(4) NOT NULL COMMENT '1 - юр, 2 - физ',
  `author` text NOT NULL,
  `phone` text NOT NULL,
  `date` int(11) NOT NULL,
  `top` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `catalog`
--

INSERT INTO `catalog` (`id`, `pid`, `cid`, `lid`, `title`, `note`, `hours`, `price_hour`, `description`, `keywords`, `fotogallery`, `gallery_header`, `print`, `feedback`, `sendfile`, `active`, `taitle`, `price_turn`, `geo`, `fin_type`, `type`, `author`, `phone`, `date`, `top`) VALUES
(4, 4, 2, 8, 'Компьютерные курсы', '<p>Компьютерные курсы</p>', '72 ч.', 0, '', '', 1, 1, 0, 0, 0, 0, 'Компьютерные курсы', 0, 0, '0', 0, '', '', 1394513599, 0),
(5, 8, 2, 19, 'Основы компьютерной грамотности', '<p>Основы компьютерной грамотности</p>', 'от 72 до 500 ч.', 0, '', '', 1, 1, 0, 0, 0, 0, 'Основы компьютерной грамотности', 0, 0, '0', 0, '', '', 1394513784, 0),
(6, 8, 2, 20, 'Безопасность строительства и качество возведения каменных, металлических и деревянных строительных конструкций', '<p>Безопасность строительства и качество возведения каменных, металлических и деревянных строительных конструкций</p>', 'от 72 до 500 ч.', 0, '', '', 1, 1, 0, 0, 0, 0, 'Безопасность строительства и качество возведения каменных, металлических и деревянных строительных конструкций', 0, 0, '0', 0, '', '', 1394513772, 0),
(7, 10, 2, 21, 'Обеспечение экологической безопасности руководителями и специалистами общехозяйственных систем управления', '<p>Обеспечение экологической безопасности руководителями и специалистами общехозяйственных систем управления</p>', '', 0, '', '', 1, 1, 0, 0, 0, 0, 'Обеспечение экологической безопасности руководителями и специалистами общехозяйственных систем управления', 0, 0, '0', 0, '', '', 1394522271, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_categories`
--

CREATE TABLE IF NOT EXISTS `catalog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `cid` int(11) NOT NULL DEFAULT '0',
  `lid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `first_name_column` varchar(50) NOT NULL DEFAULT 'Наименование',
  `taitle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Дамп данных таблицы `catalog_categories`
--

INSERT INTO `catalog_categories` (`id`, `pid`, `cid`, `lid`, `title`, `note`, `description`, `keywords`, `first_name_column`, `taitle`) VALUES
(1, 0, 2, 1, 'Образование', 'Образование', '', '', '', 'Образование'),
(4, 1, 2, 7, 'повышения квалификации', 'Повышение квалификации', '', '', 'Наименование', 'Повышение квалификации'),
(5, 0, 2, 9, 'Строительство', 'Строительство', '', '', 'Наименование', 'Строительство'),
(6, 0, 2, 10, 'Экология', 'Экология', '', '', 'Наименование', 'Экология'),
(7, 0, 2, 11, 'Физкультура и спорт', 'Физкультура и спорт', '', '', 'Наименование', 'Физкультура и спорт'),
(8, 1, 2, 12, 'профессиональной переподготовки', 'Профессиональная переподготовка', '', '', 'Наименование', 'Профессиональная переподготовк'),
(9, 5, 2, 13, 'профессиональной переподготовки', 'Профессиональная переподготовка', '', '', 'Наименование', 'Профессиональная переподготовк'),
(10, 5, 2, 14, 'повышения квалификации', 'Повышение квалификации', '', '', 'Наименование', 'Повышение квалификации'),
(11, 7, 2, 15, 'повышения квалификации', 'Повышение квалификации', '', '', 'Наименование', 'Повышение квалификации'),
(12, 7, 2, 16, 'профессиональной переподготовки', 'Профессиональная переподготовка', '', '', 'Наименование', 'Профессиональная переподготовк'),
(13, 6, 2, 17, 'повышения квалификации', 'Повышение квалификации', '', '', 'Наименование', 'Повышение квалификации'),
(14, 6, 2, 18, 'профессиональной переподготовки', 'Профессиональная переподготовка', '', '', 'Наименование', 'Профессиональная переподготовк');

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_categories_techchars_links`
--

CREATE TABLE IF NOT EXISTS `catalog_categories_techchars_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `techchar_id` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `catalog_categories_techchars_links`
--

INSERT INTO `catalog_categories_techchars_links` (`id`, `catalog_id`, `category_id`, `techchar_id`, `sort`) VALUES
(1, 0, 4, 0, 0),
(2, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_links`
--

CREATE TABLE IF NOT EXISTS `catalog_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL DEFAULT '0' COMMENT 'item_ID category',
  `prod_id` int(11) NOT NULL DEFAULT '0' COMMENT 'item_ID product',
  `pid` int(11) NOT NULL,
  `cid` int(11) NOT NULL COMMENT 'ID catalog',
  `alias` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Дамп данных таблицы `catalog_links`
--

INSERT INTO `catalog_links` (`id`, `cat_id`, `prod_id`, `pid`, `cid`, `alias`) VALUES
(1, 1, 0, 0, 2, 'obrazovanie'),
(7, 4, 0, 1, 2, 'povyshenie_kvalifikacii'),
(8, 0, 4, 7, 2, 'kompyuternye_kursy'),
(9, 5, 0, 0, 2, 'stroitelstvo'),
(10, 6, 0, 0, 2, 'ekologiya'),
(11, 7, 0, 0, 2, 'fizkultura_i_sport'),
(12, 8, 0, 1, 2, 'professionalnaya_perepodgotovka'),
(13, 9, 0, 9, 2, 'professionalnaya_perepodgotovka'),
(14, 10, 0, 9, 2, 'povysheniya_kvalifikacii'),
(15, 11, 0, 11, 2, 'povyshenie_kvalifikacii'),
(16, 12, 0, 11, 2, 'professionalnaya_perepodgotovka'),
(17, 13, 0, 10, 2, 'povyshenie_kvalifikacii'),
(18, 14, 0, 10, 2, 'professionalnaya_perepodgotovka'),
(19, 0, 5, 12, 2, 'osnovy_kompyuternoi_gramotnosti'),
(20, 0, 6, 12, 2, 'bezopasnost_stroitelstva_i_kachestvo_vozvedeniya_kamennyh_metallicheskih_i_derevyannyh_stroitelnyh_konstrukcii'),
(21, 0, 7, 14, 2, 'obespechenie_ekologicheskoi_bezopasnosti_rukovoditelyami_i_specialistami_obcshehozyaistvennyh_sistem_upravleniya');

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_techchars`
--

CREATE TABLE IF NOT EXISTS `catalog_techchars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `title` text NOT NULL,
  `left_part` text NOT NULL,
  `right_part` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_techchars_links`
--

CREATE TABLE IF NOT EXISTS `catalog_techchars_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) NOT NULL,
  `cat_cat_techchars_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0',
  `fioUser` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) DEFAULT NULL,
  `question` text NOT NULL,
  `fioSpecialist` varchar(255) DEFAULT NULL,
  `postSpecialist` varchar(150) DEFAULT NULL,
  `dateQuestion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `answer` text,
  `dateAnswer` timestamp NULL DEFAULT NULL,
  `IP` varchar(15) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `company` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `faq`
--

INSERT INTO `faq` (`id`, `pid`, `fioUser`, `email`, `phone`, `question`, `fioSpecialist`, `postSpecialist`, `dateQuestion`, `answer`, `dateAnswer`, `IP`, `active`, `feedback`, `company`) VALUES
(1, 40, 'Галицкий Юрий Сергеевич', 'olololo@yanda.ru', '8-927-789-20-97', 'Подскажите сколько стоит пройти курс "Информационная безопасность"?', NULL, NULL, '2014-03-26 09:25:00', '<p>Всю информацию касаемо курсов вы можете найти в разделе курсы.</p>', '1970-01-02 02:00:00', '127.0.0.1', 1, 0, ''),
(2, 40, 'Зайцева Виктория Сергеевна', 'razdvatri@yanda.ru', '8-927-789-20-97', 'Где я могу посмотреть курсы, на которые записалась?', NULL, NULL, '2014-03-26 09:46:00', '<p>Здравствуйте, Екатерина! Сложно не видя Вас, оценить пропорции Вашего тела! Тренируясь дома, возмодно Вы делаете в основном упражнения на мышцы ног (поэтому визуально Вам кажется, что ноги "потолстели". Я рекомендовала бы Вам заниматься по программе "BODY&amp;MIND" это сделает Ваше тело гибким, подтянутым!</p>', '1970-01-02 02:00:00', '127.0.0.1', 1, 0, ''),
(3, 40, 'Петров Алексей Сергеевич', 'olololo@yanda.ru', '8-927-789-20-97', 'Подскажите сколько стоит пройти курс "Информационная безопасность"?', NULL, NULL, '2014-03-26 10:21:00', '<p>Всю информацию касаемо курсов Вы можете найти в разделе "Курсы".</p>', '1970-01-02 02:00:00', '127.0.0.1', 1, 0, ''),
(4, 40, 'Сидорова Елена Петровна', 'olololo@yanda.ru', '8-927-789-20-97', 'Где я могу посмотреть курсы, на которые записалась?', NULL, NULL, '2014-03-26 11:00:00', '<p>Здравствуйте, Екатерина! Сложно не видя Вас, оценить пропорции Вашего тела! Тренируясь дома, возмодно Вы делаете в основном упражнения на мышцы ног (поэтому визуально Вам кажется, что ноги "потолстели". Я рекомендовала бы Вам заниматься по программе "BODY&amp;MIND" это сделает Ваше тело гибким, подтянутым!</p>', '1970-01-02 02:00:00', '127.0.0.1', 1, 0, ''),
(5, 40, 'Иванов Сергей Альбертович', 'olololo@yanda.ru', '8-927-789-20-97', 'Подскажите сколько стоит пройти курс "Информационная безопасность"?', NULL, NULL, '2014-03-26 11:04:00', '<p>Всю информацию касаемо курсов Вы можете найти в разделе "Курсы".</p>', '1970-01-02 02:00:00', '127.0.0.1', 1, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `feedback`
--

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `head` varchar(255) NOT NULL DEFAULT 'Задать вопрос',
  `fields` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `feedback_fields`
--

CREATE TABLE IF NOT EXISTS `feedback_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `rel` text NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `required` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0',
  `module_id` mediumint(5) NOT NULL DEFAULT '0',
  `filename` text NOT NULL,
  `filesize` int(20) NOT NULL DEFAULT '0',
  `filetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `extension` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort` smallint(5) NOT NULL DEFAULT '0',
  `is_show` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `files`
--

INSERT INTO `files` (`id`, `pid`, `module_id`, `filename`, `filesize`, `filetype`, `extension`, `date`, `sort`, `is_show`) VALUES
(1, 1, 1, 'Документ Microsoft Word', 22016, 'application/octet-stream', 'doc', '2014-02-21 07:29:17', 1, 1),
(2, 1, 1, 'Документ', 12657, 'application/octet-stream', 'docx', '2014-02-21 07:29:17', 2, 1),
(3, 1, 1, 'Новый текстовый документ', 1, 'application/octet-stream', 'txt', '2014-02-21 07:29:17', 3, 1),
(4, 1, 1, 'Архив Zip', 188, 'application/octet-stream', 'zip', '2014-02-21 07:29:17', 4, 1),
(5, 1, 1, 'Документ PDF', 79969, 'application/octet-stream', 'pdf', '2014-02-21 07:29:17', 5, 1),
(6, 1, 1, 'Лист Microsoft Excel', 8858, 'application/octet-stream', 'xls', '2014-02-21 07:29:17', 6, 1),
(7, 1, 1, 'Презентация Microsoft PowerPoint', 29784, 'application/octet-stream', 'ppt', '2014-02-21 07:29:17', 7, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `firms`
--

CREATE TABLE IF NOT EXISTS `firms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `print` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `fotogallery` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `module_id` mediumint(5) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `note` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `l_width` smallint(4) NOT NULL DEFAULT '0',
  `l_height` smallint(4) NOT NULL DEFAULT '0',
  `b_width` smallint(4) NOT NULL DEFAULT '0',
  `b_height` smallint(4) NOT NULL DEFAULT '0',
  `extension` varchar(5) NOT NULL DEFAULT 'jpg',
  `sort` int(5) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`id`, `pid`, `module_id`, `title`, `note`, `date`, `l_width`, `l_height`, `b_width`, `b_height`, `extension`, `sort`) VALUES
(12, 1, 1, 'g-m_zhaluzi_plisse_vesna_09.jpg', '', '2014-03-05 12:07:10', 0, 0, 800, 600, 'jpg', 5),
(6, 1, 1, '3.jpg', '', '2014-02-21 08:54:15', 0, 0, 800, 600, 'jpg', 3),
(5, 1, 1, '2.jpg', '', '2014-02-21 08:54:14', 0, 0, 800, 600, 'jpg', 2),
(4, 1, 1, '1.jpg', '', '2014-02-21 08:54:14', 0, 0, 800, 600, 'jpg', 1),
(11, 1, 1, '1.jpg', '', '2014-02-21 11:19:20', 0, 0, 800, 600, 'jpg', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `letters`
--

CREATE TABLE IF NOT EXISTS `letters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `try_count` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `priority` int(11) NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_groups`
--

CREATE TABLE IF NOT EXISTS `mail_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mail_task`
--

CREATE TABLE IF NOT EXISTS `mail_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `main`
--

CREATE TABLE IF NOT EXISTS `main` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `pid` int(5) NOT NULL DEFAULT '0',
  `cid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `title_page` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `module` mediumint(2) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  `link` varchar(255) NOT NULL DEFAULT '',
  `template` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `inmenu` tinyint(1) NOT NULL DEFAULT '1',
  `date` int(11) NOT NULL DEFAULT '0',
  `is_show_date` tinyint(4) NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL,
  `tree` mediumint(3) NOT NULL DEFAULT '0',
  `config` text NOT NULL,
  `fotogallery` tinyint(1) NOT NULL DEFAULT '1',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `print` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` int(11) NOT NULL DEFAULT '0',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `subsection` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Дамп данных таблицы `main`
--

INSERT INTO `main` (`id`, `pid`, `cid`, `title`, `title_page`, `alias`, `note`, `description`, `keywords`, `module`, `url`, `link`, `template`, `active`, `inmenu`, `date`, `is_show_date`, `source`, `tree`, `config`, `fotogallery`, `gallery_header`, `print`, `feedback`, `sendfile`, `subsection`) VALUES
(1, 0, 0, 'Об институте', 'Об институте', 'ob_institute', '', '', '', 1, '/pages/ob_institute/', '', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 1, 0, 0),
(2, 0, 0, 'Курсы', 'Курсы', 'kursy', '', '', '', 8, '/catalog/2/', '', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(7, 0, 0, 'Обучение', 'Обучение', 'obuchenie', '', '', '', 6, '/link/7/', '#', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(8, 0, 0, 'Инфоматериалы', 'Инфоматериалы', 'infomaterialy', '', '', '', 6, '/link/8/', '#', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(9, 0, 0, 'Портфолио', 'Портфолио', 'portfolio', '', '', '', 6, '/link/9/', '#', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(10, 0, 0, 'Жизнь института', 'Жизнь института', 'jizn_instituta', '', '', '', 6, '/link/10/', '#', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(11, 0, 0, 'Контакты', 'Контакты', 'kontakty', '', '', '', 1, '/pages/kontakty/', '', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(39, 0, 0, 'Новости', 'Новости', 'news', '', '', '', 2, '/news/39/', '', '', 1, 0, 0, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(40, 0, 0, 'Вопросы и ответы', 'Вопросы и ответы', 'vopros-otvet', '', '', '', 4, '/faq/40/', '', 'layoutFaq', 1, 0, 0, 0, '', 0, 'N;', 1, 1, 0, 0, 0, 0),
(41, 0, 0, 'Отзывы', 'Отзывы', 'otzyvy', '', '', '', 14, '/reviews/41/', '', '', 1, 0, 0, 0, '', 0, '', 1, 1, 0, 0, 0, 0),
(42, 0, 0, 'Акции', 'Акции', 'akcii', '', '', '', 12, '/actions/42/', '', '', 1, 0, 0, 0, '', 0, '', 1, 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `maps`
--

CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `maps_placemarks`
--

CREATE TABLE IF NOT EXISTS `maps_placemarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pid` (`pid`,`latitude`,`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `module`
--

CREATE TABLE IF NOT EXISTS `module` (
  `id` smallint(3) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `is_show_add` tinyint(1) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Дамп данных таблицы `module`
--

INSERT INTO `module` (`id`, `title`, `name`, `is_show_add`) VALUES
(1, 'Стандартная страница', 'pages', 1),
(2, 'Публикации', 'news', 1),
(4, 'Вопрос - ответ', 'faq', 1),
(6, 'Ссылка', 'link', 1),
(8, 'Каталог', 'catalog', 1),
(10, 'Статьи', 'articles', 1),
(11, 'Портфолио', 'portfolio', 1),
(12, 'Акции', 'actions', 1),
(14, 'Отзывы', 'reviews', 1),
(5, 'Профиль', 'profile', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alias` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `source` varchar(255) DEFAULT NULL,
  `fotogallery` varchar(100) DEFAULT 'simple',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `is_show_date` tinyint(4) NOT NULL DEFAULT '1',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `print` tinyint(1) NOT NULL DEFAULT '1',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `pid`, `title`, `note`, `date`, `alias`, `keywords`, `description`, `author`, `source`, `fotogallery`, `gallery_header`, `active`, `is_show_date`, `sendfile`, `print`, `feedback`) VALUES
(1, 39, 'Благодарность', '<p>31 января 2014 года закончили обучение слушатели курса &laquo;Психолого-педагогическое сопровождение детей с ОВЗ&raquo;. Сотрудники института выражают особую благодарность специалистам ГБУЗ Тольяттинского центра восстановительной медицины и реабилитации &laquo;Ариадна&raquo;, принимавшим участие в реализации программы повышения квалификации</p>', '2014-03-14 12:36:36', 'blagodarnost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(2, 39, 'Набор на курсы', '<p>Начинается набор на курсы профессиональной переподготовки по программе &laquo;Теория и практика дошкольного образования&raquo;. К обучению приглашаются лица, имеющие среднее профессиональное и высшее профессиональное образование: учителя, педагоги-психологи и иные специалисты. Начало обучения 03.03.2014</p>', '2014-03-14 12:37:03', 'nabor_na_kursy', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(3, 39, 'Вниманию специалистов сферы образования', '<p>Вниманию специалистов сферы образования!!! 18 октября 2013 года Приказом Минтруда N 544н утвержден профессиональный стандарт "Педагог (педагогическая деятельность в сфере дошкольного, начального общего, основного общего, среднего общего образования) (воспитатель, учитель)".</p>', '2014-03-14 12:37:28', 'vnimaniyu_specialistov_sfery_obrazovaniya', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(4, 39, 'Третья новость', '<p>Третья новость</p>', '2013-03-17 07:12:53', 'tretya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(5, 39, 'Четвертая новость', '<p>Четвертая новость</p>', '2014-03-17 07:13:08', 'chetvertaya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(6, 39, 'Пятая новость', '<p>Пятая новость</p>', '2014-03-17 07:13:20', 'pyataya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(7, 39, 'Шестая новость', '<p>Шестая новость</p>', '2014-03-17 07:13:35', 'shestaya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(8, 39, 'Седьмая новость', '<p>Седьмая новость</p>', '2014-03-17 07:13:46', 'sedmaya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(9, 39, 'Восьмая новость', '<p>Восьмая новость</p>', '2014-03-17 07:13:59', 'vosmaya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(10, 39, 'Девятая новость', '<p>Девятая новость</p>', '2014-03-17 07:14:13', 'devyataya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(11, 39, 'Десятая новость', '<p>Десятая новость</p>', '2014-03-17 07:14:27', 'desyataya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(12, 39, 'Одиннадцатая новость', '<p>Одиннадцатая новость</p>', '2014-03-17 07:14:42', 'odinnadcataya_novost', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0),
(13, 39, 'Двеннадцатая новость', '<p>Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость&nbsp;Двеннадцатая новость</p>', '2014-03-17 07:14:50', 'dvenn', '', NULL, '', '', 'simple', 1, 1, 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `order_history`
--

CREATE TABLE IF NOT EXISTS `order_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `time` int(11) DEFAULT '0',
  `ip` varchar(15) DEFAULT NULL,
  `total_sum` float DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `order_history_prod`
--

CREATE TABLE IF NOT EXISTS `order_history_prod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `catalog_id` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `price` float NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `price_all` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `partners`
--

CREATE TABLE IF NOT EXISTS `partners` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `alias` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `fotogallery` varchar(100) DEFAULT 'simple',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `is_show_date` tinyint(4) NOT NULL DEFAULT '1',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `print` tinyint(1) NOT NULL DEFAULT '1',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `task` text NOT NULL,
  `created` text NOT NULL,
  `result` text NOT NULL,
  `review_head` varchar(255) NOT NULL,
  `review` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `personal`
--

CREATE TABLE IF NOT EXISTS `personal` (
  `id` mediumint(4) NOT NULL AUTO_INCREMENT,
  `pid` int(4) NOT NULL DEFAULT '0',
  `fio` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `post` varchar(150) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `workPhone` varchar(20) DEFAULT NULL,
  `mobilePhone` varchar(20) DEFAULT NULL,
  `icq` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `about` text,
  `direktor` tinyint(1) NOT NULL DEFAULT '0',
  `direktorWord` text NOT NULL,
  `direktorTemplate` varchar(60) NOT NULL DEFAULT 'direktorWord',
  `fotogallery` tinyint(1) NOT NULL DEFAULT '1',
  `print` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `module_id` mediumint(5) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `l_width` smallint(4) NOT NULL DEFAULT '0',
  `l_height` smallint(4) NOT NULL DEFAULT '0',
  `b_width` smallint(4) NOT NULL DEFAULT '0',
  `b_height` smallint(4) NOT NULL DEFAULT '0',
  `extension` varchar(5) NOT NULL DEFAULT 'jpg',
  `sort` int(5) NOT NULL DEFAULT '0',
  `note` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio`
--

CREATE TABLE IF NOT EXISTS `portfolio` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `note` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `alias` varchar(255) NOT NULL,
  `show_in_main` int(11) NOT NULL DEFAULT '0' COMMENT 'Отображение не главной странице',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `fotogallery` varchar(100) DEFAULT 'simple',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `is_show_date` tinyint(4) NOT NULL DEFAULT '1',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `print` tinyint(1) NOT NULL DEFAULT '1',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `task` text NOT NULL,
  `created` text NOT NULL,
  `result` text NOT NULL,
  `review_head` varchar(255) NOT NULL,
  `review` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `product_minigallery`
--

CREATE TABLE IF NOT EXISTS `product_minigallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `filename` varchar(1000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `date_begin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `question_answer`
--

CREATE TABLE IF NOT EXISTS `question_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) DEFAULT '0',
  `count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `question_ip`
--

CREATE TABLE IF NOT EXISTS `question_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` char(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `recalls`
--

CREATE TABLE IF NOT EXISTS `recalls` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0',
  `fio` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `text` text,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) NOT NULL DEFAULT '0',
  `fioUser` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(255) DEFAULT NULL,
  `question` text NOT NULL,
  `fioSpecialist` varchar(255) DEFAULT NULL,
  `postSpecialist` varchar(150) DEFAULT NULL,
  `dateQuestion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `answer` text,
  `dateAnswer` timestamp NULL DEFAULT NULL,
  `IP` varchar(15) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `company` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id`, `pid`, `fioUser`, `email`, `phone`, `question`, `fioSpecialist`, `postSpecialist`, `dateQuestion`, `answer`, `dateAnswer`, `IP`, `active`, `feedback`, `company`) VALUES
(10, 41, 'Панин Александр Валерьевич', '', NULL, 'На нашей планете есть удивительная народность вегетарианцев, не знающая болезней и имеющая среднюю продолжительность жизни в 110-120 лет. Они живут на севере Индии в Гималаях в очень суровых условиях, на берегу реки Хунзы, в 100 километрах от самого северного', NULL, 'студент', '2014-03-26 02:00:00', NULL, NULL, '127.0.0.1', 1, 0, ''),
(11, 41, 'Иванов Сергей Петрович', '', NULL, 'На нашей планете есть удивительная народность вегетарианцев, не знающая болезней и имеющая среднюю продолжительность жизни в 110-120 лет. Они живут на севере Индии в Гималаях в очень суровых условиях, на берегу реки Хунзы, в 100 километрах от самого северного', NULL, 'директор фирмы заказчика', '2014-03-20 02:00:00', NULL, NULL, '127.0.0.1', 1, 0, ''),
(12, 41, 'Дроздова Юлия Сергеевна', '', NULL, 'На нашей планете есть удивительная народность вегетарианцев, не знающая болезней и имеющая среднюю продолжительность жизни в 110-120 лет. Они живут на севере Индии в Гималаях в очень суровых условиях, на берегу реки Хунзы, в 100 километрах от самого северного', NULL, 'студент', '2014-03-26 08:42:00', NULL, NULL, '127.0.0.1', 1, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `search_index`
--

CREATE TABLE IF NOT EXISTS `search_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `text` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Дамп данных таблицы `search_index`
--

INSERT INTO `search_index` (`id`, `pid`, `module_id`, `title`, `text`) VALUES
(1, 1, 1, 'Об институте', 'Негосударственное образовательное учреждение &laquo;Межрегиональный институт дополнительного профессионального образования&raquo;&nbsp;(НОУ &laquo;МИДПО&raquo;)&nbsp;оказывает образовательные услуги специалистам предприятий и организаций в разных сферах деятельности с целью обновления знаний, умений, навыков, роста профессионального мастерства, изучения новых технологий. Дополнительное профессиональное образование решает актуальные задачи и представлено в виде:\r\nПовышения квалификации - профессиональное совершенствование в соответствии с постоянно изменяющимися условиями производственной деятельности и стратегическим развитием предприятий.\r\nМежрегиональный институт дополнительного профессионального образования предлагает различные формы освоения образовательных программ в зависимости от условий, запросов, потребностей слушателей и заказчиков: с отрывом от производства (очное), с частичным отрывом от производства (очно-заочное) и дистанционное обучение.\r\nНОУ &laquo;МИДПО&raquo;&nbsp;предлагает курсы повышения квалификации в области строительной деятельности:\r\n\r\nРаботы по строительству, реконструкции и капитальному ремонту - 16 программ;\r\nРаботы по инженерным изысканиям &ndash; 5 программ;\r\nРаботы по подготовке проектной документации &ndash; 21 программа\r\n\r\nПовышение квалификации по особоопасным, технически сложным и уникальным объектам проводиться согласно техническому заданию организации.\r\nДля эффективного решения поставленных государством задач в области энергосбережения согласно новому законодательству разработаны и применяются на практике программы по курсам:\r\n\r\n&laquo;Энергетическое обследование (энергоаудит) предприятий&raquo;;\r\n&laquo;Основы энергобезопасности. Управление энергоэфективностью предприятия&raquo;;\r\n&laquo;Энергетическое обследование (энергоаудит) &raquo; предприятий &mdash; теория и практика. Основы энергетического менеджмента&raquo;;\r\n&laquo;Основы эффективной и надежной эксплуатации систем централизованного теплоснабжения&raquo;;\r\nПодготовка профессиональных энергоаудиторов: &laquo;Энергетические обследования (энергоаудит) тепло- и топливопотребляющих установок и сетей и энергетические обследования (энергоаудит) электрических установок и сетей&raquo;.\r\n\r\nПроцесс обучения (повышения квалификации) рассчитан на руководителей высшего и среднего звена управления предприятиями.\r\n\r\n\r\n\r\nСтолбец 1\r\nСтолбец 2\r\nСтолбец 3\r\n\r\n\r\nСтолбец 1\r\nСтолбец 2\r\nСтолбец 3\r\n\r\n\r\nСтолбец 1\r\nСтолбец 2\r\nСтолбец 3\r\n\r\n\r\n\r\nМежрегиональный институт дополнительного профессионального образования &nbsp;имеет возможность реализации программ повышения квалификации по требованиям Заказчика в рамках нормативно-правового поля системы дополнительного профессионального образования Российской Федерации и готов к сотрудничеству.'),
(2, 11, 1, 'Контакты', 'НОУ &laquo;МИДПО&raquo;\r\nАдрес : Юридический:&nbsp;&nbsp; 445012 г. Тольятти ул. Матросова д. 20 кв. 172\r\nФактический 445044, Самарская обл., г. Тольятти, ул. 70 лет Октября, 90\r\nТел.:\r\nSkype:\r\nE-mail:\r\nСайт:&nbsp;www.midpo.ru\r\nБанковские реквизиты\r\nЮридический адрес: 445012 г. Тольятти ул. Матросова д. 20 кв. 172\r\nФактический: 445044, Самарская обл., г. Тольятти, ул. 70 лет Октября, 90\r\nИНН/КПП 6324997668/632401001\r\nОГРН 1136300000176\r\nр/с 40703810110190001049\r\nФилиал № 6318 ВТБ 24 (ЗАО) г.Самара\r\n445011, г. Тольятти, ул. Жилина, 9\r\nк/с 30101810700000000955\r\nБИК 043602955 ОГРН 1027739207462\r\nОКПО 21108948\r\nОКВЭД: 80.30.3; 80.22.1; 80.22.22; 80.42\r\nДиректор: Матуняк Наталья Анатольевна на основании устава'),
(35, 8, 200, 'профессиональной переподготовки', 'Профессиональная переподготовка'),
(36, 9, 200, 'профессиональной переподготовки', 'Профессиональная переподготовка'),
(37, 10, 200, 'повышения квалификации', 'Повышение квалификации'),
(38, 11, 200, 'повышения квалификации', 'Повышение квалификации'),
(39, 12, 200, 'профессиональной переподготовки', 'Профессиональная переподготовка'),
(40, 13, 200, 'повышения квалификации', 'Повышение квалификации'),
(41, 14, 200, 'профессиональной переподготовки', 'Профессиональная переподготовка'),
(25, 1, 200, 'Образование', 'Образование'),
(34, 7, 200, 'Физкультура и спорт', 'Физкультура и спорт'),
(30, 4, 200, 'повышения квалификации', 'Повышение квалификации'),
(31, 4, 300, 'Компьютерные курсы', 'Компьютерные курсы'),
(32, 5, 200, 'Строительство', 'Строительство'),
(33, 6, 200, 'Экология', 'Экология'),
(42, 5, 300, 'Основы компьютерной грамотности', 'Основы компьютерной грамотности'),
(43, 6, 300, 'Безопасность строительства и качество возведения каменных, металлических и деревянных строительных конструкций', 'Безопасность строительства и качество возведения каменных, металлических и деревянных строительных конструкций\r\nНегосударственное образовательное учреждение "Межрегиональный институт дополнительного профессионального образования (НОУ "МИДПО") оказывает образовательные услуги специалистам предприятий и организаций в разных сферах деятельности с целью обновления знаний, умений, навыков, роста профессионального мастерства, изучения новых технологий. Дополнительное профессиональное образование решает актуальные задачи и представлено в виде:\r\nПовышения квалификации - профессиональное совершенствование в соответствии с постоянно изменяющимися условиями производственной деятельности и стратегическим развитием предприятий.\r\nМежрегиональный институт дополнительного профессионального образования предлагает раздичные формы освоения образовательных программ в зависимости от условий, запросов, потребностей слушателей и заказчиков: с отрывом от производства (очное), с частичным отрывом от производства (очно-заочное) и дистанционное обучение.'),
(44, 7, 300, 'Обеспечение экологической безопасности руководителями и специалистами общехозяйственных систем управления', 'Обеспечение экологической безопасности руководителями и специалистами общехозяйственных систем управления'),
(45, 1, 2, 'Благодарность', '31 января 2014 года закончили обучение слушатели курса &laquo;Психолого-педагогическое сопровождение детей с ОВЗ&raquo;. Сотрудники института выражают особую благодарность специалистам ГБУЗ Тольяттинского центра восстановительной медицины и реабилитации &laquo;Ариадна&raquo;, принимавшим участие в реализации программы повышения квалификации'),
(46, 2, 2, 'Набор на курсы', 'Начинается набор на курсы профессиональной переподготовки по программе &laquo;Теория и практика дошкольного образования&raquo;. К обучению приглашаются лица, имеющие среднее профессиональное и высшее профессиональное образование: учителя, педагоги-психологи и иные специалисты. Начало обучения 03.03.2014'),
(47, 3, 2, 'Вниманию специалистов сферы образования', 'Вниманию специалистов сферы образования!!! 18 октября 2013 года Приказом Минтруда N 544н утвержден профессиональный стандарт "Педагог (педагогическая деятельность в сфере дошкольного, начального общего, основного общего, среднего общего образования) (воспитатель, учитель)".'),
(48, 4, 2, 'Третья новость', 'Третья новость'),
(49, 5, 2, 'Четвертая новость', 'Четвертая новость'),
(50, 6, 2, 'Пятая новость', 'Пятая новость'),
(51, 7, 2, 'Шестая новость', 'Шестая новость'),
(52, 8, 2, 'Седьмая новость', 'Седьмая новость'),
(53, 9, 2, 'Восьмая новость', 'Восьмая новость'),
(54, 10, 2, 'Девятая новость', 'Девятая новость'),
(55, 11, 2, 'Десятая новость', 'Десятая новость'),
(56, 12, 2, 'Одиннадцатая новость', 'Одиннадцатая новость'),
(57, 13, 2, 'Двеннадцатая новость', 'Двеннадцатая новость');

-- --------------------------------------------------------

--
-- Структура таблицы `slides`
--

CREATE TABLE IF NOT EXISTS `slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `note` varchar(355) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `slides`
--

INSERT INTO `slides` (`id`, `title`, `link`, `note`) VALUES
(1, 'Экология', '/uslugi/audit_sistemi_osveszheniya/', '<p>Экология</p>'),
(2, 'Строительство', '#', '<p>Строительство</p>'),
(3, 'Образование', '#', '<p>Модернизация образования требует нового качества работы педагогов. Наш институт, &nbsp;учитывая происходящие процессы обновления содержания образования, появление новых педагогических технологий, организует курсы повышения квалификации и профессиональной переподготовки.</p>'),
(4, 'Физкультура и спорт', '#', '<p>Физкультура и спорт</p>');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL DEFAULT '',
  `pass` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `state` tinyint(1) DEFAULT '0',
  `about` text NOT NULL,
  `bday` int(11) NOT NULL DEFAULT '0',
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `flush` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `module_id` mediumint(5) NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sort` int(5) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

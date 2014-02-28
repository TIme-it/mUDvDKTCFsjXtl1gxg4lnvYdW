-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 28 2014 г., 12:32
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `price_hour` float NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `fotogallery` tinyint(1) NOT NULL DEFAULT '1',
  `gallery_header` tinyint(1) NOT NULL DEFAULT '1',
  `print` tinyint(1) NOT NULL DEFAULT '0',
  `feedback` tinyint(1) NOT NULL DEFAULT '0',
  `sendfile` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  `is_new` tinyint(1) NOT NULL DEFAULT '0',
  `is_leader` tinyint(1) NOT NULL DEFAULT '0',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `catalog`
--

INSERT INTO `catalog` (`id`, `pid`, `cid`, `lid`, `title`, `note`, `price_hour`, `description`, `keywords`, `fotogallery`, `gallery_header`, `print`, `feedback`, `sendfile`, `active`, `is_new`, `is_leader`, `is_popular`, `taitle`, `price_turn`, `geo`, `fin_type`, `type`, `author`, `phone`, `date`, `top`) VALUES
(4, 4, 2, 8, 'Компьютерные курсы', '<p>Компьютерные курсы</p>', 0, '', '', 1, 1, 0, 0, 0, 0, 0, 0, 0, 'Компьютерные курсы', 0, 0, '0', 0, '', '', 1393504780, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `catalog_categories`
--

INSERT INTO `catalog_categories` (`id`, `pid`, `cid`, `lid`, `title`, `note`, `description`, `keywords`, `first_name_column`, `taitle`) VALUES
(1, 0, 2, 1, 'Образование', 'Образование', '', '', '', 'Образование'),
(4, 1, 2, 7, 'повышения квалификации', 'Повышение квалификации', '', '', 'Наименование', 'Повышение квалификации');

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
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `catalog_links`
--

INSERT INTO `catalog_links` (`id`, `cat_id`, `prod_id`, `pid`, `cid`, `alias`) VALUES
(1, 1, 0, 0, 2, 'obrazovanie'),
(7, 4, 0, 1, 2, 'povyshenie_kvalifikacii'),
(8, 0, 4, 7, 2, 'kompyuternye_kursy');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Дамп данных таблицы `images`
--

INSERT INTO `images` (`id`, `pid`, `module_id`, `title`, `note`, `date`, `l_width`, `l_height`, `b_width`, `b_height`, `extension`, `sort`) VALUES
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;

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
(11, 0, 0, 'Контакты', 'Контакты', 'kontakty', '', '', '', 1, '/pages/kontakty/', '', '', 1, 1, 1392321600, 0, '', 0, '', 1, 1, 0, 0, 0, 0);

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
(14, 'Отзывы', 'reviews', 1);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Дамп данных таблицы `search_index`
--

INSERT INTO `search_index` (`id`, `pid`, `module_id`, `title`, `text`) VALUES
(1, 1, 1, 'Об институте', 'Негосударственное образовательное учреждение &laquo;Межрегиональный институт дополнительного профессионального образования&raquo;&nbsp;(НОУ &laquo;МИДПО&raquo;)&nbsp;оказывает образовательные услуги специалистам предприятий и организаций в разных сферах деятельности с целью обновления знаний, умений, навыков, роста профессионального мастерства, изучения новых технологий. Дополнительное профессиональное образование решает актуальные задачи и представлено в виде:\r\nПовышения квалификации - профессиональное совершенствование в соответствии с постоянно изменяющимися условиями производственной деятельности и стратегическим развитием предприятий.\r\nМежрегиональный институт дополнительного профессионального образования предлагает различные формы освоения образовательных программ в зависимости от условий, запросов, потребностей слушателей и заказчиков: с отрывом от производства (очное), с частичным отрывом от производства (очно-заочное) и дистанционное обучение.\r\nНОУ &laquo;МИДПО&raquo;&nbsp;предлагает курсы повышения квалификации в области строительной деятельности:\r\n\r\nРаботы по строительству, реконструкции и капитальному ремонту - 16 программ;\r\nРаботы по инженерным изысканиям &ndash; 5 программ;\r\nРаботы по подготовке проектной документации &ndash; 21 программа\r\n\r\nПовышение квалификации по особоопасным, технически сложным и уникальным объектам проводиться согласно техническому заданию организации.\r\nДля эффективного решения поставленных государством задач в области энергосбережения согласно новому законодательству разработаны и применяются на практике программы по курсам:\r\n\r\n&laquo;Энергетическое обследование (энергоаудит) предприятий&raquo;;\r\n&laquo;Основы энергобезопасности. Управление энергоэфективностью предприятия&raquo;;\r\n&laquo;Энергетическое обследование (энергоаудит) &raquo; предприятий &mdash; теория и практика. Основы энергетического менеджмента&raquo;;\r\n&laquo;Основы эффективной и надежной эксплуатации систем централизованного теплоснабжения&raquo;;\r\nПодготовка профессиональных энергоаудиторов: &laquo;Энергетические обследования (энергоаудит) тепло- и топливопотребляющих установок и сетей и энергетические обследования (энергоаудит) электрических установок и сетей&raquo;.\r\n\r\nПроцесс обучения (повышения квалификации) рассчитан на руководителей высшего и среднего звена управления предприятиями.\r\n\r\n\r\n\r\nСтолбец 1\r\nСтолбец 2\r\nСтолбец 3\r\n\r\n\r\nСтолбец 1\r\nСтолбец 2\r\nСтолбец 3\r\n\r\n\r\nСтолбец 1\r\nСтолбец 2\r\nСтолбец 3\r\n\r\n\r\n\r\nМежрегиональный институт дополнительного профессионального образования &nbsp;имеет возможность реализации программ повышения квалификации по требованиям Заказчика в рамках нормативно-правового поля системы дополнительного профессионального образования Российской Федерации и готов к сотрудничеству.'),
(2, 11, 1, 'Контакты', 'НОУ &laquo;МИДПО&raquo;\r\nАдрес : Юридический:&nbsp;&nbsp; 445012 г. Тольятти ул. Матросова д. 20 кв. 172\r\nФактический 445044, Самарская обл., г. Тольятти, ул. 70 лет Октября, 90\r\nТел.:\r\nSkype:\r\nE-mail:\r\nСайт:&nbsp;www.midpo.ru\r\nБанковские реквизиты\r\nЮридический адрес: 445012 г. Тольятти ул. Матросова д. 20 кв. 172\r\nФактический: 445044, Самарская обл., г. Тольятти, ул. 70 лет Октября, 90\r\nИНН/КПП 6324997668/632401001\r\nОГРН 1136300000176\r\nр/с 40703810110190001049\r\nФилиал № 6318 ВТБ 24 (ЗАО) г.Самара\r\n445011, г. Тольятти, ул. Жилина, 9\r\nк/с 30101810700000000955\r\nБИК 043602955 ОГРН 1027739207462\r\nОКПО 21108948\r\nОКВЭД: 80.30.3; 80.22.1; 80.22.22; 80.42\r\nДиректор: Матуняк Наталья Анатольевна на основании устава'),
(25, 1, 200, 'Образование', 'Образование'),
(30, 4, 200, 'повышения квалификации', 'Повышение квалификации'),
(31, 4, 300, 'Компьютерные курсы', 'Компьютерные курсы');

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
(1, 'Экология', '#', '<p>Экология</p>'),
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

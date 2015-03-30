SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- База данных: `pms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `login` varchar(100) NOT NULL,
  `password` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `name` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `phone` varchar(20) default NULL,
  `state` text COMMENT 'store user interface state',
  `active` tinyint(1) NOT NULL default '1',
  `radius` tinyint(1) default '0',
  `anonymous` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `anonymous` (`anonymous`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`id`, `login`, `password`, `role_id`, `name`, `email`, `phone`, `state`, `active`, `radius`, `anonymous`) VALUES
(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 1, 'Администратор', 'bvh.box@gmail.com', '', NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `acl_permissions`
--

CREATE TABLE IF NOT EXISTS `acl_permissions` (
  `id` int(11) NOT NULL auto_increment,
  `role_id` int(11) unsigned NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `privilege_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `role_id_2` (`role_id`,`resource_id`,`privilege_id`),
  KEY `fk_role_id` (`role_id`),
  KEY `fk_resource_id` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1342 ;

--
-- Дамп данных таблицы `acl_permissions`
--

INSERT INTO `acl_permissions` (`id`, `role_id`, `resource_id`, `privilege_id`) VALUES
(1094, 1, 50, 1),
(1095, 1, 50, 2),
(1096, 1, 50, 3),
(1097, 1, 50, 4),
(1102, 1, 123, 1),
(1103, 1, 123, 2),
(1104, 1, 123, 3),
(1105, 1, 123, 4),
(1114, 1, 126, 1),
(1115, 1, 126, 2),
(1116, 1, 126, 3),
(1117, 1, 126, 4),
(1133, 1, 127, 1),
(1135, 1, 127, 2),
(1137, 1, 127, 3),
(1139, 1, 127, 4),
(1154, 1, 129, 1),
(1159, 1, 129, 2),
(1169, 1, 129, 3),
(1170, 1, 129, 4),
(1155, 1, 130, 1),
(1160, 1, 130, 2),
(1168, 1, 130, 3),
(1171, 1, 130, 4),
(1156, 1, 131, 1),
(1161, 1, 131, 2),
(1167, 1, 131, 3),
(1172, 1, 131, 4),
(1157, 1, 132, 1),
(1162, 1, 132, 2),
(1165, 1, 132, 3),
(1173, 1, 132, 4),
(1158, 1, 133, 1),
(1163, 1, 133, 2),
(1166, 1, 133, 3),
(1174, 1, 133, 4),
(1216, 1, 136, 1),
(1220, 1, 136, 2),
(1225, 1, 136, 3),
(1226, 1, 136, 4),
(1217, 1, 137, 1),
(1221, 1, 137, 2),
(1224, 1, 137, 3),
(1227, 1, 137, 4),
(1218, 1, 138, 1),
(1222, 1, 138, 2),
(1231, 1, 138, 3),
(1228, 1, 138, 4),
(1219, 1, 139, 1),
(1223, 1, 139, 2),
(1230, 1, 139, 3),
(1229, 1, 139, 4),
(1233, 1, 142, 1),
(1237, 1, 142, 2),
(1241, 1, 142, 3),
(1245, 1, 142, 4),
(1232, 1, 143, 1),
(1236, 1, 143, 2),
(1240, 1, 143, 3),
(1244, 1, 143, 4),
(1248, 1, 144, 1),
(1250, 1, 144, 2),
(1252, 1, 144, 3),
(1254, 1, 144, 4),
(1249, 1, 145, 1),
(1251, 1, 145, 2),
(1253, 1, 145, 3),
(1255, 1, 145, 4),
(1256, 1, 146, 1),
(1257, 1, 146, 2),
(1258, 1, 146, 3),
(1259, 1, 146, 4),
(1260, 1, 147, 1),
(1261, 1, 147, 2),
(1262, 1, 147, 3),
(1263, 1, 147, 4),
(1264, 1, 148, 1),
(1265, 1, 148, 2),
(1266, 1, 148, 3),
(1267, 1, 148, 4),
(1268, 1, 149, 1),
(1269, 1, 149, 2),
(1270, 1, 149, 3),
(1271, 1, 149, 4),
(1272, 1, 150, 1),
(1273, 1, 150, 2),
(1274, 1, 150, 3),
(1275, 1, 150, 4),
(1341, 2, 123, 1),
(1118, 3, 123, 1),
(1327, 3, 123, 2),
(1120, 3, 123, 3),
(1121, 3, 123, 4),
(1124, 3, 126, 1),
(1328, 3, 126, 2),
(1128, 3, 126, 3),
(1129, 3, 126, 4),
(1301, 3, 127, 1),
(1175, 3, 129, 1),
(1329, 3, 129, 2),
(1177, 3, 129, 3),
(1178, 3, 129, 4),
(1179, 3, 130, 1),
(1331, 3, 130, 3),
(1180, 3, 131, 1),
(1332, 3, 131, 3),
(1181, 3, 132, 1),
(1185, 3, 132, 3),
(1187, 3, 132, 4),
(1182, 3, 133, 1),
(1186, 3, 133, 3),
(1188, 3, 133, 4),
(1202, 3, 134, 1),
(1204, 3, 134, 3),
(1205, 3, 134, 4),
(1337, 3, 144, 3),
(1300, 3, 146, 1),
(1302, 3, 147, 1),
(1340, 3, 147, 3),
(1338, 3, 150, 3),
(1148, 4, 123, 1),
(1149, 4, 123, 3),
(1150, 4, 127, 1),
(1151, 4, 127, 2),
(1152, 4, 127, 3),
(1153, 4, 127, 4),
(1196, 4, 129, 1),
(1197, 4, 130, 1),
(1201, 4, 130, 3),
(1198, 4, 131, 1),
(1199, 4, 132, 1),
(1200, 4, 133, 1),
(1130, 5, 123, 1),
(1147, 5, 123, 3),
(1314, 5, 127, 1),
(1325, 5, 127, 2),
(1322, 5, 127, 3),
(1326, 5, 127, 4),
(1189, 5, 129, 1),
(1190, 5, 130, 1),
(1191, 5, 131, 1),
(1194, 5, 131, 3),
(1192, 5, 132, 1),
(1193, 5, 133, 1),
(1303, 5, 136, 1),
(1304, 5, 137, 1),
(1305, 5, 138, 1),
(1306, 5, 139, 1),
(1307, 5, 142, 1),
(1317, 5, 142, 3),
(1308, 5, 143, 1),
(1318, 5, 143, 3),
(1311, 5, 144, 1),
(1312, 5, 145, 1),
(1315, 5, 146, 1),
(1316, 5, 147, 1),
(1309, 5, 148, 1),
(1319, 5, 148, 3),
(1310, 5, 149, 1),
(1320, 5, 149, 3),
(1313, 5, 150, 1),
(1321, 5, 150, 3),
(1276, 6, 123, 1),
(1277, 6, 126, 1),
(1294, 6, 127, 1),
(1278, 6, 129, 1),
(1279, 6, 130, 1),
(1284, 6, 131, 1),
(1289, 6, 132, 1),
(1292, 6, 133, 1),
(1280, 6, 136, 1),
(1281, 6, 137, 1),
(1282, 6, 138, 1),
(1283, 6, 139, 1),
(1285, 6, 142, 1),
(1286, 6, 143, 1),
(1290, 6, 144, 1),
(1291, 6, 145, 1),
(1296, 6, 146, 1),
(1297, 6, 146, 2),
(1298, 6, 146, 3),
(1299, 6, 146, 4),
(1295, 6, 147, 1),
(1287, 6, 148, 1),
(1288, 6, 149, 1),
(1293, 6, 150, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `acl_resources`
--

CREATE TABLE IF NOT EXISTS `acl_resources` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(100) character set utf8 NOT NULL,
  `title` varchar(100) collate utf8_unicode_ci default NULL,
  `parent_id` int(11) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`,`parent_id`),
  KEY `fk_parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=153 ;

--
-- Дамп данных таблицы `acl_resources`
--

INSERT INTO `acl_resources` (`id`, `name`, `title`, `parent_id`) VALUES
(50, 'admin', 'Администрирование', NULL),
(123, 'orders', 'Заказы', NULL),
(126, 'cost', 'Стоимость', 123),
(127, 'suppliers', 'Поставщики', NULL),
(129, 'address', 'Адрес', 123),
(130, 'production', 'Производство', 123),
(131, 'mount', 'Монтаж', 123),
(132, 'success', 'Выполнено', 123),
(133, 'description', 'Описание', 123),
(134, 'owncheck', 'Скрывать не свои заказы', 123),
(136, 'start_planned', 'Начало (план)', 130),
(137, 'end_planned', 'Конец (план)', 130),
(138, 'start_fact', 'Начало (факт)', 130),
(139, 'end_fact', 'Конец (факт)', 130),
(142, 'end_planned', 'Конец (план)', 131),
(143, 'end_fact', 'Конец (факт)', 131),
(144, 'planned', 'План', 132),
(145, 'fact', 'Факт', 132),
(146, 'archive', 'Архив', NULL),
(147, 'customers', 'Заказчики', NULL),
(148, 'start_fact', 'Начало (факт)', 131),
(149, 'start_planned', 'Начало (план)', 131),
(150, 'files', 'Файлы', 123),
(151, 'hideproduction', 'Скрывать заказы без производства', 123),
(152, 'hidemount', 'Скрывать заказы без монтажа', 123);

-- --------------------------------------------------------

--
-- Структура таблицы `acl_roles`
--

CREATE TABLE IF NOT EXISTS `acl_roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(50) character set utf8 NOT NULL,
  `alias` varchar(40) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `acl_roles`
--

INSERT INTO `acl_roles` (`id`, `name`, `alias`) VALUES
(1, 'Администратор', NULL),
(3, 'Менеджер', NULL),
(4, 'Производство', NULL),
(5, 'Монтаж', NULL),
(6, 'Бухгалтер', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=29 ;


-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  `is_photo` tinyint(1) unsigned NOT NULL default '0',
  `original_name` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;


-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL default '0',
  `abbreviation` varchar(5) default NULL,
  `caption` varchar(75) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `abbreviation` (`abbreviation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Дамп данных таблицы `languages`
--

INSERT INTO `languages` (`id`, `abbreviation`, `caption`) VALUES
(1, 'en', 'English'),
(2, 'nl', 'Dutch');

-- --------------------------------------------------------

--
-- Структура таблицы `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;


-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned default NULL,
  `address` text,
  `description` text,
  `mount` tinyint(1) unsigned NOT NULL default '1',
  `production` tinyint(1) unsigned NOT NULL default '1',
  `production_start_planned` date default NULL,
  `production_start_fact` date default NULL,
  `production_end_planned` date default NULL,
  `production_end_fact` date default NULL,
  `mount_start_planned` date default NULL,
  `mount_start_fact` date default NULL,
  `mount_end_planned` date default NULL,
  `mount_end_fact` date default NULL,
  `success_date_planned` date default NULL,
  `success_date_fact` date default NULL,
  `cost` int(10) default NULL,
  `advanse` int(10) default NULL,
  `created` timestamp NULL default CURRENT_TIMESTAMP,
  `creator_id` int(11) unsigned default NULL,
  `archive` tinyint(1) unsigned NOT NULL default '0',
  `archive_date` timestamp NULL default NULL,
  `invoice_number` varchar(255) default NULL,
  `invoice_date` date default NULL,
  `act_number` varchar(255) default NULL,
  `act_date` date default NULL,
  PRIMARY KEY  (`id`),
  KEY `creator_id` (`creator_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;


-- --------------------------------------------------------

--
-- Структура таблицы `orders_suppliers`
--

CREATE TABLE IF NOT EXISTS `orders_suppliers` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL,
  `supplier_id` int(11) unsigned NOT NULL,
  `success` tinyint(1) NOT NULL,
  `date` date NOT NULL,
  `cost` int(11) default NULL,
  `note` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `order_id_2` (`order_id`,`supplier_id`),
  KEY `order_id` (`order_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;


-- --------------------------------------------------------

--
-- Структура таблицы `suppliers`
--

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;


--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`);

--
-- Ограничения внешнего ключа таблицы `acl_permissions`
--
ALTER TABLE `acl_permissions`
  ADD CONSTRAINT `acl_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_permissions_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `acl_resources` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `acl_resources`
--
ALTER TABLE `acl_resources`
  ADD CONSTRAINT `acl_resources_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `acl_resources` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`creator_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `orders_suppliers`
--
ALTER TABLE `orders_suppliers`
  ADD CONSTRAINT `orders_suppliers_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_suppliers_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

<?php
$query = " 
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block` (
  `id_loffc_block` int(11) NOT NULL AUTO_INCREMENT,
  `width` float(10,2) NOT NULL,
  `show_title` tinyint(1) NOT NULL,
  `id_position` tinyint(2) NOT NULL,
  PRIMARY KEY (`id_loffc_block`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;

INSERT INTO `_DB_PREFIX_loffc_block` (`id_loffc_block`, `width`, `show_title`, `id_position`) VALUES
(60, 24.00, 1, 2),
(61, 20.00, 1, 2),
(62, 20.00, 1, 2),
(63, 20.00, 1, 2),
(64, 21.00, 1, 1),
(65, 21.00, 1, 1),
(66, 21.00, 1, 1),
(67, 21.00, 1, 1);

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_item` (
  `id_loffc_block_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_loffc_block` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `link` varchar(2000) NOT NULL,
  `linktype` varchar(25) NOT NULL,
  `link_content` varchar(2000) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `hook_name` varchar(100) NOT NULL,
  `latitude` varchar(25) NOT NULL,
  `longitude` varchar(25) NOT NULL,
  `addthis` tinyint(1) NOT NULL,
  `show_title` tinyint(1) NOT NULL DEFAULT '1',
  `target` varchar(20) NOT NULL DEFAULT '_self',
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id_loffc_block_item`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

INSERT INTO `_DB_PREFIX_loffc_block_item` (`id_loffc_block_item`, `id_loffc_block`, `type`, `link`, `linktype`, `link_content`, `module_name`, `hook_name`, `latitude`, `longitude`, `addthis`, `show_title`, `target`, `position`) VALUES
(99, 60, 'custom_html', '', '', '', '', '', '', '', 0, 0, '', 0),
(100, 63, 'link', '', 'cms', '3', '', '', '', '', 0, 1, '', 0),
(101, 62, 'link', '', 'category', '4', '', '', '', '', 0, 1, '', 0),
(102, 61, 'link', '', 'category', '1', '', '', '', '', 0, 1, '', 0),
(104, 61, 'link', '', 'category', '4', '', '', '', '', 0, 1, '_self', 1),
(105, 61, 'link', '', 'cms', '3', '', '', '', '', 0, 1, '_self', 2),
(106, 61, 'link', '', 'cms', '5', '', '', '', '', 0, 1, '_self', 3),
(107, 61, 'link', '', 'category', '4', '', '', '', '', 0, 1, '_self', 4),
(108, 62, 'link', '', 'category', '5', '', '', '', '', 0, 1, '_self', 1),
(109, 62, 'link', '', 'cms', '3', '', '', '', '', 0, 1, '_self', 2),
(110, 62, 'link', '', 'cms', '5', '', '', '', '', 0, 1, '_self', 3),
(111, 62, 'link', '', 'category', '4', '', '', '', '', 0, 1, '_self', 4),
(115, 63, 'link', '', 'cms', '5', '', '', '', '', 0, 1, '_self', 1),
(116, 63, 'link', '', 'category', '4', '', '', '', '', 0, 1, '_self', 2),
(117, 63, 'link', '', 'category', '5', '', '', '', '', 0, 1, '_self', 3),
(118, 63, 'link', '', 'category', '5', '', '', '', '', 0, 1, '_self', 4),
(119, 63, 'link', '', 'category', '4', '', '', '', '', 0, 1, '_self', 5),
(120, 61, 'link', '', 'cms', '2', '', '', '', '', 0, 1, '_self', 5),
(128, 64, 'custom_html', '', '', '', '', '', '', '', 0, 0, '', 0),
(129, 65, 'custom_html', '', '', '', '', '', '', '', 0, 0, '', 0),
(130, 66, 'custom_html', '', '', '', '', '', '', '', 0, 0, '', 0),
(131, 67, 'module', '', '', '', 'blocknewsletter', 'displayfooter', '', '', 0, 0, '', 1),
(132, 67, 'custom_html', '', '', '', '', '', '', '', 0, 0, '', 0),
(133, 62, 'link', '', 'cms', '2', '', '', '', '', 0, 1, '', 0);

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_item_lang` (
  `id_loffc_block_item` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id_loffc_block_item`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `_DB_PREFIX_loffc_block_item_lang` (`id_loffc_block_item`, `id_lang`, `title`, `text`) VALUES
(0, 1, 'item 1', ''),
(0, 2, 'item 1', ''),
(0, 3, 'item 1', ''),
(0, 4, 'item 1', ''),
(0, 5, 'item 1', ''),
(0, 6, 'item 1', ''),
(99, 1, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 2, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 3, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 4, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 5, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 6, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 7, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 8, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(99, 9, 'About us', '<p class=\"text_abount_ft\">Lorem Ipsum is simply dummy text of and typesetting industr</p>\r\n<p class=\"leo-telephone\">Phone: +01 888 (000) 1234</p>\r\n<p class=\"leo-fax\">Fax: +01 888 (000) 1234</p>\r\n<p class=\"leo-mail\">Email: <a href=\"mailto:test@test.com\">test@test.com</a></p>'),
(100, 1, 'Phasellus purus', ''),
(100, 2, 'Phasellus purus', ''),
(100, 3, 'Phasellus purus', ''),
(100, 4, 'Phasellus purus', ''),
(100, 5, 'Phasellus purus', ''),
(100, 6, 'Phasellus purus', ''),
(100, 7, 'Phasellus purus', ''),
(100, 8, 'Phasellus purus', ''),
(100, 9, 'Phasellus purus', ''),
(101, 1, 'Laoreet sed', ''),
(101, 2, 'Laoreet sed', ''),
(101, 3, 'Laoreet sed', ''),
(101, 4, 'Laoreet sed', ''),
(101, 5, 'Laoreet sed', ''),
(101, 6, 'Laoreet sed', ''),
(101, 7, 'Laoreet sed', ''),
(101, 8, 'Laoreet sed', ''),
(101, 9, 'Laoreet sed', ''),
(102, 1, 'Morbi odio', ''),
(102, 2, 'Morbi odio', ''),
(102, 3, 'Morbi odio', ''),
(102, 4, 'Morbi odio', ''),
(102, 5, 'Morbi odio', ''),
(102, 6, 'Morbi odio', ''),
(102, 7, 'Morbi odio', ''),
(102, 8, 'Morbi odio', ''),
(102, 9, 'Morbi odio', ''),
(104, 1, 'Elementum faucibus', ''),
(104, 2, 'Elementum faucibus', ''),
(104, 3, 'Elementum faucibus', ''),
(104, 4, 'Elementum faucibus', ''),
(104, 5, 'Elementum faucibus', ''),
(104, 6, 'Elementum faucibus', ''),
(104, 7, 'Elementum faucibus', ''),
(104, 8, 'Elementum faucibus', ''),
(104, 9, 'Elementum faucibus', ''),
(105, 1, 'Tristique turpis', ''),
(105, 2, 'Tristique turpis', ''),
(105, 3, 'Tristique turpis', ''),
(105, 4, 'Tristique turpis', ''),
(105, 5, 'Tristique turpis', ''),
(105, 6, 'Tristique turpis', ''),
(105, 7, 'Tristique turpis', ''),
(105, 8, 'Tristique turpis', ''),
(105, 9, 'Tristique turpis', ''),
(106, 1, 'Nulla quam', ''),
(106, 2, 'Nulla quam', ''),
(106, 3, 'Nulla quam', ''),
(106, 4, 'Nulla quam', ''),
(106, 5, 'Nulla quam', ''),
(106, 6, 'Nulla quam', ''),
(106, 7, 'Nulla quam', ''),
(106, 8, 'Nulla quam', ''),
(106, 9, 'Nulla quam', ''),
(107, 1, 'Laoreet sed', ''),
(107, 2, 'Laoreet sed', ''),
(107, 3, 'Laoreet sed', ''),
(107, 4, 'Laoreet sed', ''),
(107, 5, 'Laoreet sed', ''),
(107, 6, 'Laoreet sed', ''),
(107, 7, 'Laoreet sed', ''),
(107, 8, 'Laoreet sed', ''),
(107, 9, 'Laoreet sed', ''),
(108, 1, 'Tristique turpis', ''),
(108, 2, 'Tristique turpis', ''),
(108, 3, 'Tristique turpis', ''),
(108, 4, 'Tristique turpis', ''),
(108, 5, 'Tristique turpis', ''),
(108, 6, 'Tristique turpis', ''),
(108, 7, 'Tristique turpis', ''),
(108, 8, 'Tristique turpis', ''),
(108, 9, 'Tristique turpis', ''),
(109, 1, 'Morbi odio', ''),
(109, 2, 'Morbi odio', ''),
(109, 3, 'Morbi odio', ''),
(109, 4, 'Morbi odio', ''),
(109, 5, 'Morbi odio', ''),
(109, 6, 'Morbi odio', ''),
(109, 7, 'Morbi odio', ''),
(109, 8, 'Morbi odio', ''),
(109, 9, 'Morbi odio', ''),
(110, 1, 'Amet ibendum', ''),
(110, 2, 'Amet ibendum', ''),
(110, 3, 'Amet ibendum', ''),
(110, 4, 'Amet ibendum', ''),
(110, 5, 'Amet ibendum', ''),
(110, 6, 'Amet ibendum', ''),
(110, 7, 'Amet ibendum', ''),
(110, 8, 'Amet ibendum', ''),
(110, 9, 'Amet ibendum', ''),
(111, 1, 'Laoreet sed', ''),
(111, 2, 'Laoreet sed', ''),
(111, 3, 'Laoreet sed', ''),
(111, 4, 'Laoreet sed', ''),
(111, 5, 'Laoreet sed', ''),
(111, 6, 'Laoreet sed', ''),
(111, 7, 'Laoreet sed', ''),
(111, 8, 'Laoreet sed', ''),
(111, 9, 'Laoreet sed', ''),
(115, 1, 'Phasellus purus', ''),
(115, 2, 'Phasellus purus', ''),
(115, 3, 'Phasellus purus', ''),
(115, 4, 'Phasellus purus', ''),
(115, 5, 'Phasellus purus', ''),
(115, 6, 'Phasellus purus', ''),
(115, 7, 'Phasellus purus', ''),
(115, 8, 'Phasellus purus', ''),
(115, 9, 'Phasellus purus', ''),
(116, 1, 'Elementum faucibus', ''),
(116, 2, 'Elementum faucibus', ''),
(116, 3, 'Elementum faucibus', ''),
(116, 4, 'Elementum faucibus', ''),
(116, 5, 'Elementum faucibus', ''),
(116, 6, 'Elementum faucibus', ''),
(116, 7, 'Elementum faucibus', ''),
(116, 8, 'Elementum faucibus', ''),
(116, 9, 'Elementum faucibus', ''),
(117, 1, 'Nulla quam', ''),
(117, 2, 'Nulla quam', ''),
(117, 3, 'Nulla quam', ''),
(117, 4, 'Nulla quam', ''),
(117, 5, 'Nulla quam', ''),
(117, 6, 'Nulla quam', ''),
(117, 7, 'Nulla quam', ''),
(117, 8, 'Nulla quam', ''),
(117, 9, 'Nulla quam', ''),
(118, 1, 'Amet ibendum', ''),
(118, 2, 'Amet ibendum', ''),
(118, 3, 'Amet ibendum', ''),
(118, 4, 'Amet ibendum', ''),
(118, 5, 'Amet ibendum', ''),
(118, 6, 'Amet ibendum', ''),
(118, 7, 'Amet ibendum', ''),
(118, 8, 'Amet ibendum', ''),
(118, 9, 'Amet ibendum', ''),
(119, 1, 'Laoreet sed', ''),
(119, 2, 'Laoreet sed', ''),
(119, 3, 'Laoreet sed', ''),
(119, 4, 'Laoreet sed', ''),
(119, 5, 'Laoreet sed', ''),
(119, 6, 'Laoreet sed', ''),
(119, 7, 'Laoreet sed', ''),
(119, 8, 'Laoreet sed', ''),
(119, 9, 'Laoreet sed', ''),
(120, 1, 'Phasellus purus', ''),
(120, 2, 'Phasellus purus', ''),
(120, 3, 'Phasellus purus', ''),
(120, 4, 'Phasellus purus', ''),
(120, 5, 'Phasellus purus', ''),
(120, 6, 'Phasellus purus', ''),
(120, 7, 'Phasellus purus', ''),
(120, 8, 'Phasellus purus', ''),
(120, 9, 'Phasellus purus', ''),
(128, 1, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 2, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 3, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 4, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 5, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 6, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 7, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 8, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(128, 9, 'Payment Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_payment.png\" alt=\"\" /></a></p>'),
(129, 1, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 2, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 3, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 4, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 5, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 6, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 7, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 8, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(129, 9, 'Shipping Methods', '<p><a><img src=\"modules/lofadvancecustom/images/img_ship.png\" alt=\"\" /></a></p>'),
(130, 1, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 2, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 3, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 4, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 5, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 6, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 7, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 8, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(130, 9, 'Customer Care', '<p class=\"add_phone\">+00 123 456 789</p>\r\n<p>Lorem psum dolor sit amet consec.</p>'),
(131, 1, 'Newsletter', ''),
(131, 2, 'Newsletter', ''),
(131, 3, 'Newsletter', ''),
(131, 4, 'Newsletter', ''),
(131, 5, 'Newsletter', ''),
(131, 6, 'Newsletter', ''),
(131, 7, 'Newsletter', ''),
(131, 8, 'Newsletter', ''),
(131, 9, 'Newsletter', ''),
(132, 1, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 2, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 3, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 4, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 5, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 6, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 7, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 8, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(132, 9, 'Sign Up for Our Newsletter:', '<p>Sign Up for Our Newsletter:</p>'),
(133, 1, 'Amet ibendum', ''),
(133, 2, 'Amet ibendum', ''),
(133, 3, 'Amet ibendum', ''),
(133, 4, 'Amet ibendum', ''),
(133, 5, 'Amet ibendum', ''),
(133, 6, 'Amet ibendum', ''),
(133, 7, 'Amet ibendum', ''),
(133, 8, 'Amet ibendum', ''),
(133, 9, 'Amet ibendum', '');


CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_item_shop` (
  `id_loffc_block_item` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_loffc_block_item`,`id_shop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `_DB_PREFIX_loffc_block_item_shop` (`id_loffc_block_item`, `id_shop`) VALUES
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1);

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_lang` (
  `id_loffc_block` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id_loffc_block`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `_DB_PREFIX_loffc_block_lang` (`id_loffc_block`, `id_lang`, `title`) VALUES
(60, 1, 'About store'),
(60, 2, 'Über Speicher'),
(60, 3, 'Au sujet du magasin'),
(60, 4, 'About store'),
(60, 5, 'circa il deposito'),
(60, 6, 'Acerca de la tienda'),
(60, 7, 'О магазине'),
(60, 8, 'O sklepie'),
(60, 9, 'About store'),
(61, 1, 'Latest new'),
(61, 2, 'Neueste neuen'),
(61, 3, 'dernières nouvelles'),
(61, 4, 'Latest new'),
(61, 5, 'Ultima nuova'),
(61, 6, 'La última nueva'),
(61, 7, 'Последние новые'),
(61, 8, 'Ostatnia nowa'),
(61, 9, 'Latest new'),
(62, 1, 'Information'),
(62, 2, 'Information'),
(62, 3, 'informations'),
(62, 4, 'Information'),
(62, 5, 'informazioni'),
(62, 6, 'información'),
(62, 7, 'информация'),
(62, 8, 'Informacja'),
(62, 9, 'Information'),
(63, 1, 'New promotion'),
(63, 2, 'Neue Förderung'),
(63, 3, 'nouvelle promotion'),
(63, 4, 'New promotion'),
(63, 5, 'nuova promozione'),
(63, 6, 'Nueva promoción'),
(63, 7, 'Новая акция'),
(63, 8, 'Nowa promocja'),
(63, 9, 'New promotion'),
(64, 1, 'Payment Methods'),
(64, 2, 'Zahlungsmethoden'),
(64, 3, 'Modes de paiement'),
(64, 4, 'Payment Methods'),
(64, 5, 'Metodi di pagamento'),
(64, 6, 'Formas de pago'),
(64, 7, 'Способы оплаты'),
(64, 8, 'Formy płatności'),
(64, 9, 'Payment Methods'),
(65, 1, 'Shipping Methods'),
(65, 2, 'Verschiffen-Methoden'),
(65, 3, 'Méthodes dexpédition'),
(65, 4, 'Shipping Methods'),
(65, 5, 'Metodi di spedizione'),
(65, 6, 'Métodos de Envío'),
(65, 7, 'Доставка методы'),
(65, 8, 'Metody wysyłki'),
(65, 9, 'Shipping Methods'),
(66, 1, 'Customer Care'),
(66, 2, 'Customer Care'),
(66, 3, 'Service à la clientèle'),
(66, 4, 'Customer Care'),
(66, 5, 'Customer Care'),
(66, 6, 'Atención al Cliente'),
(66, 7, 'Customer Care'),
(66, 8, 'Customer Care'),
(66, 9, 'Customer Care'),
(67, 1, 'Newsletter'),
(67, 2, 'Customer Care'),
(67, 3, 'Lettre dinformation'),
(67, 4, 'Newsletter'),
(67, 5, 'Newsletter'),
(67, 6, 'hoja informativa'),
(67, 7, 'информационный бюллетень'),
(67, 8, 'Customer Care'),
(67, 9, 'Newsletter');

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_loffc_block_shop` (
  `id_loffc_block` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  PRIMARY KEY (`id_loffc_block`,`id_shop`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `_DB_PREFIX_loffc_block_shop` (`id_loffc_block`, `id_shop`) VALUES
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1);

";
?>
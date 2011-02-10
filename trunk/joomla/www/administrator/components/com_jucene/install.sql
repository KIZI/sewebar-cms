DROP TABLE IF EXISTS `#__jucene_fields`;

CREATE TABLE IF NOT EXISTS `#__jucene_fields` (
  `id` int(30) NOT NULL auto_increment,
  `fieldname` varchar(100) collate utf8_czech_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#__jucene_synchronyze`;

CREATE TABLE IF NOT EXISTS `#__jucene_synchronyze` (
  `id` int(8) NOT NULL auto_increment,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `hash` varchar(300) collate utf8_czech_ci NOT NULL,
  `id_article` int(8) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;
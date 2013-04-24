DROP TABLE IF EXISTS `#__dbconnect_tables`;
CREATE TABLE `#__dbconnect_tables` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL COMMENT 'ID uživatele, kterému patří daný záznam',
  `db_type` varchar(20) NOT NULL,
  `server` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `db_name` varchar(50) NOT NULL,
  `table` varchar(80) NOT NULL,
  `primary_key` varchar(50) NOT NULL,
  `shared` tinyint(1) NOT NULL COMMENT '1 = sdílené s ostatními uživateli',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='Tabulka obsahující informace o možných připojitelných databá';


DROP TABLE IF EXISTS `#__dbconnect_task_table_content`;
CREATE TABLE `#__dbconnect_task_table_content` (
  `id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabulka obsahující předgenerovaný obsah tabulek pro jednotli';


DROP TABLE IF EXISTS `#__dbconnect_tasks`;
CREATE TABLE `#__dbconnect_tasks` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL COMMENT 'ID uživatele, kterému patří daný záznam',
  `name` varchar(60) NOT NULL,
  `db_table` int(11) NOT NULL COMMENT 'ID databázového připojení z dbconnect_tables',
  `columns` text NOT NULL COMMENT 'Informace o sloupcích ve formátu XML',
  `bkef_article` int(11) NOT NULL COMMENT 'ID článku, ve kterém je uložen BKEF',
  `fml_article` int(11) NOT NULL COMMENT 'ID článku, ve kterém je uložen FML',
  `kbi_source` int(11) NOT NULL COMMENT 'ID KBI zdroje z tabulky #__kbi_sources',
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='Tabulka obsahující definici zadání DM úloh';

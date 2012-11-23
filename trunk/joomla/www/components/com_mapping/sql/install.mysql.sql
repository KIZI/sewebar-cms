/*tabulka pro zaznamenávání zkušeností*/
CREATE TABLE IF NOT EXISTS `#__mapping_expirience` (
  `name1` varchar(100) NOT NULL,
  `name2` varchar(100) NOT NULL,
  `datatype1` varchar(50) NOT NULL,
  `datatype2` varchar(50) NOT NULL,
  `ratio` float NOT NULL,
  PRIMARY KEY  (`name1`,`name2`,`datatype1`,`datatype2`),
  KEY `namesKey` (`name1`,`name2`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*tabulka pro nastavení*/
CREATE TABLE IF NOT EXISTS `#__mapping_config` (
  `group` enum('constant','assignClass','valuesAssignClass','matchRate') NOT NULL COMMENT 'Typ vloženého obsahu',
  `name` varchar(150) NOT NULL,
  `value` varchar(150) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`group`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabulka obsahující nastavení pro com_mapping';

/*hodnoty pro výchozí součásti komponenty*/
INSERT INTO `#__mapping_config` (`group`, `name`, `value`, `description`) VALUES
('assignClass', 'ManualAssignClass', '1', 'MANUAL_ASSIGN'),
('assignClass', 'OptimizedAssignClass', '2', 'OPTIMIZED_ASSIGN'),
('matchRate', 'MATCH_NAMES_RATE', '1', 'Podobnost jmen - na základě similar_text'),
('matchRate', 'MATCH_DATATYPE_RATE', '1', 'Podobnost na základě tripletů, event. shody(překryvu) u číselných sloupců'),
('matchRate', 'MATCH_EXPIRIENCE_RATE', '1', 'Podobnost na základě předchozích zkušeností'),
('constant', 'EXPIRIENCE_USER_RATIO', '1', 'Ohodnocení uživatelsky schválené podobnosti pro ExpirienceClass'),
('constant', 'EXPIRIENCE_AUTO_RATIO', '0.3', 'Ohodnocení automatického namapování pro ExpirienceClass'),
('valuesAssignClass', 'IdenticalValuesAssignClass', '2', 'IDENTICAL_VALUES_ASSIGN'),
('valuesAssignClass', 'NgramsValuesAssignClass', '1', 'NGRAMS_VALUES_ASSIGN'),
('constant', 'COLUMN_XPLET_LENGTH', '3', 'Délka "xpletů", na které se dělí výčtové položky při porovnávání sloupců - tj. triplety, diplety atp.'),
('valuesAssignClass', 'SimilarityValuesAssignClass', '1', 'SIMILARITY_VALUES_ASSIGN'),
('valuesAssignClass', 'ManualValuesAssignClass', '1', 'MANUAL_VALUES_ASSIGN'),
('constant', 'VALUES_MAPPING_NGRAMS_LENGTH', '2', 'Délka n-gramů používaných u mapování hodnot.'),
('assignClass', 'MaxAssignClass', '1', 'MAX_ASSIGN'),
('assignClass', 'GlobalMaxAssignClass', '1', 'GLOBAL_MAX_ASSIGN'),
('constant', 'OPTIMIZED_IGNORE_MERGE', '0.1', 'rozdíl mezi maximální shodou a minimální uvažovanou'),
('constant', 'OPTIMIZED_BOTTOM_MERGE_STEP', '0.3', 'rozdíl mezi maximální shodou a minimální uvažovanou'),
('constant', 'OPTIMIZED_MAX_ARR_VALUES', '2', 'hranice pro nej shodované hodnoty - počet položek pole'),
('constant', 'MINUS_INFINITE', '-999999999', 'Hodnota záporného nekonečna použitá u hranic intervalů'),
('constant', 'PLUS_INFINITE', '999999999', 'Hodnota kladného nekonečna použitá u hranic intervalů');
/*TODO*/
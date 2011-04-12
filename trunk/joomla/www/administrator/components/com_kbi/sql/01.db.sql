/* Sources */
DROP TABLE IF EXISTS `#__kbi_sources`;
CREATE TABLE `#__kbi_sources` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR( 255 ) NOT NULL ,
	PRIMARY KEY ( `id` )
);

ALTER TABLE `#__kbi_sources` ADD COLUMN `url` VARCHAR( 255 );
ALTER TABLE `#__kbi_sources` ADD COLUMN `ontology` VARCHAR( 255 );
ALTER TABLE `#__kbi_sources` ADD COLUMN `type` VARCHAR( 31 );
ALTER TABLE `#__kbi_sources` ADD COLUMN `method` VARCHAR( 15 );
ALTER TABLE `#__kbi_sources` ADD COLUMN `params` TEXT;
ALTER TABLE `#__kbi_sources` ADD COLUMN `dictionaryquery` TEXT;

/* Queries */
DROP TABLE IF EXISTS `#__kbi_queries`;
CREATE TABLE `#__kbi_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `query` text,
  `delimiter` varchar(31) DEFAULT NULL,
  `dictionaryquery` text,
  `dictionaryqueryxsl` text,
  `featurelist` text,
  PRIMARY KEY (`id`)
);

ALTER TABLE `#__kbi_queries` ADD COLUMN `paramsxsl` TEXT;

/* XSLTs */
DROP TABLE IF EXISTS `#__kbi_xslts`;
CREATE TABLE `#__kbi_xslts` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR( 255 ) NOT NULL ,
	PRIMARY KEY ( `id` )
);

ALTER TABLE `#__kbi_xslts` ADD COLUMN `style` TEXT;

/* Queries */
DROP TABLE IF EXISTS `#__kbi_querydefinitions`;
CREATE TABLE `#__kbi_querydefinitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR( 255 ) NOT NULL ,
  `source` text,
  `query` text,
  `xslt` text,
  `icon` VARCHAR( 255 ) NOT NULL ,
  PRIMARY KEY (`id`)
);
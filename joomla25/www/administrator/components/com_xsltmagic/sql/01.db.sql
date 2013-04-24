/* XSLMagic */
DROP TABLE IF EXISTS `#__xslt_magic`;
CREATE TABLE `#__xslt_magic` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `rule` text,
  `source` text,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);


INSERT INTO `#__xslt_magic` (`id`, `name`, `rule`, `source`, `modified`)
VALUES
 (1, 'pravidlo #1', 'contains(/*[1], ''PMML'')', '/4FTPMML2HTML.xsl', '2011-06-01 16:33:48'),
(2, 'pravidlo #2', 'contains(name(/*[1]), ''BKEF'')', '/bkef.xsl', '2011-06-01 16:33:48'),
(3, 'pravidlo #3', 'contains(name(/*[1]/*[4]/@numberOfCategories),"numberOfCategories")', '/4FTPMML2HTML.xsl', '2011-06-02 08:15:26');
                            
INSERT INTO `jos_kbi_queries` (`id`,`name`,`query`)
VALUES
	(1,'dotaz 1','getTopicInstanceForString($String,$TopicType, $Topic):-value ($Object,$String),topic-name($Topic,$Object),instance-of($Topic,$TopicType).getTopicInstanceForString($String,$TopicType,$Topic)?'),
	(2,'gg','ddd'),
	(3,'SPARQL test 1','SELECT ?s ?p ?o WHERE {\r\n  ?s ?p ?o .\r\n}\r\nLIMIT 10');


INSERT INTO `jos_kbi_sources` (`id`,`name`,`url`,`ontology`)
VALUES
	(1,'Ontopia - localhost','http://localhost:8080/tmrap/tmrap/get-tolog','ar-ontology.xtm'),
	(4,'sparqlbot.semsol.org','http://sparqlbot.semsol.org/','');

INSERT INTO `jos_kbi_xslts` (`id`,`name`,`style`)
VALUES
	(1,'style 1','<?xml version=\"1.0\"?>\r\n\r\n<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\r\n\r\n<xsl:template match=\"/\">\r\n	<html>\r\n		<body>\r\n		50. <xsl:value-of select=\"//row[50]/value\"/><br />\r\n		</body>\r\n	</html>\r\n</xsl:template>\r\n</xsl:stylesheet>'),
	(2,'janko hrasko style','<?xml version=\"1.0\"?>\r\n\r\n<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\r\n\r\n<xsl:template match=\"/\">\r\n	<html>\r\n		<body>\r\n			<xsl:for-each select=\"//row\">\r\n				<xsl:value-of select=\"value\"/><br />\r\n			</xsl:for-each>\r\n		</body>\r\n	</html>\r\n</xsl:template>\r\n</xsl:stylesheet>'),
	(3,'tabulka','<?xml version=\"1.0\"?>\r\n\r\n<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\r\n\r\n<xsl:template match=\"/\">\r\n	<table>\r\n	  <xsl:for-each select=\"//row\">\r\n	    <tr><td><xsl:value-of select=\"value\"/></td></tr>\r\n	  </xsl:for-each>\r\n	</table>\r\n</xsl:template>\r\n</xsl:stylesheet>'),
	(4,'SPARQL test 1','<?xml version=\"1.0\"?>\r\n\r\n<xsl:stylesheet version=\"1.0\" xmlns:xsl=\"http://www.w3.org/1999/XSL/Transform\">\r\n\r\n<xsl:template match=\"/\">\r\n	<h1><xsl:value-of select=\"/sparql/results/result[3]/binding[2]/literal\"></xsl:value-of></h1>\r\n</xsl:template>\r\n</xsl:stylesheet>');
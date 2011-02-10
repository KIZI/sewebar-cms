<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:s="http://www.w3.org/2005/sparql-results#">

<xsl:template match="/">
		<ul>
			<xsl:for-each select="/s:sparql/s:results/s:result/s:binding[@name='o']">
				<li><xsl:value-of select="s:literal"/></li>
			</xsl:for-each>
		</ul>
</xsl:template>
</xsl:stylesheet>
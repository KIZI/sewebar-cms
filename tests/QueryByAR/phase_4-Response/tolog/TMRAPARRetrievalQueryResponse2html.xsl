<?xml version="1.0" encoding="UTF-8"?>
<!-- Transformation for presenting the result of the OKS query -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xd="http://www.oxygenxml.com/ns/doc/xsl" version="1.0"
    xmlns:tm="http://psi.ontopia.net/xml/tm-xml/"
    xmlns:www.dmg.org="http://www.dmg.org/PMML-4_0#"
    >
    <xsl:template match="www.dmg.org:associationrule">
        <h2>List of discovered association rules matching the query</h2>
        <ol>
        <xsl:for-each select=".//tm:value">
            <li><xsl:value-of select="."/></li>
        </xsl:for-each>
        </ol>
    </xsl:template>


</xsl:stylesheet>

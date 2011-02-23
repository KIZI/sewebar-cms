<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="1.0"
    xmlns:pmml="http://www.dmg.org/PMML-3_0"
     exclude-result-prefixes="pmml"
    >
    <xsl:template match="/">
        <xsl:apply-templates select="//pmml:AssociationRule"/>
    </xsl:template>
    <xsl:template match="//pmml:AssociationRule">
        <AssociationRule id="{@id}" antecedent="{@antecedent}" consequent="{@consequent}" >
            <IMValue name="Support"><xsl:value-of select="@support"/></IMValue>
            <IMValue name="Confidence"><xsl:value-of select="@confidence"/></IMValue>
            <IMValue name="Lift"><xsl:value-of select="@lift"/></IMValue>
        </AssociationRule>                
    </xsl:template>
</xsl:stylesheet>

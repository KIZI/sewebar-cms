<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:p="http://www.dmg.org/PMML-4_0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:keg="http://keg.vse.cz" xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1"
    extension-element-prefixes="func exsl"
    exclude-result-prefixes="p xsi keg guha">
    
    <xsl:output method="xml" encoding="UTF-8" indent="yes"/>
    <xsl:strip-space elements="*"/>
    
    <xsl:template match="AssociationRules">
        <xsl:element name="PMML">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="p:Header"/>
    <xsl:template match="p:DataDictionary"/>
    <xsl:template match="p:TransformationDictionary"/>
    <xsl:template match="TaskSetting"/>
    <xsl:template match="BBA"/>
    <xsl:template match="DBA"/>
    
    <xsl:template match="AssociationRules/AssociationRule" name="AssociationRule">
        <xsl:element name="AssociationRule">
            <xsl:variable name="ante" select="@antecedent"/>
            <xsl:variable name="cons" select="@consequent"/>
            <xsl:variable name="cond" select="@condition"/>
            <Text><xsl:value-of select="Text"/></Text>
            <xsl:if test="count($ante)>0">
                <Antecedent>
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent"><xsl:value-of select="$ante"/></xsl:with-param>
                    </xsl:call-template>
                </Antecedent>
            </xsl:if>
            <xsl:if test="count($cons)>0">
                <Consequent>
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent"><xsl:value-of select="$cons"/></xsl:with-param>
                    </xsl:call-template>
                </Consequent>
            </xsl:if>
            <xsl:if test="count($cond)>0">
                <Condition>
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent"><xsl:value-of select="$cond"/></xsl:with-param>
                    </xsl:call-template>
                </Condition>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template name="rekurze">
        <xsl:param name="cedent"/>
        <xsl:param name="connective"/>
        <xsl:choose>
            <xsl:when test="count(//DBA[@id = $cedent]) > 0">
                <xsl:for-each select="//DBA[@id = $cedent]/BARef">
                    <xsl:variable name="odkaz"><xsl:value-of select="text()"/></xsl:variable>
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent"><xsl:value-of select="$odkaz"/></xsl:with-param>
                        <xsl:with-param name="connective"><xsl:value-of select="//DBA[@id = $cedent]/@connective"/></xsl:with-param>
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="BBA">
                    <xsl:variable name="FieldRef"><xsl:value-of select="//BBA[@id = $cedent]/FieldRef"/></xsl:variable>
                    <xsl:variable name="CatRef"><xsl:value-of select="//BBA[@id = $cedent]/CatRef"/></xsl:variable>
                    <xsl:attribute name="id"><xsl:value-of select="//BBA[@id = $cedent]/@id"/></xsl:attribute>
                    <xsl:attribute name="connective"><xsl:value-of select="$connective"/></xsl:attribute>
                    <xsl:element name="FieldRef"><xsl:value-of select="$FieldRef"/></xsl:element>
                    <xsl:element name="CatRef"><xsl:value-of select="$CatRef"/></xsl:element>
                    <xsl:if test="count(//p:DerivedField[@name = $FieldRef and @optype = 'continuous']) > 0">
                        <xsl:element name="leftbound"><xsl:value-of select="//p:DerivedField[@name = $FieldRef and @optype = 'continuous']//p:DiscretizeBin[@binValue = $CatRef]//p:Interval[1]/@leftMargin"/></xsl:element>
                        <xsl:element name="rightbound"><xsl:value-of select="//p:DerivedField[@name = $FieldRef and @optype = 'continuous']//p:DiscretizeBin[@binValue = $CatRef]//p:Interval[last()]/@rightMargin"/></xsl:element>
                    </xsl:if>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
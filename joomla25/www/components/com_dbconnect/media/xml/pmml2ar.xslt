<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:p="http://www.dmg.org/PMML-4_0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:keg="http://keg.vse.cz" xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1"
    extension-element-prefixes="func exsl"
    exclude-result-prefixes="p xsi keg guha">
    
    <xsl:output encoding="UTF-8" method="xml" indent="yes" />
    
    <xsl:template match="/">
            <!--<xsl:apply-templates select="./p:PMML/p:TransformationDictionary"></xsl:apply-templates>-->
            <xsl:apply-templates select="//guha:AssociationModel/AssociationRules" />
        
    </xsl:template>

    <xsl:template match="guha:AssociationModel/AssociationRules">
        <AssociationRules>
            <xsl:apply-templates select="AssociationRule" /><!--TODO výběr jen vybraných pravidel pomocí atributu! -->
        </AssociationRules>
    </xsl:template>
    
    <xsl:template match="AssociationRule">
        <AssociationRule>
            <Text>
                <xsl:value-of select="Text"/>
            </Text>
            <Antecedent>
                <xsl:apply-templates select="../DBA[@id=current()/@antecedent]" />
            </Antecedent>
            <Consequent>
                <xsl:apply-templates select="../DBA[@id=current()/@consequent]" />
            </Consequent>
        </AssociationRule>
    </xsl:template>
    
    <xsl:template match="guha:AssociationModel/AssociationRules/DBA">
        <xsl:choose>
            <xsl:when test="count(current()/BARef)>1">
                <Cedent> 
                    <xsl:attribute name="connective"><xsl:value-of select="current()/@connective"/></xsl:attribute>
                    <xsl:apply-templates select="../DBA[@id=current()/BARef/text()]"></xsl:apply-templates>
                    <xsl:apply-templates select="../BBA[@id=current()/BARef/text()]"></xsl:apply-templates>
                </Cedent>    
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="../DBA[@id=current()/BARef/text()]"></xsl:apply-templates>
                <xsl:apply-templates select="../BBA[@id=current()/BARef/text()]"></xsl:apply-templates>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="guha:AssociationModel/AssociationRules/BBA">
        <Attribute>
            <FieldRef>
                <Name>
                    <xsl:value-of select="current()/FieldRef/text()"/>
                </Name>
                <Identity>
                    <xsl:apply-templates select="/p:PMML/p:TransformationDictionary/p:DerivedField[@name=current()/FieldRef/text()]" mode="fieldName"></xsl:apply-templates>
                </Identity>
            </FieldRef>
            <CatRef>
                <Name>
                    <xsl:value-of select="./CatRef/text()"/>
                </Name>
                <Data>
                    <xsl:apply-templates select="/p:PMML/p:TransformationDictionary/p:DerivedField[@name=current()/FieldRef/text()]/p:Discretize/p:DiscretizeBin[@binValue=current()/CatRef/text()]" mode="data"></xsl:apply-templates>
                    <xsl:apply-templates select="/p:PMML/p:TransformationDictionary/p:DerivedField[@name=current()/FieldRef/text()]/p:MapValues/p:InlineTable/p:row/p:field[text()=current()/CatRef/text()]" mode="data"></xsl:apply-templates>
                </Data>
            </CatRef>
        </Attribute>
    </xsl:template>
    
    <xsl:template match="p:DiscretizeBin" mode="data">
        <xsl:apply-templates select="current()/p:Interval"></xsl:apply-templates>
    </xsl:template>
    <xsl:template match="p:InlineTable/p:row/p:field" mode="data">
        <Value>
            <xsl:value-of select="parent::p:row/p:column"/>
        </Value>
    </xsl:template>
    <xsl:template match="p:Interval">
        <Interval>
            <xsl:attribute name="closure"><xsl:value-of select="@closure"/></xsl:attribute>
            <xsl:attribute name="leftMargin"><xsl:value-of select="@leftMargin"/></xsl:attribute>
            <xsl:attribute name="rightMargin"><xsl:value-of select="@rightMargin"/></xsl:attribute>
        </Interval>
    </xsl:template>
    
    <xsl:template match="p:DerivedField" mode="fieldName">
        <xsl:apply-templates select="current()/*" mode="fieldName"></xsl:apply-templates>
    </xsl:template>
    <xsl:template match="p:Discretize" mode="fieldName">
        <xsl:value-of select="@field"/>
    </xsl:template>
    <xsl:template match="p:MapValues" mode="fieldName">
        <xsl:value-of select="current()/p:FieldColumnPair/@field"/>
    </xsl:template>
    
    <xsl:template match="p:TransformationDictionary/p:DerivedField">
        <name><xsl:value-of select="@name"/></name>
    </xsl:template>
    
</xsl:stylesheet>
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    
    <xsl:output encoding="UTF-8" method="xml" indent="no" xml:space="default"/>
    
    <xsl:template match="/">
        <Rules>
            <xsl:apply-templates select="//AssociationRule" />
        </Rules>
    </xsl:template>

    <xsl:template match="AssociationRule">
        <Rule>
            <Text><xsl:value-of select="./Text"/></Text>
            <Condition>
                <xsl:apply-templates select="./Antecedent" />
            </Condition>
            <Execute>
                DrlResult.fireRule(%PROJECTID%,%RULEID%);
            </Execute>
        </Rule>
    </xsl:template>
    
    <xsl:template match="Antecedent">
        <xsl:apply-templates select="./Cedent" />
    </xsl:template>
    
    <xsl:template match="Cedent">
        <xsl:choose>
            <xsl:when test="count(./Attribute)>0">
                <!--jde o cedent složený z konkrétních atributů -->
                <xsl:for-each select="./Attribute">
                    <xsl:apply-templates select="." />
                    <xsl:if test="position() != last()">
                        <xsl:choose>
                            <xsl:when test="parent::node()/@connective='Conjunction'"> and </xsl:when>
                            <xsl:when test="parent::node()/@connective='Disjunction'"> or </xsl:when>
                        </xsl:choose>  
                    </xsl:if> 
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="count(./Cedent)>0">
                <!--jde o složený cedent-->
                <xsl:for-each select="./Cedent">
                    (<xsl:apply-templates select="." />)
                    <xsl:if test="position() != last()">
                        <xsl:choose>
                            <xsl:when test="parent::node()/@connective='Conjunction'"> and </xsl:when>
                            <xsl:when test="parent::node()/@connective='Disjunction'"> or </xsl:when>
                        </xsl:choose>
                    </xsl:if>    
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="Attribute" xml:space="preserve" >
        DrlObj (name == "<xsl:value-of select="./FieldRef/Name/text()"/>"<xsl:apply-templates select="./CatRef/Data/*" />)    
    </xsl:template>
    
    <xsl:template match="Data/Interval">
        , <xsl:choose>
            <xsl:when test="./@closure='closedClosed'">numVal &gt;= <xsl:value-of select="@leftMargin"/>, numVal &lt;= <xsl:value-of select="@rightMargin"/></xsl:when>
            <xsl:when test="./@closure='openClosed'">  numVal &gt; <xsl:value-of select="@leftMargin"/>, numVal &lt;= <xsl:value-of select="@rightMargin"/></xsl:when>
            <xsl:when test="./@closure='closedOpen'">  numVal &gt;= <xsl:value-of select="@leftMargin"/>, numVal &lt; <xsl:value-of select="@rightMargin"/></xsl:when>
            <xsl:when test="./@closure='openOpen'">    numVal &gt; <xsl:value-of select="@leftMargin"/>, numVal &lt; <xsl:value-of select="@rightMargin"/></xsl:when>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="Data/Value" xml:space="preserve">, value == "<xsl:value-of select="text()"/>"</xsl:template>
</xsl:stylesheet>
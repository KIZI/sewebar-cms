<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:arb="http://keg.vse.cz/ns/arbuilder0_1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:p="http://www.dmg.org/PMML-4_0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:keg="http://keg.vse.cz" xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1"
    extension-element-prefixes="func exsl"
    exclude-result-prefixes="p xsi keg guha">
    
    <xsl:output omit-xml-declaration="yes" method="xml" encoding="UTF-8" indent="yes"/>
    <xsl:strip-space elements="*"/>
    <xsl:variable name="debug"/> 
    
    <xsl:template match="/">
        <xsl:apply-templates select="arb:ARBuilder/ARQuery"/>
    </xsl:template>
    
     
    
    <xsl:template match="ARQuery">        
        <xsl:variable name="ante"><xsl:value-of select="AntecedentSetting"/></xsl:variable>
        <xsl:variable name="cons"><xsl:value-of select="ConsequentSetting"/></xsl:variable>
        <xsl:variable name="cond"><xsl:value-of select="ConditionSetting"/></xsl:variable>
        <xsl:variable name="antecedent">antecedent</xsl:variable>
        <xsl:variable name="consequent">consequent</xsl:variable>
        <xsl:variable name="condition">condition</xsl:variable>
<!--            <xsl:attribute name="id"><xsl:value-of select="position()"/></xsl:attribute>-->
<!--        <Text><xsl:value-of select="Text"/></Text>-->
            <xsl:if test="count($ante)>0">
<!--                <Antecedent>-->
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent" select="$ante"/>
                        <xsl:with-param name="rulePos" select="$antecedent"/>
                    </xsl:call-template>
<!--                </Antecedent>-->
            </xsl:if>
            <xsl:if test="count($cons)>0">
<!--                <Consequent>-->
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent" select="$cons"/>
                        <xsl:with-param name="rulePos" select="$consequent"/>
                    </xsl:call-template>
<!--                </Consequent>-->
            </xsl:if>
            <xsl:if test="count($cond)>0">
<!--                <Condition>-->
                    <xsl:call-template name="rekurze">
                        <xsl:with-param name="cedent" select="$cond"/>
                        <xsl:with-param name="rulePos" select="$condition"/>
                    </xsl:call-template>
<!--                </Condition>-->
            </xsl:if>
            <xsl:if test="count(IMValue) > 0">                
                <xsl:apply-templates select="IMValue"/>                
            </xsl:if>            
        
    </xsl:template>
    
    <xsl:template name="rekurze">
        <xsl:param name="cedent"/>
        <xsl:param name="rulePos"></xsl:param>
        <xsl:apply-templates select="DBASettings/DBASetting[@id = $cedent] | BBASettings/BBASetting[@id = $cedent]">
            <xsl:with-param name="step" select="1"/>
            <xsl:with-param name="rulePos" select="$rulePos"/>
        </xsl:apply-templates>
    </xsl:template>
    
    <xsl:template match="DBASetting">
        <xsl:param name="step"/>
        <xsl:param name="rulePos"></xsl:param>
        <xsl:if test="count(BASettingRef) > 0">
            <xsl:choose>
                <xsl:when test="$step = 1">
                    <xsl:apply-templates select="BASettingRef">
<!--                        <xsl:with-param name="connective" select="@connective"/>-->
                        <xsl:with-param name="rulePos" select="$rulePos"/>
                    </xsl:apply-templates>
                </xsl:when>
                <xsl:otherwise>
                    
                  <!--<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>-->
                  <xsl:apply-templates select="BASettingRef">
                      <xsl:with-param name="rulePos" select="$rulePos"/>
                  </xsl:apply-templates>                       
                    
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>
    </xsl:template>   
    
    <xsl:template match="BBASetting">  
        <xsl:param name="rulePos"/>        
        <xsl:param name="connective"/>
        <xsl:variable name="FieldRef"><xsl:value-of select="FieldRef"/></xsl:variable>
        <xsl:variable name="CatRef"><xsl:value-of select="CatRef"/></xsl:variable>
        <xsl:variable name="fieldQuery" select="translate(Text,'()',': ')"/>
        
<!--        <xsl:element name="BBA">-->
            <xsl:text> </xsl:text>
            <xsl:value-of select="$rulePos"/>
            <xsl:text>:</xsl:text>            
            <xsl:value-of select="FieldRef"/>
            <xsl:text> </xsl:text>
            <xsl:value-of select="FieldRef"/>
            <xsl:text>:</xsl:text>
        <xsl:for-each select="Coefficient/Category">
            <xsl:value-of select="."/>
            </xsl:for-each>
            <!--<xsl:if test="position()>0">
                <xsl:text> AND </xsl:text>
            </xsl:if>-->
<!--            <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>-->
            <!--<xsl:attribute name="connective"><xsl:value-of select="$connective"/></xsl:attribute>-->
<!--            <xsl:element name="Text">-->
            <xsl:value-of select="fieldQuery"/>
            <xsl:text> </xsl:text>
<!--            </xsl:element>-->
<!--            <xsl:element name="CatRef"><xsl:value-of select="$CatRef"/></xsl:element>            -->
<!--        </xsl:element>-->
    </xsl:template>
    
    <xsl:template match="BASettingRef">
        <xsl:param name="rulePos"/>
        <xsl:variable select="text()" name="baref"/>
        <xsl:apply-templates select="../../DBASetting[@id = $baref] | ../../../BBASettings/BBASetting[@id = $baref]">
            <xsl:with-param name="rulePos" select="$rulePos"/>
        </xsl:apply-templates>        
    </xsl:template>
</xsl:stylesheet>

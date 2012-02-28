<?xml version="1.0" encoding="UTF-8"?>
<!--
wARNING: THIS QUERY IS NOT MAINTAINED
* This query assumes that the Antecedent/Consequent is a DBA referencing DBA which references BBA
* Logical connectives are ignored
* Condition is ignored

-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xd="http://www.oxygenxml.com/ns/doc/xsl" version="1.0"
    xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1"
    xmlns:arb="http://keg.vse.cz/ns/arbuilder0_1"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" >
    <xsl:output method="text" indent="yes"/>
    <xsl:template match="/arb:ARBuilder">
        <xsl:apply-templates select="ARQuery"/>
    </xsl:template>
    
    <!-- The ARBuilder output has DBA as antecedent -->
    <xsl:template match="DBASetting" mode="topLevelCedent">
        <xsl:param name="cedentType"/>
        <xsl:variable name="BAIDs" select="BASettingRef"></xsl:variable>
        
        <!-- outut cedent specific text -->
        <xsl:choose>
            <xsl:when test="$cedentType='antecedent'">
                <xsl:text>
d:haveantecedent($DBAANT : p:antecedent, $RULE: p:associationrule)
                </xsl:text>                        
            </xsl:when>
            <xsl:when test="$cedentType='consequent'">
                <xsl:text>
        d:haveconsequent($DBACONS : p:consequent, $RULE: p:associationrule)
                </xsl:text>
            </xsl:when>
        </xsl:choose>
        <!-- set variables for further processing -->
        <xsl:variable name="cedentNameVariable">
            <xsl:choose>
                <xsl:when test="$cedentType='antecedent'">
                    <xsl:text>$DBAANT</xsl:text>                        
                </xsl:when>
                <xsl:when test="$cedentType='consequent'">
                    <xsl:text>$DBACONS</xsl:text>
                    </xsl:when>
            </xsl:choose>        
        </xsl:variable>
        
        <xsl:apply-templates select="../DBASetting[@id = $BAIDs]" mode="cedent">
            <xsl:with-param name="topCedentName" select="$cedentNameVariable"></xsl:with-param>
        </xsl:apply-templates>
    </xsl:template>
    
    <xsl:template match="DBASetting" mode="cedent">
        <xsl:param name="topCedentName"></xsl:param>
        <xsl:variable name="BAIDs" select="BASettingRef"></xsl:variable>
        <xsl:apply-templates select="../DBASetting[@id = $BAIDs]" mode="literal">
            <xsl:with-param name="topCedentName" select="$topCedentName"></xsl:with-param>
        </xsl:apply-templates>
        
    </xsl:template>
    
    
    <xsl:template match="DBASetting" mode="literal">
        <xsl:param name="topCedentName"></xsl:param>
        <xsl:variable name="thisDBASettingName"><xsl:text>$Literal</xsl:text><xsl:value-of select="@id"/></xsl:variable>
        <!-- this is a passthrough DBA /literal level-->
        <xsl:text>
            ,d:bederivedfrom(
        </xsl:text>        
        <xsl:value-of select="$topCedentName"/>
        <xsl:text> : d:derivedbooleanattribute,</xsl:text> 
        <xsl:value-of select="$thisDBASettingName"/>    
            <xsl:text>: d:booleanattribute)</xsl:text>
        
        <xsl:variable name="BAIDs" select="BASettingRef"></xsl:variable>
        <xsl:apply-templates select="//BBASetting[@id = $BAIDs]">
            <xsl:with-param name="DBASetting" select="$thisDBASettingName"></xsl:with-param>
        </xsl:apply-templates>
    </xsl:template>
    
    <xsl:template match="BBASetting" >        
        <!--  GetBBAContainingCategoryAsString("<35;45)", "Age", $BBA1), d:bederivedfrom($DBAANT : d:derivedbooleanattribute, $BBA1 : d:booleanattribute) --> 
        <xsl:param name="DBASetting"></xsl:param>
        <xsl:text>
        ,GetBBAContainingCategoryAsString("</xsl:text><xsl:value-of select="Coefficient/Category"/><xsl:text>"
        ,"</xsl:text><xsl:value-of select="FieldRef"/><xsl:text>",
            $BBA</xsl:text><xsl:value-of select="@id"/><xsl:text>),d:bederivedfrom(</xsl:text>
        <xsl:value-of select="$DBASetting"/>        
        <xsl:text> : d:derivedbooleanattribute, $BBA</xsl:text><xsl:value-of select="@id"/>
        <xsl:text>: d:booleanattribute)</xsl:text>        
    </xsl:template>    
    
    <xsl:template match="ARQuery">
        <xsl:text>
            using d for i"http://keg.vse.cz/dmo/" 
            using p for i"http://www.dmg.org/PMML-4_0#"

/*Support predicates, also in arlib*/
             getTopicInstanceForString($String,$TopicType, $Topic):- value ($Object,$String),topic-name($Topic, $Object), instance-of( $Topic, $TopicType). 

            GetBBAContainingCategoryAsString($RuleCategoryAsString, $DerivedFieldAsString, $BBA) :- 
getTopicInstanceForString($DerivedFieldAsString, p:DerivedField, $DerivedField),
getTopicInstanceForString($RuleCategoryAsString, d:derivedfieldcontent, $RuleCategory), d:havebin($DerivedField :p:DerivedField ,$RuleCategory : d:derivedfieldcontent), d:havecoefficient($BBA : d:basicbooleanattribute, $RuleCategory: d:coefficient).
        </xsl:text>
        <xsl:text>
            getRules($RuleAsString) :- 
        </xsl:text>
        <xsl:variable name="AntecedentDBASettingID" select="AntecedentSetting"></xsl:variable>
        <xsl:variable name="ConsequentDBASettingID" select="ConsequentSetting"></xsl:variable>
        <xsl:if test="$AntecedentDBASettingID">
            <xsl:apply-templates select="DBASettings/DBASetting[@id=$AntecedentDBASettingID]" mode="topLevelCedent">
                <xsl:with-param name="cedentType" select="'antecedent'"></xsl:with-param>
            </xsl:apply-templates>
            <xsl:if test="$ConsequentDBASettingID"><xsl:text>,</xsl:text></xsl:if>
        </xsl:if>
        <xsl:apply-templates select="DBASettings/DBASetting[@id=$ConsequentDBASettingID]" mode="topLevelCedent">
            <xsl:with-param name="cedentType" select="'consequent'"></xsl:with-param>
        </xsl:apply-templates>
        <!-- It is necessary to convert the resulting rule object to string with topic-name and value predicates, otherwise they would appear only as topic identifiers in the result-->
        <xsl:text>, topic-name($RULE, $ObjectR), value ($ObjectR,$RuleAsString). </xsl:text>
        <!-- the problem is that if the query is issued via TMRAP, there is an error since TMRAP does not allow objects (datatype returned by topic-name) to appear in the result,
            therefore it is necessary to hide the object into an inference rule, and then call it.
        -->
        <xsl:text> </xsl:text>
        <xsl:text>getRules($RuleAsString)?</xsl:text>
    </xsl:template>
</xsl:stylesheet>

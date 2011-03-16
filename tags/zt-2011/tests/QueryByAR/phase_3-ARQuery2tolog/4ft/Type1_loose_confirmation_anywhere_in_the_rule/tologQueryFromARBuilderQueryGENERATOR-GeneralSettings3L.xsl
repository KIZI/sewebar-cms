<?xml version="1.0" encoding="UTF-8"?>
<!--
Input: phase_2-QBAR2ARQUERY/Type 1
       valid against http://sewebar.vse.cz/schemas/ARQuery0_1.sch

Output: tolog query against GUHA AR ONTOLOGY

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
    
    <xsl:template match="RulePart">
        <xsl:choose>
            
        <xsl:when test="text()='Antecedent'">
            <xsl:text>
        d:haveantecedent($DBAANT : p:antecedent, $RULE: p:associationrule)
                </xsl:text>                        
        </xsl:when>
            <xsl:when test="text()='Consequent'">
            <xsl:text>
        d:haveconsequent($DBACONS : p:consequent, $RULE: p:associationrule)
                </xsl:text>
        </xsl:when>
            <xsl:when test="text()='Condition'">
            <xsl:text>
        d:haveconsequent($DBACOND : p:condition, $RULE: p:associationrule)
                </xsl:text>
        </xsl:when>
        </xsl:choose>
        <xsl:if test="following-sibling::RulePart">,</xsl:if>
    </xsl:template>
    <xsl:template match="RulePart" mode="variable">
        <xsl:choose>
            <xsl:when test="text()='Antecedent'">
                <xsl:text>$DBAANT</xsl:text>                        
            </xsl:when>
            <xsl:when test="text()='Consequent'">
                <xsl:text>$DBACONS</xsl:text>
            </xsl:when>
            <xsl:when test="text()='Condition'">
                <xsl:text>$DBACOND</xsl:text>
            </xsl:when>
        </xsl:choose>                
    </xsl:template>
    

    
    <xsl:template match="RulePart" mode="wrapInCedents">
        <xsl:param name="CedentName"></xsl:param>
        
        <!-- this is a passthrough DBA / literal level-->
        
        <!-- if there are multiple rule parts wrap the whole thing in an OR clause-->
        <xsl:if test="not(preceding-sibling::RulePart) and following-sibling::RulePart">{</xsl:if>        
        <xsl:text>
            d:bederivedfrom(
        </xsl:text>        
        <xsl:apply-templates select="." mode="variable"/>
        <xsl:text> : d:derivedbooleanattribute,</xsl:text> 
        <xsl:value-of select="$CedentName"/>    
        <xsl:text>: d:booleanattribute)</xsl:text>                
        <xsl:choose>
            <!-- there are multiple rule parts and this is not the last one -->
            <xsl:when test="following-sibling::RulePart">|</xsl:when>
            <!-- there are multiple rule parts and this is the last  one -->
            <xsl:when test="preceding-sibling::RulePart">}</xsl:when>
        </xsl:choose>       
    </xsl:template>
    
    
    
    <xsl:template match="DBASetting">
        <xsl:variable name="thisLiteralName"><xsl:text>$L_</xsl:text><xsl:value-of select="@id"/></xsl:variable>
        <xsl:variable name="artificialCedentName"><xsl:text>$C_</xsl:text><xsl:value-of select="@id"/></xsl:variable>
        <!-- create path from rule part to cedent-->
        <xsl:apply-templates select="//RulePart" mode="wrapInCedents">
            <xsl:with-param name="CedentName" select="$artificialCedentName"></xsl:with-param>
        </xsl:apply-templates>
        <!-- create path from  cedent to this literal-->
        <xsl:text>
            ,d:bederivedfrom(
        </xsl:text>        
        <xsl:value-of select="$artificialCedentName"/>
        <xsl:text> : d:derivedbooleanattribute,</xsl:text> 
        <xsl:value-of select="$thisLiteralName"/>    
        <xsl:text>: d:booleanattribute)</xsl:text>                
        
        <!-- create path from  literal to BBA-->
        <xsl:variable name="BAIDs" select="BASettingRef"></xsl:variable>
        <xsl:apply-templates select="//BBASetting[@id = $BAIDs]">
            <xsl:with-param name="DBASetting" select="$thisLiteralName"></xsl:with-param>
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
        <xsl:apply-templates select="GeneralSetting/Scope/RulePart"/>,

        <xsl:for-each select="GeneralSetting/MandatoryPresenceConstraint/MandatoryBA">
            <xsl:variable name="curID" select="."></xsl:variable>
            <xsl:apply-templates select="//DBASetting[@id=$curID]"/>
            <xsl:if test="following-sibling::MandatoryBA">,</xsl:if>            
        </xsl:for-each>
        
        <!-- It is necessary to convert the resulting rule object to string with topic-name and value predicates, otherwise they would appear only as topic identifiers in the result-->
        <xsl:text>, topic-name($RULE, $ObjectR), value ($ObjectR,$RuleAsString). </xsl:text>
        <!-- the problem is that if the query is issued via TMRAP, there is an error since TMRAP does not allow objects (datatype returned by topic-name) to appear in the result,
            therefore it is necessary to hide the object into an inference rule, and then call it.
        -->
        <xsl:text> </xsl:text>
        <xsl:text>getRules($RuleAsString)?</xsl:text>
    </xsl:template>
</xsl:stylesheet>

<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:p="http://www.dmg.org/PMML-4_0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:keg="http://keg.vse.cz" xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1"
    extension-element-prefixes="func exsl"
    exclude-result-prefixes="p xsi keg guha">
    
    <xsl:output method="xml" encoding="UTF-8" indent="yes"/>
    <xsl:strip-space elements="*"/>
    
    <xsl:template match="/">
        <xsl:element name="PMML">
        <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="p:Header">
        <xsl:element name="Header">
            <xsl:attribute name="copyright"><xsl:value-of select="@copyright"/></xsl:attribute>
<!--            <xsl:element name="Annotation"><xsl:apply-templates select="p:Annotation"/></xsl:element>           -->
            <xsl:apply-templates select="guha:AssociationModel"/>
        </xsl:element>
       
    </xsl:template>
    
    <xsl:template match="p:Annotation">
        <xsl:value-of select="."/>
       
    </xsl:template>
    
    <xsl:template match="p:TransformationDictionary">
        <xsl:element name="fieldValuesSet">
           <xsl:attribute name="fieldCount">
               <xsl:value-of select="count(p:DerivedField)"/>
           </xsl:attribute>
            <xsl:apply-templates select="p:DerivedField"/>
        </xsl:element>        
    </xsl:template>
    
    <xsl:template match="p:DerivedField">
      <xsl:element name="{@name}">
          <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
          <xsl:attribute name="type"><xsl:value-of select="@optype"/></xsl:attribute>
           
            <xsl:apply-templates select="p:Discretize/p:DiscretizeBin | p:MapValues/p:InlineTable/p:Extension[@name='Frequency']"/>
            
      </xsl:element>
        
    </xsl:template>
    
    <xsl:template match="p:DiscretizeBin">
        <xsl:element name="fieldValue">
<!--           not needed, development variable
    <xsl:attribute name="check"><xsl:value-of select="@binValue"/></xsl:attribute>-->
                <xsl:for-each select="p:Interval">
                      <xsl:choose>
                           
                            <xsl:when test="@leftMargin = @rightMargin">
                                <xsl:attribute name="from">
                                    <xsl:value-of select="@leftMargin"/>
                                </xsl:attribute>
                            </xsl:when>						    
                            <xsl:otherwise>
                                <xsl:attribute name="from"><xsl:value-of select="@leftMargin"/></xsl:attribute>
                                <xsl:attribute name="to"><xsl:value-of select="@rightMargin"/></xsl:attribute>
                            </xsl:otherwise>
                           
                        </xsl:choose>
                </xsl:for-each>
            
<!--            <xsl:attribute name="frequency"><xsl:value-of select="p:Extension[@name='Frequency']/@value"/></xsl:attribute>-->
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="p:Extension[@name='Frequency']">
        <xsl:element name="fieldValue">
<!--           not needed 
    <xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>-->
            <xsl:value-of select="@extender"/>
        </xsl:element>           
    </xsl:template>
    
    <xsl:template match="p:BBASettings">
        <BBASettings>
            <xsl:apply-templates select="p:BBASetting"/>
        </BBASettings>
    </xsl:template>
    
    
    <xsl:template match="p:BBASetting">
        <xsl:element name="bba">
            <xsl:attribute name="name"><xsl:value-of select="p:Attribute"/></xsl:attribute>
            <xsl:attribute name="coefficient"><xsl:value-of select="p:CoefficientType"/></xsl:attribute>
            <xsl:attribute name="minLength"><xsl:value-of select="p:MinimalLength"/></xsl:attribute>
            <xsl:attribute name="maxLength"><xsl:value-of select="p:MaximalLength"/></xsl:attribute>
            <xsl:attribute name="type"><xsl:value-of select="p:LiteralType"/></xsl:attribute>
            <xsl:attribute name="equivalence"><xsl:value-of select="p:EquivalenceClass"/></xsl:attribute>            
        </xsl:element>
    
    </xsl:template>
    
    <xsl:template match="p:BooleanAttributeId">
        <xsl:variable name="ref" select="."/>
       
        <xsl:if test="position()>1">
             <xsl:choose>
                  <xsl:when test="../@type='Disjunction'"> <xsl:value-of select="'not'"/> </xsl:when>
                  <xsl:when test="../@type='Conjunction'"> <xsl:value-of select="'and'"/></xsl:when>
             </xsl:choose>
        </xsl:if>
        <!--  odvozeny atribut se muze skladat z dalsich zakladnich a odvozenych atributu: dochazi k rekurzi -->
        <xsl:apply-templates select="../../../p:BBASettings/p:BBASetting[@id=$ref] | ../../p:DBASetting[@id=$ref]"/>
    </xsl:template>
    
    <xsl:template match="guha:AssociationModel">
        <xsl:element name="AssociationModel">           
            <xsl:apply-templates select="AssociationRules/AssociationRule"/>            
        </xsl:element>            
    </xsl:template>
    
    <xsl:template match="AssociationRules/AssociationRule">    
        <xsl:param name="arrowOnly"/>
        <xsl:variable name="ante" select="@antecedent"/>
        <xsl:variable name="cons" select="@consequent | @succedent"/>
        <xsl:variable name="cond" select="@condition"/>            
        <xsl:element name="AssociationRule">
            
<!--            keep model informations together with rule for easier extraxtion-->
            
            
                <xsl:element name="modelName">
                    <xsl:value-of select="../../../guha:AssociationModel/@modelName"/>
                </xsl:element>
                <!--<xsl:element name="numberOfRules">
                    <xsl:value-of select="../../../guha:AssociationModel/@numberOfRules"/>
                </xsl:element>
                <xsl:element name="functionName">
                    <xsl:value-of select="../../../guha:AssociationModel/@functionName"/>
                </xsl:element>-->

           

            <!--            get the text representation of the rule-->
            <xsl:if test="count(Text)>0">
                <xsl:element name="Text"><xsl:value-of select="Text"/></xsl:element>
            </xsl:if>
            <xsl:if test="count(IMValue)>0">
                <xsl:apply-templates select="IMValue"/>
            </xsl:if>
        <!-- Pravidlo ve formatu Antecedent => Consequent [/ Condition] 
            - podminka se nemusi vyskytovat
            - kazda cast pravidla muze byt Item nebo Itemset, 
            pricemz Itemset se muze opet skladat z Itemu nebo Itemsetu...
        -->
        
            <xsl:choose>
                <xsl:when test="count(../DBA[@id=$ante]/BARef)+count(../BBA[@id=$ante])>0">
                    <!--  <xsl:element name="antecedent">-->
                    <xsl:call-template name="cedent">
                        <xsl:with-param name="cedentID" select="$ante"/>
                    </xsl:call-template>
                   <!-- </xsl:element>-->
                </xsl:when>
                <!-- antecedent exists for Itemset, but doesn't refer any other items -->
                <xsl:otherwise>
                    
                </xsl:otherwise>
            </xsl:choose>
            <!-- arrow -->
           
            <!-- consequent -->
            <!--<xsl:element name="consequent">-->
            <xsl:call-template name="cedent">
                <xsl:with-param name="cedentID" select="$cons"/>
            </xsl:call-template>
            <!--</xsl:element>-->
            <!-- condition -->
            <xsl:if test="../BBA[@id=$cond] | ../DBA[@id=$cond]">
              <!--  <xsl:element name="condition">-->
                <xsl:call-template name="cedent">
                    <xsl:with-param name="cedentID" select="$cond"/>
                </xsl:call-template>
               <!-- </xsl:element>-->
            </xsl:if>
        </xsl:element>          
    </xsl:template>
    
    <xsl:template match="IMValue">
        <xsl:variable name="quantifier" select="translate(@name,'()&gt;%=','')"></xsl:variable>
        <xsl:element name="{$quantifier}"><xsl:value-of select="."/></xsl:element>
    </xsl:template>
    
    <xsl:template name="cedent">
        <xsl:param name="cedentID"/>
        <xsl:param name="rulePart"/>
<!--        <xsl:choose>-->
            <!-- LISp-Miner has a text representation of the whole cedent in the text extension -->
            <!--<xsl:when test="/p:PMML/p:Header/p:Application/@name='LISp-Miner'">
                <xsl:element name="DBA"><xsl:value-of select="../DBA[@id=$cedentID]/Text"/></xsl:element>                    
            </xsl:when>
            <xsl:otherwise>-->
                <!-- While Ferda has not, so the textual representation needs to be reconstructed -->
                <!-- beware - this would not work with LISp-Miner very well -->
                <xsl:apply-templates select="../BBA[@id=$cedentID] | ../DBA[@id=$cedentID]">
                    <xsl:with-param name="topLevel" select="'1'"/>
                </xsl:apply-templates>
<!--            </xsl:otherwise>-->
<!--        </xsl:choose>-->
    </xsl:template>
    
    <xsl:template match="BBA">
        <xsl:param name="topLevel"/>
        <xsl:variable name="cr" select="translate(CatRef,';()&lt;&gt;%=',' ')"></xsl:variable>
        <xsl:variable name="fRef" select="FieldRef"/>
        <!--<xsl:attribute name="{FieldRef}">            
            <xsl:value-of select="$cr"/>            
            </xsl:attribute>-->
        <!--<xsl:element name="Name">            
            <xsl:value-of select="FieldRef"/>            
        </xsl:element>-->
        <xsl:element name="{FieldRef}">            
            <xsl:value-of select="$cr"/>            
        </xsl:element>        
        <!--  <xsl:apply-templates select="/p:PMML/p:TransformationDictionary/p:DerivedField[@name=$fRef]"/>-->
    </xsl:template>
    
    
    
    <xsl:template match="DBA">
        <xsl:param name="topLevel"/>
        <!-- item can be preceded by NOT operator -->
<!--        <xsl:element name="DBA"><xsl:value-of select="."/></xsl:element>-->
        <xsl:choose>
            <xsl:when test="@connective='Negation'">
               <xsl:apply-templates select="BARef"/>
            </xsl:when>
            <xsl:when test="$topLevel='1' or count(BARef) =1">
                <xsl:apply-templates select="BARef"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates select="BARef"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    <!-- ......................................... -->
    <!-- Itemset se sklada s odkazu (ItemRef) na Itemy nebo Itemsety -->
    <xsl:template match="BARef">
        <xsl:variable name="ref" select="text()"/>
        <!-- items are delimited by concective AND or OR -->
        <xsl:if test="position() > 1">
            <xsl:choose>
                <xsl:when test="../p:Extension[@name='Connective' and @value='Disjunction']"></xsl:when>
                <xsl:otherwise> </xsl:otherwise>
            </xsl:choose>
        </xsl:if>
        <!-- Itemset refers (by ItemRef) to other Items or Itemsets - RECURSION -->
        <xsl:apply-templates select="../../BBA[@id=$ref] | ../../DBA[@id=$ref]"/>
    </xsl:template>
    
   
</xsl:stylesheet>

<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
    xmlns:p="http://www.dmg.org/PMML-3_2"    xmlns="http://www.dmg.org/PMML-3_2" 
    xmlns:exsl="http://exslt.org/common" xmlns:func="http://exslt.org/functions" xmlns:keg="http://keg.vse.cz"
    extension-element-prefixes="func exsl" 
    exclude-result-prefixes="p keg" >
    
    <!-- This XSLT transformation  corrects errors in PMML input of Ferda and LISp-Miner -->
    <!-- Author Tomas Kliegr-->
    <!-- TO DO
        keg:includeSigLevelIfMissing is currently disabled because it was too slow
    -->
    <xsl:param name="dictionaryURL" select="'InterestMeasureDictionary.xml'"></xsl:param>    
    <xsl:variable name="appName" select="/p:PMML/p:Header/p:Application/@name"/>
    <!--  This function is here to bridge the gap between BKEF 1.0 and BKEF 2.0 -->
    <func:function name="keg:getTestCriterionName">
        <xsl:param name="InterestMeasureName"/>
        <xsl:variable name="translated"  select="document($dictionaryURL)/InterestMeasures/InterestMeasure[str=$InterestMeasureName]/TestCriteria/TestCriterion[1]/str[@lang='MasterName']"/>
        <xsl:choose>                
            <xsl:when test="$translated and not($translated='')"> <func:result select="$translated"/></xsl:when>
            <xsl:otherwise><func:result select="$InterestMeasureName"/></xsl:otherwise>
        </xsl:choose>                   
    </func:function>
    <!-- if interest measure has a significance level and the data mining tool does not include it in the results (it is only in task setting),
    this system returns an xml fragment describing the significance level. The fragment is in the format used in the results
    -->
    <func:function name="keg:includeSigLevelIfMissing">
        <!-- This function is very slow, the results of the xpath  queries need to be cached -->
        <xsl:param name="testCriterionMasterName"/>        
        <!-- //InterestMeasure[./TestCriteria/TestCriterion/str[@lang='MasterName']/text()='Lower Critical Implication' and ./TestCriteria/TestCriterion[@type='SignificanceLevel']/str[@software='LISp-Miner' and @missing='true']]/str[@lang='pmml' and @software='LISp-Miner']/text() -->
        <xsl:variable name="interestMeasurePMMLName"  select="document($dictionaryURL)//InterestMeasure[./TestCriteria/TestCriterion/str[@lang='MasterName']/text()=$testCriterionMasterName and ./TestCriteria/TestCriterion[@type='SignificanceLevel']/str[@software=$appName and @missing='true']]/str[@lang='pmml' and @software=$appName]/text()"/>
        <xsl:choose>                
            <xsl:when test="$interestMeasurePMMLName and not(interestMeasurePMMLName = '')">
                <xsl:variable name="sigLevelMasterName" select="document($dictionaryURL)//InterestMeasure[str/@lang='pmml' and str/@software=$appName and str/text()=$interestMeasurePMMLName]/TestCriteria/TestCriterion[@type='SignificanceLevel']/str[@lang='MasterName']"/>
                <xsl:variable name="sigLevel" select="//p:Extension[@name='QuantifierThreshold' and @value= $interestMeasurePMMLName]/p:SignificanceLevel"/>
                <func:result>        
                    <xsl:text>
                    </xsl:text>
                    <Extension name="{$sigLevelMasterName}"><xsl:value-of select="$sigLevel+0"/></Extension>    
                </func:result>
            </xsl:when>
            <xsl:otherwise><func:result select="''"/></xsl:otherwise>
        </xsl:choose>                   
    </func:function>
    
    <func:function name="keg:getSignificanceLevelName">
        <xsl:param name="InterestMeasureName"/>
        <xsl:variable name="translated"  select="document($dictionaryURL)/InterestMeasures/InterestMeasure[str=$InterestMeasureName]/TestCriteria/TestCriterion[@type='SignificanceLevel']/str[@lang='MasterName']"/>
        <xsl:choose>                
            <xsl:when test="$translated and not($translated='')"> <func:result select="$translated"/></xsl:when>
            <xsl:otherwise><func:result select="$InterestMeasureName"/></xsl:otherwise>
        </xsl:choose>                   
    </func:function>
        
    <func:function name="keg:closuresMatch">
        <xsl:param name="closureofLeftInterval"/>
        <xsl:param name="closureofRightInterval"/>        
        <xsl:choose>                
            <xsl:when test="($closureofLeftInterval='closedOpen' and $closureofRightInterval='openClosed') or ($closureofLeftInterval='closedOpen' and $closureofRightInterval='openOpen')or ($closureofLeftInterval='openOpen' and $closureofRightInterval='openOpen') or ($closureofLeftInterval='openOpen' and $closureofRightInterval='openClosed')"> 
                <func:result select="'false'"/></xsl:when>
            <xsl:otherwise><func:result select="'true'"/></xsl:otherwise>
        </xsl:choose>                   
    </func:function>
    <func:function name="keg:closureMerge">
        <xsl:param name="closureofLeftInterval"/>
        <xsl:param name="closureofRightInterval"/>
        <xsl:variable name="leftB">
        <xsl:choose>                
            <xsl:when test="substring($closureofLeftInterval,1,2)='op'">open</xsl:when>
            <xsl:otherwise>closed</xsl:otherwise>
        </xsl:choose>              
        </xsl:variable>
        <xsl:variable name="rightB">
            <xsl:choose>                
                <xsl:when test="(string-length($closureofRightInterval)=8) or (string-length($closureofRightInterval)=10 and substring($closureofRightInterval,1,2)='cl')">Open</xsl:when>
                <xsl:otherwise>Closed</xsl:otherwise>
            </xsl:choose>              
        </xsl:variable> 
        <func:result select="concat($leftB,$rightB)"/>
    </func:function>    
    <func:function name="keg:translateInterestMeasure">
        <!-- parametr name je volitelny. povinny je tehdy, pokud nezname id prekladaneho vyrazu, coz nastava pri prekladu z XML-->    
        <xsl:param name="name"/>
        <xsl:param name="type"/> <!-- InterestMeasure nebo TestCriterion -->
        <xsl:param name="fromLang"/>
        <xsl:param name="toLang"/>        
        <xsl:variable name="translated" >
            <xsl:choose>                       
                <xsl:when test="$type='InterestMeasure'">
                    <xsl:value-of select="document($dictionaryURL)/InterestMeasures/InterestMeasure[str/@lang=$fromLang and str=$name]/str[@lang=$toLang]"/>
                </xsl:when>
                <xsl:when test="$type='TestCriterion'">
                    <xsl:value-of select="document($dictionaryURL)/InterestMeasures/InterestMeasure/TestCriteria/TestCriterion[str/@lang=$fromLang and str=$name]/str[@lang=$toLang]"/> 
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="'TEMPLATE_ERROR'"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:choose>                
            <xsl:when test="$translated and not($translated='')"> <func:result select="$translated"/></xsl:when>
            <xsl:otherwise><func:result select="$name"/></xsl:otherwise>
        </xsl:choose>                    
    </func:function>
    
        <xsl:template match="node()|@*" >
            <xsl:copy>                
                <xsl:apply-templates select="node()|@*"></xsl:apply-templates>
            </xsl:copy>
        </xsl:template>
    
        <xsl:template match="p:Extension[@name='QuantifierThreshold']" priority="100">            
            <xsl:variable name="norm" select="keg:translateInterestMeasure(@value,'InterestMeasure','pmml', 'MasterName')"></xsl:variable>
            <Extension name="QuantifierThreshold" value="{$norm}">
                <xsl:apply-templates select="p:Threshold">
                    <xsl:with-param name="testCriterionName" select="keg:getTestCriterionName($norm)"></xsl:with-param>
                </xsl:apply-templates>
                <xsl:apply-templates  select="p:SignificanceLevel">
                    <xsl:with-param name="significanceLevelName" select="keg:getSignificanceLevelName($norm)"></xsl:with-param>
                </xsl:apply-templates>
             </Extension>   
        </xsl:template>
    
    <xsl:template match="p:Extension/p:Threshold">
        <xsl:param name="testCriterionName"></xsl:param>
        <Threshold name="{$testCriterionName}"><xsl:value-of select="text()"/></Threshold>
    </xsl:template>
    
    <xsl:template match="p:Extension/p:SignificanceLevel">
        <xsl:param name="significanceLevelName"></xsl:param>
        <SignificanceLevel name="{$significanceLevelName}"><xsl:value-of select="text()"/></SignificanceLevel>
    </xsl:template>
    
    <xsl:template match="p:Extension[@name='Quantifier']" priority="100">        
        <xsl:variable name="norm" select="keg:translateInterestMeasure(@extender,'TestCriterion','pmml', 'MasterName')"/>        
        <Extension name="Quantifier" value="{@value+0}" extender="{$norm}"/>
        
        <!-- <xsl:copy-of select="keg:includeSigLevelIfMissing($norm)"/> -->         
    </xsl:template>        
    

    <xsl:template match="p:DiscretizeBin/p:Interval">
        <xsl:choose>
            <xsl:when test="count(../p:Interval)=1">
                <!-- Performance optimization for nodes that need not be merged -->
                <Interval 
                    closure="{@closure}" 
                    leftMargin="{@leftMargin}" 
                    rightMargin="{@rightMargin}" 
                />
            </xsl:when>
            <xsl:when test="@rightMargin = following-sibling::*[1]/@leftMargin and keg:closuresMatch(@closure,following-sibling::*[1]/@closure) ='true'">
                <xsl:comment>Interval was merged during normalization</xsl:comment>
            </xsl:when>
            <xsl:when test="@leftMargin = preceding-sibling::*[local-name(.)='Interval'][1]/@rightMargin and keg:closuresMatch(preceding-sibling::*[1]/@closure,@closure)='true'">
                <!-- This Interval has a preceding connecting interval that was skipped, but the following interval is not connecting and cannot be merged -->
                <xsl:apply-templates select="preceding-sibling::*[local-name(.)='Interval'][1]" mode="getMerged">
                    <xsl:with-param name="origRightMargin" select="@rightMargin"/>
                    <xsl:with-param name="origClosure" select="@closure"/>
                    <xsl:with-param name="lastClosure" select="@closure"/>
                    <xsl:with-param name="lastLeftMargin" select="@leftMargin"/>            
                </xsl:apply-templates>                 
            </xsl:when>
            <xsl:otherwise>
                <Interval 
                    closure="{@closure}" 
                    leftMargin="{@leftMargin}" 
                    rightMargin="{@rightMargin}" 
                />
            </xsl:otherwise>
        </xsl:choose>                      
    </xsl:template>
    <xsl:template  match="p:Interval" mode="getMerged">
        <xsl:param name="origRightMargin"/>
        <xsl:param name="lastLeftMargin"/>
        <xsl:param name="lastClosure"/>
        <xsl:param name="origClosure"/>
        <xsl:param name="openningMargin"/>
        <xsl:choose>
            <xsl:when test="$lastLeftMargin = @rightMargin and keg:closuresMatch(@closure,$lastClosure)='true' and count(preceding-sibling::*[local-name(.)='Interval'])>0">
                        <xsl:apply-templates select="preceding-sibling::*[local-name(.)='Interval'][1]" mode="getMerged">
                            <xsl:with-param name="origRightMargin" select="$origRightMargin"/>
                            <xsl:with-param name="origClosure" select="$origClosure"/>
                            <xsl:with-param name="lastLeftMargin" select="@leftMargin"/>
                            <xsl:with-param name="lastClosure" select="@closure"/>
                        </xsl:apply-templates>                                                                 
            </xsl:when>
            <xsl:when test="$lastLeftMargin = @rightMargin and keg:closuresMatch(@closure,$lastClosure)='true'">
                <Interval 
                    closure="{keg:closureMerge(@closure,$origClosure)}" 
                    leftMargin="{@leftMargin}"  
                    rightMargin="{$origRightMargin}" 
                />
            </xsl:when>
            <xsl:otherwise>
                <Interval 
                    closure="{keg:closureMerge(@lastClosure,$origClosure)}" 
                    leftMargin="{$lastLeftMargin}"  
                    rightMargin="{$origRightMargin}" 
                />                
            </xsl:otherwise>            
        </xsl:choose>
                    
        
        
    </xsl:template>
        
    
</xsl:stylesheet>

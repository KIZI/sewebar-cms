<?xml version="1.0" encoding="UTF-8"?>
<!-- 
This schematron  1.5 schema checks
- if a document uses ARQuery model of the ARBuilder schema
- if the AR Query document complies with SEWEBAR restrictions.
Specifically, query terms are expressed on literal level (DBASetting).
The only supported scope is EntireRule (via GeneralSetting). It is not possible to query separately Antecedent/Consequent/Condition/...
-->
<schema xmlns="http://www.ascc.net/xml/schematron">
    <ns uri="http://keg.vse.cz/ns/GUHA0.1rev1" prefix="guha"/> 
    <ns uri="http://keg.vse.cz/ns/arbuilder0_1" prefix="arb"/>    
    <pattern name="main">
        <!--let name="DBASettingIDs" value="/arb:ARBuilder/ARQuery/DBASettings/DBASetting/@id"/>
        <let name="BBASettingIDs" value="/arb:ARBuilder/ARQuery/BBASettings/BBASetting/@id"/-->
        <rule context="arb:ARBuilder">
            <assert test="ARQuery">
                The document must contain ARQuery node.
            </assert>
        </rule>
        <rule context="ARQuery">            
            <assert test="not(AntecedentSetting) and not(ConsequentSetting) and not(ConditionSetting)">
                AntecedentSetting, ConsequentSetting and ConditionSetting forbidden, use GeneralSetting 
            </assert>
            <assert test="GeneralSetting">
                GeneralSetting must be defined
            </assert>            
        </rule>
        <rule context="GeneralSetting">
            <assert test="Scope/RulePart = 'Antecedent' and Scope/RulePart = 'Consequent' and Scope/RulePart = 'Condition'" >
                The only supported scope is Antecedent and Consequent and Condition (all at once)
            </assert>
            
            <assert test="MandatoryPresenceConstraint">
                MandatoryPresenceConstraint must be defined (and at least one MandatoryB)
            </assert>
        </rule>
        <rule context="BBASetting">
            <assert test="Coefficient/Type='One category'">
                The only supported coefficient type is 'One category'
            </assert>
        </rule>
        <rule context="DBASetting">
            <assert test="@type='Literal'">
                All DBASetting must be Literal level
            </assert>
        </rule>
        <rule context="MandatoryPresenceConstraint">
            <assert test="MandatoryBA=/arb:ARBuilder/ARQuery/DBASettings/DBASetting/@id">
                MandatoryBA must reference a DBA
            </assert>
            <assert test="count(MandatoryBA)>=1 and count(MandatoryBA)&lt;=5">
                There needs to be at least 1 and at most 5 MandatoryBAs
            </assert>
        </rule>
        <rule context="DBASetting[@type='Literal']/BASettingRef">
            <assert test=".=/arb:ARBuilder/ARQuery/BBASettings/BBASetting/@id">
                Literal type DBA must reference a BBA                
            </assert>
        </rule>
        <rule context="ApplyRecursively">
            <assert test=".='true'">
                ApplyRecursively must be set to true
                </assert>
        </rule>
        <rule context="LiteralSign">
            <assert test=".='Positive'"></assert>
        </rule>
        <rule context="InterestMeasureThreshold">
            <assert test="InterestMeasure='Any Interest Measure'"/>
            <assert test="not(Threshold)"/>
        </rule>
        
    </pattern>
</schema>
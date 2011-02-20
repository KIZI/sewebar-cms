<?xml version="1.0" encoding="UTF-8"?>
<!-- 
This schematron schema checks
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
            <assert test="not(GeneralSetting)and not(ConditionSetting)">
                ConditionSetting, and MandatorySetting forbidden 
            </assert>
            <assert test="AntecedentSetting">
                AntecedentSetting must be defined
            </assert> 
            <assert test="ConsequentSetting">
                ConsequentSetting must be defined
            </assert>             
        </rule>
        
        <rule context="AntecedentSetting|ConsequentSetting">
            <assert test=".=/arb:ARBuilder/ARQuery/DBASettings/DBASetting/@id" >
                Must reference a DBA
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
            <assert test="count(BASettingRef)=1">
                All DBAs must reference exactly one BBA
            </assert>
            <assert test="BASettingRef=/arb:ARBuilder/ARQuery/BBASettings/BBASetting/@id">
                All DBAs must reference exactly one BBA
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
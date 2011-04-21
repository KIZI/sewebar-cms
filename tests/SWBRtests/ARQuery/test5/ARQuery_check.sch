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
            <assert test="ARQuery"> The document must contain ARQuery node. </assert>
        </rule>
        <rule context="ARQuery">
            <assert test="not(GeneralSetting)"> GeneralSetting forbidden </assert>
            <assert test="not(Condition)"> Condition forbidden </assert>
            <assert test="AntecedentSetting"> AntecedentSetting must be defined </assert>
            <assert test="ConsequentSetting"> AntecedentSetting must be defined </assert>
        </rule>

        <rule context="AntecedentSetting|ConsequentSetting|ConditionSetting">
            <assert test=".=/arb:ARBuilder/ARQuery/DBASettings/DBASetting/@id"> Must reference a DBA
            </assert>
        </rule>
        <rule context="BBASetting">
            <assert test="Coefficient/Type='One category' or not(Coefficient)"> Coefficient must be
                missing or 'One category' </assert>
        </rule>
        
        <rule context="DBASetting[@id=/arb:ARBuilder/ARQuery/ConsequentSetting]">
            <assert test="count(BASettingRef) = 1"> Consequent DBASetting must contain exactly one literal </assert>
            <assert test="not(@type='Literal')">Consequent must be wrapped in a non-literal DBA</assert>
        </rule>
        <rule context="DBASetting[@id=//DBASetting[@id=/arb:ARBuilder/ARQuery/ConsequentSetting]/BASettingRef]">            
            <assert test="count(BASettingRef) = 1">Consequent Cedent DBASetting have one BARef</assert>
            <assert test="not(@type='Literal')">Consequent DBASetting must refer to cedent DBA Setting</assert>
        </rule>
        
        <rule context="DBASetting[@id=/arb:ARBuilder/ARQuery/AntecedentSetting]">
            <assert test="count(BASettingRef) = 1"> Antecedent DBASetting must contain exactly one literal
            </assert>
            <assert test="not(@type='Literal')">Antecedent must be wrapped in a non-literal DBA</assert>
        </rule>
        <rule context="DBASetting[@id=//DBASetting[@id=/arb:ARBuilder/ARQuery/AntecedentSetting]/BASettingRef]">
            <assert test="count(BASettingRef) &lt;= 5">Antecedent Cedent DBASetting must have one to five BASettingRef</assert>
            <assert test="not(@type='Literal')">Antecedent DBASetting must refer to cedent DBA Setting</assert>
        </rule>
        <rule context="DBASetting[@id=/arb:ARBuilder/ARQuery/ConditionSetting]">
            <assert test="count(BASettingRef) = 1"> Condition DBASetting (if present) must contain exactly one literal </assert>
            <assert test="not(@type='Literal')">Condition must be wrapped in a non-literal DBA</assert>            
        </rule>
        <rule context="DBASetting[@id=//DBASetting[@id=/arb:ARBuilder/ARQuery/ConditionSetting]/BASettingRef]">            
            <assert test="count(BASettingRef) &lt;= 5">Condition Cedent DBASetting must have one to five BASettingRef</assert>
            <assert test="not(@type='Literal')">Condition DBASetting must refer to cedent DBA Setting</assert>
        </rule>
        <rule context="DBASetting[@type!='Literal']">
            
            <assert test="@type='Conjunction'"> All non-literal DBA must have Conjunction type </assert>
            <assert test="BASettingRef=/arb:ARBuilder/ARQuery/DBASettings/DBASetting/@id"> All DBA
            on non-Literal level must reference existing DBA </assert>
            
            <assert test="BASettingRef=/arb:ARBuilder/ARQuery/DBASettings/DBASetting/@id">  </assert>
            
        </rule>
 
        <rule context="DBASetting[@type='Literal']">
            <assert test="count(BASettingRef)=1"> All DBA on Literal level must reference exactly
                one BBA </assert>
            <assert test="BASettingRef=/arb:ARBuilder/ARQuery/BBASettings/BBASetting/@id"> All DBA
                on Literal level must reference exactly one existing BBA </assert>
        </rule>

        <rule context="ApplyRecursively">
            <assert test=".='true'"> ApplyRecursively must be set to true </assert>
        </rule>
        <rule context="LiteralSign">
            <assert test=".='Positive' or .='Negative' "/>
        </rule>

        <rule context="InterestMeasureSetting[count(InterestMeasureThreshold)=1]">
            <assert
                test="(InterestMeasureThreshold/InterestMeasure='Any Interest Measure' or InterestMeasureThreshold/InterestMeasure='Support' or InterestMeasureThreshold/InterestMeasure='Confidence' or InterestMeasureThreshold/InterestMeasure='Above Average Implication')"
                > Selected interest measure must be from supported list </assert>
        </rule>
        <rule context="InterestMeasureSetting[count(InterestMeasureThreshold)=2]">
            <assert
                test="InterestMeasureThreshold/InterestMeasure='Support' and (InterestMeasureThreshold/InterestMeasure='Confidence' or InterestMeasureThreshold/InterestMeasure='Above Average Implication')"
                > Selected interest measure not from supported combination list </assert>
        </rule>
        <rule context="InterestMeasureSetting[not(count(InterestMeasureThreshold)=1 or count(InterestMeasureThreshold)=2)]">
            <assert test="false">Unsupported number of interest measures</assert>
        </rule>
        
        
    </pattern>
</schema>

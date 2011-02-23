<?xml version="1.0" encoding="UTF-8"?>
<schema xmlns="http://purl.oclc.org/dsdl/schematron">
    <ns uri="http://www.dmg.org/PMML-3_2" prefix="ns"/>
    
    <pattern>
        <rule context="ns:TransformationDictionary">
            <assert test="./ns:DerivedField/ns:Discretize or ./ns:DerivedField/ns:MapValues">
                DerivedField obsahuje bud Discretize nebo MapValues, nic jineho neni povolene
            </assert>
            <assert test="//ns:DiscretizeBin/ns:Extension and ((//ns:DiscretizeBin/ns:Extension/@name = 'Frequency' and //ns:DiscretizeBin/ns:Extension/@extender) or (//ns:DiscretizeBin/ns:Extension/@name = 'Enumeration' and not(//ns:DiscretizeBin/ns:Extension/@extender))) and (number(//ns:DiscretizeBin/ns:Extension/@value)!=0)">
                kazdy DiscretizeBin obsahuje Extension. Pokud je hodnota atributu name "Frequency", potom obsahuje 
                atribut extender, pokud je hodnota "Enumeration", potom ho neobsahuje. V obou pripadech je tam 
                atribut value, ktery obsahuje prirozene cislo
            </assert>
        </rule>
        
        <rule context="ns:TransformationDictionary/ns:DerivedField/ns:MapValues">
            <!--
            <assert test="./@outputColumn = //ns:FieldColumnPair/@column and ./@outputColumn = //ns:FieldColumnPair/@field">
                hodnoty atributu MapValues/FieldColumnPair column a field jsou shodne
            </assert>
            -->
            <assert test="//ns:FieldColumnPair/@column = //ns:FieldColumnPair/@field">
                hodnoty atributu MapValues/FieldColumnPair column a field jsou shodne
            </assert>
            <assert test="//ns:InlineTable/ns:Extension and //ns:InlineTable/ns:Extension/@name = 'Frequency' and (number(//ns:DiscretizeBin/ns:Extension/@value)!=0)">
                InlineTable obsahuje na zacatku nenulovy pocet Extensions, ktere maji opet atributy 
                name, value a extender, name musi byt "Frequency" a value prirozene cislo.
            </assert>
        </rule>
        
        <rule context="ns:InlineTable/ns:row">
            <assert test="count(ns:*) = 2">
                v InlineTable/row jsou vzdy 2 sloupce
            </assert>
        </rule>
        
        
    </pattern>
    
    <pattern>

        
        <rule context="//ns:DataDictionary/ns:DataField/ns:Value/ns:Extension">
            <assert test="./@extender = ../@value">
                Hodnota atributu "extender" tohoto ditete - elementu "Extension" se
                rovna hodnote atributu "value" rodice Value
            </assert>
            <assert test="./@value >= 0 and (./@value = round(./@value))">
                Hodnota atributu "value" elementu "Extension" je nezaporne cele cislo.
            </assert>
        </rule>
    </pattern>
    
    <pattern>
        <rule context="ns:AssociationModel">
            <assert test="./@functionName = 'associationRules'">
                atribut functionName vzdy "associationRules"
            </assert>
            <assert test="./@maxNumberOfItemsPerTA = '1'">
                atribut maxNumberOfItemsPerTA vzdy "1"
            </assert>
            <assert test="./@avgNumberOfItemsPerTA = '1'">
                atribut avgNumberOfItemsPerTA vzdy "1"
            </assert>
            <assert test="./@numberOfItemsets = '0'">
                atribut numberOfItemsets vzdy "0"
            </assert>
            <assert test="./@numberOfRules = count(//ns:AssociationRule)">
                atribut numberOfRules se rovna poctu elementu AssociationRule
            </assert>
            <!-- -->
            <assert test="count(ns:MiningSchema/preceding-sibling::ns:Extension[@name = 'TaskSetting']) = 1 and count(ns:MiningSchema/preceding-sibling::ns:Extension[@name = 'QuantifierThreshold']) >= 1">
                AssociationModel obsahuje pred elementem MiningSchema 1 element 
                Extension s name="TaskSetting" a dale vice elementu s name="QuantifierThreshold"
            </assert>
        </rule>
        
        <rule context="ns:Extension[@name = 'TaskSetting']">
            <assert test="count(./ns:BasicBooleanAttributeSettings) = 1">
                element Extension s name="TaskSetting" obsahuje 
                    - jeden element BasicBooleanAttributeSettings
            </assert>
            <assert test="count(./ns:DerivedBooleanAttributeSettings) = 1">
                element Extension s name="TaskSetting" obsahuje 
                - jeden element DerivedBooleanAttributeSettings
            </assert>
            <assert test="count(./ns:Antecedent) = 1">
                element Extension s name="TaskSetting" obsahuje 
                - jeden Antecedent
            </assert>
            <assert test="count(./ns:Consequent) = 1">
                element Extension s name="TaskSetting" obsahuje 
                - jeden Consequent
            </assert>
            <assert test="count(./ns:Condition) = 1">
                element Extension s name="TaskSetting" obsahuje 
                - jeden Condition
            </assert>
        </rule>
        
        <rule context="ns:BasicBooleanAttributeSettings">
            <assert test="count(//ns:BasicBooleanAttributeSetting) >= 0">
                element BasicBooleanAttributeSettings obsahuje x elementu
                BasicBooleanAttributeSetting
            </assert>
        </rule>
        <rule context="ns:BasicBooleanAttributeSetting">
            <assert test="./@id and ./@name">
                element BasicBooleanAttributeSetting ma atributy id a name
            </assert>
            <assert test="
                (
                    (
                        ns:CoefficientType = 'FixedSet'
                    )                
                    or
                    (
                        name(child::ns:*[position() = 1]) = 'Attribute' 
                        and 
                        name(child::ns:*[position() = 2]) = 'CoefficientType' 
                        and 
                        name(child::ns:*[position() = 3]) = 'MinimalLength' 
                        and 
                        name(child::ns:*[position() = 4]) = 'MaximalLength'
                    )
                )
                    ">
                element BasicBooleanAttributeSetting obsahuje elementy 
                Attribute, CoefficientType, MinimalLength a MaximalLength v tomto poradi
                - pokud se nejedna o koeficient typu FixedSet
            </assert>
            <assert test="
                (
                    ns:CoefficientType = 'FixedSet'
                )
                or
                (
                    (
                        ns:MinimalLength >= 0 and (ns:MinimalLength = round(ns:MinimalLength))
                    ) 
                    and
                    (
                        ns:MaximalLength >= 0 and (ns:MaximalLength = round(ns:MaximalLength))
                    ) 
                    and 
                    (
                        ns:MaximalLength >= ns:MinimalLength
                    )
                )
                ">
                elementy MinimalLength a MaximalLength obsahuji cela cisla vetsi nebo rovna 
                0, s tim, ze MinimalLength vzdy mensi nebo roven nez MaximalLength - 
            </assert>
        </rule>
        
        <rule context="ns:DerivedBooleanAttributeSettings">
            <assert test="count(ns:DerivedBooleanAttributeSetting) > 0">
                element DerivedBooleanAttributeSettings obsahuje elementy DerivedBooleanAttributeSetting
            </assert>
        </rule>
        
        <rule context="ns:DerivedBooleanAttributeSetting">
            <assert test="./@type and ./@id and ./@name">
                element DerivedBooleanAttributeSetting obsahuje atributy type, id a name
            </assert>
            <assert test="./@type = 'Sign' or ./@type = 'Conjunction' or ./@type = 'Disjunction'">
                atribut type muze mit hodnoty "Sign", "Conjunction" a "Disjunction".
            </assert>
            <!--
            <assert test="count(../../*/*/@id = @id) = 1">
                id je unikatni v ramci BasicBooleanAttributeSettings i DerivedBooleanAttributeSettings
            </assert>
            -->
            <assert test="not(./@type='Sign') or (./@type='Sign' and ./ns:BooleanAttributeId and (./ns:Type = 'Positive' or ./ns:Type = 'Negative' or ./ns:Type = 'Both'))">
                jestlize je type u DerivedBooleanAttributeSetting "Sign", potom 
                ma 1 podelement BooleanAttributeId a 1 podelement Type, ktery 
                ma pripustne hodnoty "Positive", "Negative" a "Both".
            </assert>
            
            <!-- tricky one? -->
            <assert test="(
                              not(./@type='Conjunction') 
                              and 
                              not(./@type='Disjunction')
                          ) 
                          or 
                          (
                             count(./ns:BooleanAttributeId) > 0 
                             and 
                             count(./ns:MinimalLength) = 1 
                             and 
                             count(./ns:MaximalLength) = 1 
                             and 
                             (
                                 (
                                     ns:MinimalLength >= 0 
                                     and 
                                     (ns:MinimalLength = round(ns:MinimalLength))
                                 ) 
                                 and 
                                 (
                                     ns:MaximalLength >= 0 
                                     and 
                                     (ns:MaximalLength = round(ns:MaximalLength))
                                 ) 
                                 and 
                                 (ns:MaximalLength >= ns:MinimalLength)
                             )
                          )
                         ">
                jestlize je type u DerivedBooleanAttributeSetting "Conjunction" nebo "Disjunction", obsahuje minimalne 1 podelement 
                BooleanAttributeId a po jednom elementu MinimalLength a MaximalLength - tyto opet obsahuji cele cisla 
                vetsi nebo rovna 0 a minimal musi byt mesni rovno nez maximal
            </assert>
        </rule>
        
       
        <rule context="ns:Item">
            <assert test="count(./ns:Extension) > 0">
                element Item obsahuje x elementu Extension
            </assert>
        </rule>
        
        <rule context="ns:Item/ns:Extension">
            <assert test="./@name and ./@value">
                element Extension v elementu Item obsahuje atributy name a value
            </assert>
        </rule>
        
        <rule context="ns:Itemset">
            <assert test="count(ns:Extension[@name='Connective'])">
                element Extension v elementu Itemset obsahuje Extension Connective
            </assert>        
        </rule>
        <rule context="ns:Itemset/ns:Extension[@name='Connective']">
            <assert test="@value = 'Negation' or @value = 'Conjunction' or @value = 'Disjunction'">
                Extension musi mit nastaven atribut value, ktera muze byt jedna z "Negation", "Conjunction" a
                "Disjunction".
            </assert>
        </rule>
        <rule context="ns:Itemset/ns:Extension">
            <assert test="count(ns:BooleanAttributeId) >=2 and @name='Connective'">
                Konjunkce nebo Disjunkce musi obsahovat alespon dva prvky    BooleanAttributeId           
            </assert>
            
        </rule>
        
        <!-- 
            element AssociationRule obsahuje podelementy Extensions, ktere mohou
            byt dvou tvaru:
            1. name="Condition", value="cele cislo vetsi nebo rovno -1"
            2. name="Condition", value="double cislo",
            extender="jmenokvantivikatoru, viz vise"
            3. name="4ftFrequency", value="nezaporne cele cislo",
            extender="jedna z moznosti a, b, c, d"
        -->
        <rule context="ns:AssociationRule">
            <assert test="count(./ns:Extension) > 0">
                element AssociationRule obsahuje podelementy Extension
            </assert>
        </rule>
        
        <rule context="ns:AssociationRule/ns:Extension">
            <assert test="(
                            (
                                ./@name = 'Condition' 
                                and 
                                (./@value >= -1 and (./@value = round(./@value)))
                            )
                            or 
                            (
                                ./@name = 'Quantifier' 
                                and 
                                (./@value = number(./@value))
                            )
                            or
                            (
                                ./@name = '4ftFrequency' 
                                and 
                                (./@value >= 0 and (./@value = round(./@value))) 
                                and 
                                (./@extender = 'a' or ./@extender = 'b' or ./@extender = 'c' or ./@extender = 'd')
                            )
                            or
                            (
                            ./@name = 'Text'  
                            )
                          )
                            ">
                
                podelementy Extension v elementu AssociationRule mohou byt tvaru:
                1. name="Condition", value="cele cislo vetsi nez -1"
                2. name="Quantifier", value="double cislo", extender="jmenokvantivikatoru, viz vise"
                3. name="4ftFrequency", value="nezaporne cele cislo", extender="jedna z moznosti a, b, c, d"
            </assert>
        </rule>
        
    </pattern>
    
</schema>
declare function local:mainFunction ($request as node()) as node()*{
let $request := local:processRequest($request)
let $order :=
for $x in collection("sewebar1.dbxml")
order by base-uri($x)
return $x
for $pmml in $order[position() <= 6 and position() > 2]
return
for $ar in $pmml/PMML/AssociationRule
return
let $bba_ar := $ar//BBA
let $bba_ok := local:ARSearchStep3(<cedent>{$bba_ar}</cedent>, $request)
(: !!! BBA DataDictionary zrejme chyba !!! :)
return if ($bba_ok/@trans >= count($request//BBA) or $bba_ok/@data >= count($request//BBA)) 
then
<ar>
    <text>{$ar/Text/text()}</text>
</ar>
else ()
};

declare function local:ARSearchStep3($cedent, $request){
let $request_trans := $request/BBA/Field[@dictionary = "TransformationDictionary"]
let $request_data := $request/BBA/Field[@dictionary = "DataDictionary"]
return
<foundBBA trans="{local:CategoriesTrans($cedent/BBA/CatRef, $request_trans/Category)}"/>
(: data="{local:CategoriesData($cedent/BBA/CatRef union $cedent/BBA/Interval, $request_data/Category union $request_data/Interval)}" :)
};

declare function local:CategoriesTrans($cedent_cats, $request_cats){
count(for $request_cat in $request_cats
return
for $cedent_cat in $cedent_cats
return
if ($cedent_cat/text() = $request_cat/text())
then true() else ())
};

declare function local:CategoriesData($cedent_cats, $request_cats){
count(for $request_cat in $request_cats
return
for $cedent_cat in $cedent_cats
return
if ($cedent_cat/name() = "Interval")
then if ($cedent_cat/@left = $request_cat/@left and $cedent_cat/@right = $request_cat/@right and $cedent_cat/@type = $request_cat/@closure)
    then true()
    else ()
else if ($cedent_cat/text() = $request_cat/text())
    then true()
    else ())
};

declare function local:processRequest($request as node()) {
let $generalSet := $request/ARQuery/GeneralSetting
let $attribs :=
    for $MBA in $generalSet/MandatoryPresenceConstraint/MandatoryBA/text()
    return
        for $DBA in $request//DBASetting[@id = $MBA]
        return
               local:DBAtoBBARecursion($DBA//BASettingRef, $request, "")

return
<AR_query>
    <Scope>{$generalSet/Scope/node()}</Scope>
    {$attribs}
</AR_query>
};
declare function local:getBBAs($BBAs as node()*, $request as node()) as node()*{
for $BBA in $BBAs
return
    local:BBABuild($BBA, $request//DictionaryMapping)
};
declare function local:DBAtoBBARecursion($BARefs as node()*, $request as node(), $literal as xs:string){
for $odkaz in $BARefs
let $liter := if ($literal = "") then "Both" else $literal
return 
if (count($request//BBASetting[@id = $odkaz/text()])>0) then
local:BBABuild($request//BBASetting[@id = $odkaz/text()], $request//DictionaryMapping)
else local:DBAtoBBARecursion($request//DBASetting[@id = $odkaz/text()]//BASettingRef, $request, $request//DBASetting[@id = $odkaz/text()]/LiteralSign)
};

declare function local:BBABuild($BBA as node(), $mapping) as node(){
let $dictionary := $BBA/FieldRef/@dictionary/string()
let $field := $BBA/FieldRef/text()
let $coefficient := $BBA/Coefficient
let $category :=
    for $cat in $coefficient//Category
    return 
    if ($cat/name() = "Category") 
        then <Category>{$cat/text()}</Category>
        else if ($cat/name() = "Interval")
            then <Interval closure="{$cat/@closure}" left="{$cat/@leftMargin}" right="{$cat/@rightMargin}"/> 
            else $cat

return 
<BBA id="{$BBA/@id}">
    <Field dictionary="{$dictionary}">
        <Name>{$field}</Name>
        <Type>{$coefficient/Type/text()}</Type>
        {$category}
    </Field>
    {local:DictionarySwitch($dictionary, $field, $coefficient, $mapping)}
</BBA>
};

declare function local:DictionarySwitch($dict, $field, $coeff, $mapping){
let $valueMapping := $mapping//Field[@name = $field and @dictionary = $dict]/parent::node()
let $fieldTrans := $valueMapping/Field[@dictionary != $dict]
let $category := 
    let $catTrans := $fieldTrans/child::node()
    for $everyCat in $catTrans
    return
    if ($everyCat/name() = "Value") 
        then <Category>{$everyCat/text()}</Category>
        else if ($everyCat/name() = "Interval")
            then <Interval closure="{$everyCat/@closure}" left="{$everyCat/@leftMargin}" right="{$everyCat/@rightMargin}"/> 
            else $everyCat
            
return
if (count($fieldTrans) > 0) then
<Field dictionary="{$fieldTrans[1]/@dictionary}">
    <Name>{$fieldTrans[1]/@name/string()}</Name>
    <Type>{$coeff/Type/text()}</Type>
    {$category}
</Field>
else ()
};

let $vstup := <arb:ARBuilder xmlns:arb="http://keg.vse.cz/ns/arbuilder0_1"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://keg.vse.cz/ns/arbuilder0_1 http://sewebar.vse.cz/schemas/ARBuilder0_1.xsd">
    <DataDescription xmlns:pmml="http://www.dmg.org/PMML-4_0"
        xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1">
        <Dictionary sourceSubType="TransformationDictionary" sourceType="PMML">
            <Field name="Age">
                <Category>&lt;31;41)</Category>
            </Field>
            <Field name="Sex">
                <Category>M</Category>
            </Field>
            <Field name="District">
                <Category>Havlickuv Brod</Category>
            </Field>
            <Field name="Quality">
                <Category>bad</Category>
            </Field>
        </Dictionary>
        <Dictionary sourceSubType="DataDictionary" sourceType="PMML">
            <Field name="Age">
                <Interval closure="closedOpen" leftMargin="31" rightMargin="41"/>
            </Field>
            <Field name="Sex">
                <Category>No</Category>
            </Field>
            <Field name="District">
                <Category>Havlickuv Brod</Category>
            </Field>
            <Field name="status">
                <Category>B</Category>
                <Category>D</Category>
            </Field>
        </Dictionary>
        <DictionaryMapping>
            <ValueMapping>
                <Field name="Age" dictionary="TransformationDictionary">
                    <Value>&lt;31;41)</Value>
                </Field>
                <Field name="Age" dictionary="DataDictionary">
                    <Interval closure="closedOpen" leftMargin="31" rightMargin="41"/>
                </Field>
            </ValueMapping>
            <ValueMapping>
                <Field name="Sex" dictionary="TransformationDictionary">
                    <Value>M</Value>
                </Field>
                <Field name="Sex" dictionary="DataDictionary">
                    <Value>No</Value>
                </Field>
            </ValueMapping>
            <ValueMapping>
                <Field name="District" dictionary="TransformationDictionary">
                    <Value>Havlickuv Brod</Value>
                </Field>
                <Field name="District" dictionary="DataDictionary">
                    <Value>Havlickuv Brod</Value>
                </Field>
            </ValueMapping>
            <ValueMapping>
                <Field name="Quality" dictionary="TransformationDictionary">
                    <Value>bad</Value>
                </Field>
                <Field name="status" dictionary="DataDictionary">
                    <Value>B</Value>
                </Field>
            </ValueMapping>
            <ValueMapping>
                <Field name="Quality" dictionary="TransformationDictionary">
                    <Value>bad</Value>
                </Field>
                <Field name="status" dictionary="DataDictionary">
                    <Value>D</Value>
                </Field>
            </ValueMapping>
        </DictionaryMapping>
    </DataDescription>
    <ARQuery>
        <BBASettings>
            <BBASetting id="BBA_FTLiteralI_275">
                <Text>Age(&lt;31;41))</Text>
                <FieldRef dictionary="TransformationDictionary">Age</FieldRef>
                <Coefficient>
                    <Type>At least one from listed</Type>
                    <Category>&lt;31;41)</Category>
                </Coefficient>
            </BBASetting>
            <BBASetting id="BBA_FTLiteralI_276">
                <Text>Sex(M)</Text>
                <FieldRef dictionary="TransformationDictionary">Sex</FieldRef>
                <Coefficient>
                    <Type>At least one from listed</Type>
                    <Category>M</Category>
                </Coefficient>
            </BBASetting>
            <BBASetting id="BBA_FTLiteralI_277">
                <Text>District(Havlickuv Brod)</Text>
                <FieldRef dictionary="TransformationDictionary">District</FieldRef>
                <Coefficient>
                    <Type>At least one from listed</Type>
                    <Category>Havlickuv Brod</Category>
                </Coefficient>
            </BBASetting>
            <BBASetting id="BBA_FTLiteralI_278">
                <Text>Quality(bad)</Text>
                <FieldRef dictionary="TransformationDictionary">Quality</FieldRef>
                <Coefficient>
                    <Type>At least one from listed</Type>
                    <Category>bad</Category>
                </Coefficient>
            </BBASetting>
        </BBASettings>
        <DBASettings>
            <DBASetting id="DBA_FTLiteralI_Sign_275" type="Literal">
                <BASettingRef>BBA_FTLiteralI_275</BASettingRef>
                <LiteralSign>Both</LiteralSign>
            </DBASetting>
            <DBASetting id="DBA_FTLiteralI_Sign_276" type="Literal">
                <BASettingRef>BBA_FTLiteralI_276</BASettingRef>
                <LiteralSign>Both</LiteralSign>
            </DBASetting>
            <DBASetting id="DBA_FTLiteralI_Sign_277" type="Literal">
                <BASettingRef>BBA_FTLiteralI_277</BASettingRef>
                <LiteralSign>Both</LiteralSign>
            </DBASetting>
            <DBASetting id="DBA_FTLiteralI_Sign_278" type="Literal">
                <BASettingRef>BBA_FTLiteralI_278</BASettingRef>
                <LiteralSign>Both</LiteralSign>
            </DBASetting>
        </DBASettings>
        <GeneralSetting>
            <Scope>
                <RulePart>Antecedent</RulePart>
                <RulePart>Consequent</RulePart>
                <RulePart>Condition</RulePart>
            </Scope>
            <ApplyRecursively>true</ApplyRecursively>
            <MandatoryPresenceConstraint>
                <MandatoryBA>DBA_FTLiteralI_Sign_276</MandatoryBA>
                <MandatoryBA>DBA_FTLiteralI_Sign_278</MandatoryBA>
            </MandatoryPresenceConstraint>
        </GeneralSetting>
        <InterestMeasureSetting>
            <InterestMeasureThreshold id="1">
                <InterestMeasure>Any Interest Measure</InterestMeasure>
            </InterestMeasureThreshold>
        </InterestMeasureSetting>
    </ARQuery>
</arb:ARBuilder>


let $result := local:mainFunction($vstup)
return
<XQuery_search>
    <Result>
        <FoundARs>
            <count>{count($result)}</count>
            <ARText>{$result}</ARText>
        </FoundARs>
    </Result>
</XQuery_search>

<?php defined('_JEXEC') or die('Restricted access'); ?>

<h1>KBI debug console.</h1>
<form action="<?php print $this->url?>" method="post">
	<fieldset>
		<legend>Build Query</legend>
		<label for="source">Source<span>(id or JSON)</span></label>
		<textarea id="source" name="source" rows="10" cols="80">
{
    "type": "JUCENE"
}
		</textarea>
		<label>Examples</label>
		<pre>
{
    "type": "ONTOPIA",
    "url": "http:\/\/nlp.vse.cz:8080\/tmrap\/tmrap\/get-tolog",
    "topicmap": "ItalianOpera.ltm",
    "syntax": "text\/x-tmxml"
}
		</pre>
		<pre>
{
    "type": "XQUERY",
    "url": "http:\/\/nlp.vse.cz:8081\/xquery_search\/xquery_servlet\/" 
}
		</pre>
		<pre>
{
    "type": "JUCENE",
    "url": "url nezacinajici http:// znamena lokalni instalaci jucene" 
}
		</pre>
		<label for="query">Query<span>(id or source specific language or JSON)</span></label>
		<textarea id="query" name="query" rows="10" cols="80" wrap="off">
<arb:ARBuilder xmlns:arb="http://keg.vse.cz/ns/arbuilder0_1"
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
		</textarea>
		<label for="query">Query parameters<span>(JSON)</span></label>
		<textarea id="params" name="params" rows="10" cols="80"></textarea>
		<label for="xslt">XSLT for results<span>(id or XML/XSLT)</span></label>
		<textarea id="xslt" name="xslt" rows="10" cols="80"></textarea>
		<input type="button" value="AJAX GET" onclick="return KbiGetAjax('<?php print $this->url?>');" disabled="disabled"/>
		<input type="button" value="AJAX POST" onclick="return KbiPostAjax('<?php print $this->url?>');"/>
		<input type="submit" value="POST"/>
	</fieldset>
</form>
<form action="" method="post">
	<fieldset>
		<legend>Results</legend>
		<div id="messages">&nbsp;</div>
		<textarea id="results" name="results" rows="50" cols="80" disabled="disabled"><?php print isset($this->results) ? var_dump($this->results) : '' ?></textarea>
		<input type="button" onclick="$('results').empty(); return false;" value="Reset results" />
	</fieldset>
</form>
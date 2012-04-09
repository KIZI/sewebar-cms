<?php          
require_once('sources/models/JSON.php');
require_once('sources/models/parseData/AncestorGetData.php');
require_once('sources/models/parseData/GetDataARBuilderQuery.php');
require_once('sources/models/parseData/AsociationRulesParser.php');
require_once('sources/models/parseData/ARQueryParser.php');
require_once('sources/models/parseData/TaskSettingParser.php');
require_once('sources/models/serializeRules/AncestorSerializeRules.php');
require_once('sources/models/serializeRules/SerializeRulesTaskSetting.php');
require_once('sources/models/serializeRules/SerializeRulesARQuery.php');
require_once('sources/models/Utils.php');

function encodeData($array)
{
	$data = "";
	foreach ($array as $key=>$value) $data .= "{$key}=".urlencode($value).'&';
	return $data;
}
    
if(session_id() === '') {
	session_start();
}

// ulozeni session id pro komunikace s LISpMiner-em
$ckfile = dirname(__FILE__) . "/cookie_".session_id();

// Pokus session s LISpMiner-em jeste nezacla tak posleme data pro inicializaci
if (!file_exists($ckfile)) {
	$data = array(
		'content' => file_get_contents('XML/barboraForLMImport.pmml'),
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://lmcloud.vse.cz/SewebarConnect/Import.ashx");
	curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
	curl_setopt($ch, CURLOPT_POSTFIELDS, encodeData($data));
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	
	$response = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	
	//echo "Import executed<br>";
	//var_dump($response);
}

// dotaz/task pro LISpMiner
$toSolve = isset($_POST['data']) ? $_POST['data'] : $_GET['data'];
$toSolve = str_replace("\\\"", "\"", $toSolve);
$sr = new SerializeRulesTaskSetting();
$content = $sr->serializeRules($toSolve);
$content = '<?xml version="1.0" encoding="UTF-8"?>
<?oxygen SCHSchema="http://sewebar.vse.cz/schemas/GUHARestr0_1.sch"?>
<PMML xmlns="http://www.dmg.org/PMML-4_0" version="4.0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:pmml="http://www.dmg.org/PMML-4_0"
    xsi:schemaLocation="http://www.dmg.org/PMML-4_0 http://sewebar.vse.cz/schemas/PMML4.0+GUHA0.1.xsd">
    <Header copyright="Copyright (c) KIZI UEP">
        <Extension name="dataset" value="Loans"/>
        <Extension name="author" value="admin"/>
        <Extension name="subsystem" value="4ft-Miner"/>
        <Extension name="module" value="4ftResult.exe"/>
        <Extension name="format" value="4ftMiner.Task"/>
        <Application name="SEWEBAR-CMS" version="0.00.01 05.04.2012"/>
        <Annotation/>
        <Timestamp>05.04.2012 18:56:13</Timestamp>
    </Header>
    <DataDictionary/>
    <guha:AssociationModel xmlns=""
        xsi:schemaLocation="http://keg.vse.cz/ns/GUHA0.1rev1 http://sewebar.vse.cz/schemas/GUHA0.1rev1.xsd"
        xmlns:guha="http://keg.vse.cz/ns/GUHA0.1rev1"
        modelName="c5bc5bf085126ac877fa74ce17e2ecfc968b2dce"
        functionName="associationRules" algorithmName="4ft">
        <TaskSetting>
            <Extension name="LISp-Miner">
                <HypothesesCountMax>2</HypothesesCountMax>
            </Extension>
            <Extension name="metabase" value="LM LMBarbora.mdb MB"/>
            <BBASettings>
                <BBASetting id="5">
                    <Text>District</Text>
                    <Name>District</Name>
                    <FieldRef>District</FieldRef>
                    <Coefficient>
                        <Type>Subset</Type>
                        <MinimalLength>1</MinimalLength>
                        <MaximalLength>3</MaximalLength>
                    </Coefficient>
                </BBASetting>
                <BBASetting id="9">
                    <Text>Quality</Text>
                    <Name>Quality</Name>
                    <FieldRef>Quality</FieldRef>
                    <Coefficient>
                        <Type>One category</Type>
                        <Category>bad</Category>
                    </Coefficient>
                </BBASetting>
            </BBASettings>
            <DBASettings>
                <DBASetting type="Literal" id="4">
                    <BASettingRef>5</BASettingRef>
                    <LiteralSign>Positive</LiteralSign>
                </DBASetting>
                <DBASetting type="Conjunction" id="3">
                    <BASettingRef>4</BASettingRef>
                    <MinimalLength>1</MinimalLength>
                </DBASetting>
                <DBASetting type="Conjunction" id="2">
                    <BASettingRef>3</BASettingRef>
                    <MinimalLength>1</MinimalLength>
                </DBASetting>
                <DBASetting type="Literal" id="8">
                    <BASettingRef>9</BASettingRef>
                    <LiteralSign>Positive</LiteralSign>
                </DBASetting>
                <DBASetting type="Conjunction" id="7">
                    <BASettingRef>8</BASettingRef>
                    <MinimalLength>1</MinimalLength>
                </DBASetting>
                <DBASetting type="Conjunction" id="6">
                    <BASettingRef>7</BASettingRef>
                    <MinimalLength>1</MinimalLength>
                </DBASetting>
            </DBASettings>
            <AntecedentSetting>2</AntecedentSetting>
            <ConsequentSetting>6</ConsequentSetting>
            <InterestMeasureSetting>
                <InterestMeasureThreshold id="1">
                    <InterestMeasure>Confidence</InterestMeasure>
                    <Threshold>0.5</Threshold>
                    <CompareType>Greater than or equal</CompareType>
                </InterestMeasureThreshold>
            </InterestMeasureSetting>
        </TaskSetting>
        <AssociationRules/>
    </guha:AssociationModel>
</PMML>';
$data = array(
  'content' => $content,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://lmcloud.vse.cz/SewebarConnect/Task.ashx");
curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
curl_setopt($ch, CURLOPT_POSTFIELDS, encodeData($data));
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

// ziskani vysledku tasku z LISpMiner-a
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$DD = null;
$FL = null;
$ER = $response;

$sr = new GetDataARBuilderQuery($DD, $FL, $ER, 'en');
echo $sr->getData();
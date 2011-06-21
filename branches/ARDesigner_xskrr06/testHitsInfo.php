<?php
require_once('sources/models/JSON.php');
require_once('sources/models/parseData/AncestorGetData.php');
require_once('sources/models/parseData/GetDataARBuilderQuery.php');
require_once('sources/models/parseData/AsociationRulesParser.php');
require_once('sources/models/parseData/ARQueryParser.php');
require_once('sources/models/parseData/TaskSettingParser.php');
require_once('sources/models/serializeRules/AncestorSerializeRules.php');
require_once('sources/models/serializeRules/SerializeRulesTaskSetting.php');
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
if(!file_exists($ckfile)) {
	$data = array(
		'content' => file_get_contents('XML/barboraForLMImport.pmml'),
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://146.102.66.141/SewebarConnect/Import.ashx");
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

$data = array(
  'content' => $content,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://146.102.66.141/SewebarConnect/Task.ashx");
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


?>

<?php

require_once('../../../../sources/models/JSON.php');
require_once('../../../../sources/models/parseData/AncestorGetData.php');
require_once('../../../../sources/models/parseData/GetDataARBuilderQuery.php');
require_once('../../../../sources/models/parseData/AsociationRulesParser.php');
require_once('../../../../sources/models/parseData/ARQueryParser.php');
require_once('../../../../sources/models/parseData/TaskSettingParser.php');
require_once('../../../../sources/models/Utils.php');
require_once('../../../../sources/models/CompareXml.php');

$DD = "dataDescription.xml";
$FL = "BackgroundARs-FeatureList.xml";
$ER = "BackgroundARs-DesiredARBuilderOutput.xml";

$sr = new GetDataARBuilderQuery($DD, $FL, $ER, 'en');
$json = $sr->getData();
print("<textarea rows='10' cols='100'>$json</textarea>");


require_once('../../../../sources/models/serializeRules/AncestorSerializeRules.php');
require_once('../../../../sources/models/serializeRules/SerializeRulesBackgroundAssociationRules.php');

$sr = new SerializeRulesBackgroundAssociationRules();
$xmlFileFinal = $sr->serializeRules($json);
print("<textarea rows='10' cols='100'>$xmlFileFinal</textarea>");

libxml_use_internal_errors(true);

$correctXML = true;
/* creating a DomDocument object */
$objDom = new DomDocument();

/* loading the xml data */
$objDom->loadXML($xmlFileFinal);
/* tries to validade your data */
if (!$objDom->schemaValidate("../../../../XML/schemas/ARBuilder0_1.xsd")) {
    /* if anything goes wrong you can get all errors at once */
    $allErrors = libxml_get_errors();
    /* each element of the array $allErrors will be a LibXmlError Object */
    print_r($allErrors);
    print "<h2>Validation was incorrect</h2>";
} else {
    print "<h2>Validation was finished correctly</h2>";
}
/*
require_once("../../../../lib/Schematron.php");


$s = new Schematron();

//$fp=fopen("validation_sample1.xml","r");
$fp = fopen("ARQuery_check.sch", "r");
$uncompiled = fread($fp, filesize("ARQuery_check.sch"));
fclose($fp);

//$fp=fopen("sample1.xml","r");
//$fp = fopen("ARQuery-Sample.xml", "r");
//$xml = fread($fp, filesize("ARQuery-Sample.xml"));
//fclose($fp);

// Uncompiled tests 
print("<h3>Schematron Test</h3>");

// MEM USING MEM
$ret = $s->schematronValidate($xmlFileFinal, $uncompiled);
print("<textarea rows='10' cols='100'>$ret</textarea>");
*/

$filePath = "BackgroundARs-DesiredARBuilderOutput.xml";
$file = fopen($filePath, "r");
$expectedResult = fread($file, filesize($filePath));

if(areXmlSame($xmlFileFinal, $expectedResult)){
    print "<h2>XML file is as expected</h2>";
}
else{
    print "<h2>XML file is NOT as expected</h2>";
}

?>

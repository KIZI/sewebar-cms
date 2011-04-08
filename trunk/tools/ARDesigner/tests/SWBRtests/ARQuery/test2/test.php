<?php

require_once('../../../../sources/models/JSON.php');
require_once('../../../../sources/models/parseData/AncestorGetData.php');
require_once('../../../../sources/models/parseData/GetDataARBuilderQuery.php');
require_once('../../../../sources/models/parseData/AsociationRulesParser.php');
require_once('../../../../sources/models/parseData/ARQueryParser.php');
require_once('../../../../sources/models/parseData/TaskSettingParser.php');
require_once('../../../../sources/models/Utils.php');

$DD = "dataDescription.xml";
$FL = "ARQuery-FeatureList.xml";
//$FL = "XML/featurelist1.xml";
//$ER = "XML/taskSetting.xml";
//$ER = "XML/associationRules.xml";
//$ER = "XML/arQuery.xml";
//$ER = "testSelenium/test30Rules/associationRules.xml";
//$ER = "testSelenium/test30ElementsRule/associationRules.xml";
$ER = "ARQuery-Sample.xml";

//$DD = "testSelenium/testSpeed200attributes/datadescription.xml";
//$FL = "testSelenium/testSpeed200attributes/featurelist.xml";
//$ER = "testSelenium/testSpeed200attributes/associationRules.xml";

$sr = new GetDataARBuilderQuery($DD, $FL, $ER, 'en');
$json = $sr->getData();
print("<textarea rows='10' cols='100'>$json</textarea>");


require_once('../../../../sources/models/serializeRules/AncestorSerializeRules.php');
require_once('../../../../sources/models/serializeRules/SerializeRulesARQuery.php');

//session_start();
//$_SESSION["ARBuilder_domDataDescr"] = "datadescription.xml";

//$json = "{\"rule0\":[{\"name\":\"status\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\"Support\",\"type\":\"oper\",\"fields\":[{\"name\":\"min value\",\"value\":\"\"}]},{\"name\":\"Confidence\",\"type\":\"oper\",\"fields\":[{\"name\":\"min value\",\"value\":\"\"}]},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]}],\"rules\":1}";

$sr = new SerializeRulesARQuery();
$xmlFileFinal = $sr->serializeRules($json);
print("<textarea rows='10' cols='100'>$xmlFileFinal</textarea>");

libxml_use_internal_errors(true);

$correctXML = true;
/* creating a DomDocument object */
$objDom = new DomDocument();

/* loading the xml data */
$objDom->loadXML($xmlFileFinal);
/* tries to validade your data */
if (!$objDom->schemaValidate("../../../../XML/ARBuilder0_1.xsd")) {
    /* if anything goes wrong you can get all errors at once */
    $allErrors = libxml_get_errors();
    /* each element of the array $allErrors will be a LibXmlError Object */
    print_r($allErrors);
    print "<h2>Validation was incorrect</h2>";
} else {
    print "<h2>Validation was finished correctly</h2>";
}

require_once("../../../../sources/models/Schematron.php");

$s = new Schematron();

//$fp=fopen("validation_sample1.xml","r");
$fp = fopen("ARQuery_check.sch", "r");
$uncompiled = fread($fp, filesize("ARQuery_check.sch"));
fclose($fp);

//$fp=fopen("sample1.xml","r");
//$fp = fopen("ARQuery-Sample.xml", "r");
//$xml = fread($fp, filesize("ARQuery-Sample.xml"));
//fclose($fp);

/* Uncompiled tests */
print("<h3>Schematron Test</h3>");

/* MEM USING MEM */
$ret = $s->schematronValidate($xmlFileFinal, $uncompiled);
print("<textarea rows='10' cols='100'>$ret</textarea>");
?>

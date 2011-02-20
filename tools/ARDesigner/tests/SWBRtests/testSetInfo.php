<?php

require_once('../../serializeRules/AncestorSerializeRules.php');
require_once('../../serializeRules/SerializeRulesARQuery.php');

session_start();
$_SESSION["ARBuilder_domDataDescr"] = "datadescription.xml";

$json = "{\"rule0\":[{\"name\":\"status\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\"Support\",\"type\":\"oper\",\"fields\":[{\"name\":\"min value\",\"value\":\"\"}]},{\"name\":\"Confidence\",\"type\":\"oper\",\"fields\":[{\"name\":\"min value\",\"value\":\"\"}]},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]}],\"rules\":1}";

$sr = new SerializeRulesARQuery();
$xmlFileFinal = $sr->serializeRules($json);

libxml_use_internal_errors(true);

$correctXML = true;
/* creating a DomDocument object */
$objDom = new DomDocument();

/* loading the xml data */
$objDom->loadXML($xmlFileFinal);
/* tries to validade your data */
if (!$objDom->schemaValidate("../../XML/ARBuilder0_1.xsd")) {
    /* if anything goes wrong you can get all errors at once */
    $allErrors = libxml_get_errors();
    /* each element of the array $allErrors will be a LibXmlError Object */
    print_r($allErrors);
    $correctXML = false;
} else {
    $correctXML = true;
}

require_once("../../lib/Schematron.php");

$s = new Schematron();
print("<h1>Schematron Test</h1>");

//$fp=fopen("validation_sample1.xml","r");
$fp = fopen("ARQuery_check.sch", "r");
$uncompiled = fread($fp, filesize("ARQuery_check.sch"));
fclose($fp);

//$fp=fopen("sample1.xml","r");
//$fp = fopen("ARQuery-Sample.xml", "r");
//$xml = fread($fp, filesize("ARQuery-Sample.xml"));
//fclose($fp);

/* Uncompiled tests */
print("<h3>Testing Schematron for uncompiled scripts</h3>");

/* MEM USING MEM */
$ret = $s->schematronValidate($xmlFileFinal, $uncompiled);
print("<textarea rows='10' cols='20'>$ret</textarea>");
?>

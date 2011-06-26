<?php                                                                  

require_once('../../sources/models/serializeRules/AncestorSerializeRules.php');
require_once('../../sources/models/serializeRules/SerializeRulesARQuery.php');
require_once('../../sources/models/CompareXml.php');

session_start();
$_SESSION["ARBuilder_domDataDescr"] = "datadescription.xml";

$json = "{\"rule0\":[{\"name\":\"status\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"Interval\",\"value\":\"\"}]},{\"name\":\"Support\",\"type\":\"oper\",\"fields\":[{\"name\":\"Subset\",\"value\":\"\"}]},{\"name\":\"Confidence\",\"type\":\"oper\",\"fields\":[{\"name\":\"Subset\",\"value\":\"\"}]},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"Interval\",\"value\":\"\"}]}],\"rules\":1}";

$sr = new SerializeRulesARQuery();
$xmlFileFinal = $sr->serializeRules($json);

libxml_use_internal_errors(true);

$correctXML = true;
$objDom = new DomDocument();

$objDom->loadXML($xmlFileFinal);
if (!$objDom->schemaValidate("../../XML/schemas/ARBuilder0_1.xsd")) {
    $allErrors = libxml_get_errors();
    print "<h2>XML file is not correct</h2>";
    print_r($allErrors);
} else {
    print "<h2>XML file is correct</h2>";
}
$ret = $xmlFileFinal;
print("<textarea rows='10' cols='20'>$ret</textarea>");

$filePath = "resultARQuery.xml";
$file = fopen($filePath, "r");
$expectedResult = fread($file, filesize($filePath));

if(areXmlSame($xmlFileFinal, $expectedResult)){
    print "<h2>XML file is as expected</h2>";
}
else{
    print "<h2>XML file is NOT as expected</h2>";
}

?>

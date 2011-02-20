<?php
require_once('../../serializeRules/AncestorSerializeRules.php');
require_once('../../serializeRules/SerializeRulesARQuery.php');

session_start();
$_SESSION["ARBuilder_domDataDescr"] = "../../XML/datadescription.xml";

$json = "{\"rule0\":[{\"name\":\"(\",\"type\":\"lbrac\"},{\"name\":\"(\",\"type\":\"lbrac\"},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\"AND\",\"type\":\"and\"},{\"name\":\"district\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\")\",\"type\":\"rbrac\"},{\"name\":\"AND\",\"type\":\"and\"},{\"name\":\"(\",\"type\":\"lbrac\"},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\"AND\",\"type\":\"and\"},{\"name\":\"district\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\")\",\"type\":\"rbrac\"},{\"name\":\")\",\"type\":\"rbrac\"},{\"name\":\"Support\",\"type\":\"oper\",\"fields\":[{\"name\":\"min value\",\"value\":\"\"}]},{\"name\":\"(\",\"type\":\"lbrac\"},{\"name\":\"(\",\"type\":\"lbrac\"},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\"AND\",\"type\":\"and\"},{\"name\":\"district\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\")\",\"type\":\"rbrac\"},{\"name\":\"AND\",\"type\":\"and\"},{\"name\":\"(\",\"type\":\"lbrac\"},{\"name\":\"duration\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\"AND\",\"type\":\"and\"},{\"name\":\"district\",\"type\":\"attr\",\"category\":\"Interval\",\"fields\":[{\"name\":\"maxLength\",\"value\":\"\"}]},{\"name\":\")\",\"type\":\"rbrac\"},{\"name\":\")\",\"type\":\"rbrac\"}],\"rules\":1}";

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
    print "<h2>XML file is not correct</h2>";
    print_r($allErrors);
} else {
    print "<h2>XML file is correct</h2>";
}
$ret = $xmlFileFinal;
print("<textarea rows='10' cols='20'>$ret</textarea>");
?>

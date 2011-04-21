<?php
    require_once('parseData/AncestorGetData.php');
    require_once('parseData/GetDataARBuilderQuery.php');
    require_once('parseData/AsociationRulesParser.php');
    require_once('parseData/ARQueryParser.php');
    require_once('lib/Utils.php');
    
    $DD = "testSelenium/testSpeed200attributes/datadescription.xml";
    $FL = "testSelenium/testSpeed200attributes/featurelist.xml";
    $ER = "testSelenium/testSpeed200attributes/associationRules.xml";
    
    $sr = new GetDataARBuilderQuery($DD,$FL,$ER,'en');
    echo $sr->getData();
?>
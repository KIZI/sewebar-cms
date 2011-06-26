<?php                  
    require_once('parseData/AncestorGetData.php');
    require_once('parseData/GetDataARBuilderQuery.php');
    require_once('parseData/AsociationRulesParser.php');
    require_once('parseData/ARQueryParser.php');
    require_once('lib/Utils.php');
    
    $DD = "XML/datadescription.xml";
    $FL = "XML/featurelist.xml";
    //$ER = "XML/taskSetting.xml";
    //$ER = "XML/associationRules.xml";
    //$ER = "XML/arQuery.xml";
    $ER = "testSelenium/test30Rules/associationRules.xml";

    //$DD = "testSelenium/testSpeed200attributes/datadescription.xml";
    //$FL = "testSelenium/testSpeed200attributes/featurelist.xml";
    //$ER = "testSelenium/testSpeed200attributes/associationRules.xml";
    
    $sr = new GetDataARBuilderQuery($DD,$FL,$ER,'en');
    echo $sr->getData();
?>
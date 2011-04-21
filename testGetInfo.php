<?php
    require_once('sources/models/JSON.php');
    require_once('sources/models/parseData/AncestorGetData.php');
    require_once('sources/models/parseData/GetDataARBuilderQuery.php');
    require_once('sources/models/parseData/AsociationRulesParser.php');
    require_once('sources/models/parseData/ARQueryParser.php');
    require_once('sources/models/parseData/TaskSettingParser.php');
    require_once('sources/models/Utils.php');
    
    $DD = "XML/datadescription.xml";
    $FL = "XML/featurelistQueryByAr.xml";
    $ER = null;
    //$FL = "XML/featurelist1.xml";
    //$ER = "XML/taskSetting.xml";
    //$ER = "XML/associationRules.xml";
    //$ER = "XML/arQuery.xml";
    //$ER = "testSelenium/test30Rules/associationRules.xml";
    //$ER = "testSelenium/test30ElementsRule/associationRules.xml";
    
    //$DD = "testSelenium/testSpeed200attributes/datadescription.xml";
    //$FL = "testSelenium/testSpeed200attributes/featurelist.xml";
    //$ER = "testSelenium/testSpeed200attributes/associationRules.xml";
    
    $sr = new GetDataARBuilderQuery($DD,$FL,$ER,'en');
    echo $sr->getData();
?>

<?php              
    require_once('../../../lib/Utils.php');
    require_once('../../../lib/JSON.php');
    require_once('../../../parseData/AncestorGetData.php');
    require_once('../../../parseData/GetDataARBuilderQuery.php');
    require_once('../../../parseData/AsociationRulesParser.php');
    require_once('../../../parseData/ARQueryParser.php');
    require_once('../../../parseData/TaskSettingParser.php');
    
    $DD = "datadescription.xml";
    $FL = "featurelist7.xml";

    $sr = new GetDataARBuilderQuery($DD,$FL,null,'en');
    echo $sr->getData();
?>
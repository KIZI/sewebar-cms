<?php
    require_once('sources/models/JSON.php');
    require_once('sources/models/parseData/AncestorGetData.php');
    require_once('sources/models/parseData/GetDataARBuilderQuery.php');
    require_once('sources/models/parseData/AsociationRulesParser.php');
    require_once('sources/models/parseData/ARQueryParser.php');
    require_once('sources/models/parseData/TaskSettingParser.php');
    require_once('sources/models/Utils.php');
    
    sleep(2);
    
    $DD = null;
    $FL = null;
    $ER = "XML/hitslist.xml";
    
    $sr = new GetDataARBuilderQuery($DD, $FL, $ER, 'en');
    echo $sr->getData();
?>
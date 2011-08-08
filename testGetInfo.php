<?php    
    require_once('sources/models/JSON.php');
    require_once('sources/models/parseData/AncestorGetData.php');
    require_once('sources/models/parseData/GetDataARBuilderQuery.php');
    require_once('sources/models/parseData/AsociationRulesParser.php');
    require_once('sources/models/parseData/ARQueryParser.php');
    require_once('sources/models/parseData/TaskSettingParser.php');
    require_once('sources/models/Utils.php');
    
    // standard setting
    $DD = "XML/datadescription_0.2.xml";
    //$FL = "XML/featurelistQueryByAr_loose.xml";
    $FL = "XML/featurelistQueryByAr.xml";
    $ER = null;
    
    /*
    // BKEF patterns
    $DD = "XML/bkef/datadescription.xml";
    $DD = "XML/bkef/barbora_BKEF2FDML0_2_updated.xml";
    $FL = "XML/bkef/featurelist.xml";
    $ER = null;
    $ER = "XML/bkef/barbora_bkef11_patterns.xml"; 
    */
    
    $sr = new GetDataARBuilderQuery($DD, $FL, $ER, 'en');
    echo $sr->getData();
?>

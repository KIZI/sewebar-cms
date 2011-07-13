<?php             
    require_once('sources/models/JSON.php');
    require_once('sources/models/parseData/AncestorGetData.php');
    require_once('sources/models/parseData/GetDataARBuilderQuery.php');
    require_once('sources/models/parseData/AsociationRulesParser.php');
    require_once('sources/models/parseData/ARQueryParser.php');
    require_once('sources/models/parseData/TaskSettingParser.php');
    require_once('sources/models/Utils.php');
    
    $DD = null;
    $FL = null;
    
    // init states
    $finishedStates = Array('Solved', 'Interrupted');
    $inProgressStates = Array('Waiting', 'Running', 'Not Generated', 'Interrupted');
    $states = array_merge($finishedStates, $inProgressStates);
    
    // select random hitlist
    //sleep(3);
    $randNum = rand(0, count($states) - 1);
    $ER = "XML/hitlist_".strtolower(strtr($states[$randNum], " ", "_")).".xml";
    
    $sr = new GetDataARBuilderQuery($DD, $FL, $ER, 'en');
    echo $sr->getData();
?>


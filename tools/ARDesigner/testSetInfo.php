<?php
    require_once('sources/models/serializeRules/AncestorSerializeRules.php');
    require_once('sources/models/serializeRules/SerializeRulesBackgroundAssociationRules.php');
    require_once('sources/models/serializeRules/SerializeRulesARQuery.php');
    require_once('sources/models/serializeRules/SerializeRulesTaskSetting.php');
    require_once('sources/models/serializeRules/SerializeRulesQueryByAR.php');

    session_start();
    
    $toSolve = $_POST['data'];
    
    //$sr = new SerializeRulesBackgroundAssociationRules();
    //$sr = new SerializeRulesTaskSetting();
    //$sr = new SerializeRulesARQuery();
    $sr = new SerializeRulesQueryByAR();
    echo $sr->serializeRules($toSolve);
?>

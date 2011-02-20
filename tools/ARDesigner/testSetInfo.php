<?php
    require_once('serializeRules/AncestorSerializeRules.php');
    require_once('serializeRules/SerializeRulesBackgroundAssociationRules.php');
    //require_once('serializeRules/SerializeRulesARQuery.php');
    //require_once('serializeRules/SerializeRulesTaskSetting.php');

    session_start();
    
    $toSolve = $_POST['data'];
    
    $sr = new SerializeRulesBackgroundAssociationRules();
    //$sr = new SerializeRulesTaskSetting();
    //$sr = new SerializeRulesARQuery();
    echo $sr->serializeRules($toSolve);
?>

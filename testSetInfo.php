<?php
    require_once('sources/models/JSON.php');
    require_once('sources/models/serializeRules/AncestorSerializeRules.php');
    require_once('sources/models/serializeRules/SerializeRulesBackgroundAssociationRules.php');
    require_once('sources/models/serializeRules/SerializeRulesARQuery.php');
    require_once('sources/models/serializeRules/SerializeRulesTaskSetting.php');
    require_once('sources/models/serializeRules/SerializeRulesQueryByAR.php');
    require_once('sources/models/Utils.php');

    session_start();
    
    $toSolve = isset($_POST['data']) ? $_POST['data'] : $_GET['data'];
    //$toSolve = $_GET['data'];
    $toSolve = str_replace("\\\"", "\"", $toSolve);
    //echo $toSolve;

    //$sr = new SerializeRulesBackgroundAssociationRules();
    //$sr = new SerializeRulesARQuery();
    //$sr = new SerializeRulesQueryByAR();
    $sr = new SerializeRulesTaskSetting();
    echo $sr->serializeRules($toSolve);
?>

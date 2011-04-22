<?php
function areXmlSame($xmlString1, $xmlString2){
    $document1 = new DomDocument();
    $document2 = new DomDocument();
    $document1->loadXML($xmlString1);
    $document2->loadXML($xmlString2);
    $allNodes1 = $document1->getElementsByTagName("*");
    $allNodes2 = $document2->getElementsByTagName("*");
    $length1 = $allNodes1->length;
    $length2 = $allNodes2->length;

    if($length1 != $length2){
        return false;
    }
    for($i = 0; $i < $length1; $i++){
        $node1 = $allNodes1->item($i);
        $node2 = $allNodes2->item($i);
        if(!compareAttributes($node1, $node2)){
            return false;
        }
        if($node1->nodeName != $node2->nodeName){
            return false;
        }
    }
    return true;
}

function compareAttributes($node1, $node2){
    $attrs1 = $node1->attributes;
    $attrs2 = $node2->attributes;
    $names1 = array();
    $names2 = array();

    foreach($attrs1 as $key => $value){
        $names1[] = $value;
        $names2[] = $value;
    }

    $length1 = count($names1);
    $length2 = count($names2);

    if($length1 != $length2){
        return false;
    }

    for($j = 0; $j < $length1; $j++){
        if($names1[$j] != $names2[$j]){
            return false;
        }
    }

    return true;
}

?>

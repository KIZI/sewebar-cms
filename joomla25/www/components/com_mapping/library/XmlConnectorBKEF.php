<?php

class XmlConnectorBKEF{
  /**
   *  Funkce pro načtení dat v BKEF, vrací data v XML uniformátu
   */     
  public static function transformXML($xmlString){            
    $xml=simplexml_load_string($xmlString);
    if (!$xml){return false;}
    $xml2=simplexml_load_string('<uniData><sourceType>BKEF</sourceType></uniData>');
    if (count($xml->MetaAttributes[0]->MetaAttribute)>0){
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $metaAttribute) {
        if ($metaAttribute['level']!=0){
          continue;
        }
    	  if (count($metaAttribute->Formats[0]->Format)>0)
    	    foreach ($metaAttribute->Formats[0]->Format as $format) {
         	  $col=$xml2->addChild('col');
         	  $col->addChild('name',(string)$metaAttribute->Name);
         	  $col->addChild('formatName',(string)$format->Name);
         	  $col->addChild('dataType',strtolower((string)($format->DataType)));
             
            //TODO - podpora nového formátu BKEFu!!!
            if ((strtolower($format->Range['type'])=="enumeration")&&(@count($format->Range->Value)>0)){
              //jde o výčet hodnot
              $enumeration=$col->addChild('enumeration');
              foreach ($format->Range->Value as $value) {
              	$enumeration->addChild('value',(string)$value);
              }
            }elseif((strtolower($format->Range['type'])=="interval")&&(@count($format->Range->Interval)>0)){
              $intervals=$col->addChild('intervals');
              foreach ($format->Range->Interval as $origInterval) { //exit(var_dump($origInterval));
              	$interval=$intervals->addChild('interval');
                if (substr((string)$origInterval['closure'],0,4=="open")){
                  $leftBoundType="open";
                  $rightBountType=strtolower(substr((string)$origInterval['closure'],4));
                }else{
                  $leftBoundType="closed";
                  $rightBoundType=strtolower(substr((string)$origInterval['closure'],6));
                }                  
                $interval->addAttribute('lbound',$leftBoundType);
                $interval->addAttribute('lvalue',(string)$origInterval['leftMargin']);
                $interval->addAttribute('rbound',$rightBoundType);
                $interval->addAttribute('rvalue',(string)$origInterval['rightMargin']); 
              }
            }
          }
      }
      unset($xml);
      //exit($xml2->asXML());
      return $xml2->asXML();
    }else{
      return false;
    }
  }

}

?>

<?php

class XmlConnectorTASK{
  /**
   *  Funkce pro načtení dat v BKEF, vrací data v XML uniformátu
   */     
  public static function transformXML($xmlString){            
    $xml=simplexml_load_string($xmlString);
    if (!$xml){return false;}
    $xml2=simplexml_load_string('<uniData><sourceType>PMML</sourceType></uniData>');
    if (count($xml->col)>0){
      foreach ($xml->col as $col){
        $col2=$xml2->addChild('col');
      	$col2->addChild('name',(string)$col->name);
      	$col2->addChild('dataType',strtolower((string)$col->type));
        
        //zkopirujeme statistiky
       	if (isset($col->statistics)){
          $statistics2=$col2->addChild('statistics');
          if (isset($col->statistics[0]->min)){$statistics2->addAttribute('min',(string)$col->statistics[0]->min);}
          if (isset($col->statistics[0]->max)){$statistics2->addAttribute('max',(string)$col->statistics[0]->max);}
          if (isset($col->statistics[0]->avg)){$statistics2->addAttribute('avg',(string)$col->statistics[0]->avg);}
        }
        //zkopirujeme jednotlive hodnoty
        
        if (count(@$col->items->item)>0){ 
          //podle datoveho typu si vytvorime dany typ vyctu
          if ((string)$col2->dataType=="string"){
            $enumeration=$col2->addChild('enumeration');
          }else{
            $enumeration=$col2->addChild('numberEnumeration');
          }
          //vylistujeme jednotlive hodnoty
          foreach ($col->items->item as $item) {
          	$item2=$enumeration->addChild('value',(string)$item);
          	$item2->addAttribute('count',(integer)$item['count']);
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

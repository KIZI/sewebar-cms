<?php

class XmlConnectorPMML{
  /**
   *  Funkce pro načtení dat v PMML, vrací data v XML uniformátu
   */     
  public static function transformXML($xmlString){       
    $xml=simplexml_load_string($xmlString);       
    if (!$xml){return false;}
    $xml2=simplexml_load_string('<uniData><sourceType>PMML</sourceType></uniData>');
    if (count($xml->DataDictionary[0]->DataField)>0){
      foreach ($xml->DataDictionary[0]->DataField as $dataField) {
      	$col=$xml2->addChild('col');
      	$colName=(string)$dataField['name'];
      	$col->addChild('name',$colName);
      	if (isset($dataField['dataType'])){
          $col->addChild('dataType',strtolower((string)($dataField['dataType'])));  
        }
        if (isset($dataField['optype'])){
          $col->addChild('opType',strtolower((string)($dataField['optype'])));  
        }
        /*statisticke Extension*/
        if (count($dataField->Extension)>0){
          $statistics=null;
          foreach ($dataField as $extension) {
          	if ($extension['name']=="Min"){
              if (!isset($statistics)){$col->addChild('statistics');$statistics=$col->statistics;}
              $statistics->addAttribute('min',$extension['value']);
            }elseif($extension['name']=="Max"){
              if (!isset($statistics)){$col->addChild('statistics');$statistics=$col->statistics;}
              $statistics->addAttribute('max',$extension['value']);
            }elseif($extension['name']=='Avg'){
              if (!isset($statistics)){$col->addChild('statistics');$statistics=$col->statistics;}
              $statistics->addAttribute('avg',$extension['value']);
            }
          }
        }
        /*ZJIŠTĚNÍ HODNOT DANÉHO SLOUPCE - DATA DICTIONARY*/
        if (count($dataField->Value)>0){
          if ((@(string)$dataField['optype']=="categorial")||(@(string)$dataField['optype']=="categorical")){
            //kategorialni vycet hodnot
            $enumeration=$col->addChild('enumeration');      
          }elseif(@(string)$dataField['optype']=='continuous'){
            //spojite hodnoty
            $enumeration=$col->addChild('numberEnumeration');
          } //TODOTODO
          if (isset($enumeration)){
            foreach ($dataField->Value as $value) {
            	$enum=$enumeration->addChild('value',$value['value']);  
            	if (count($value->Extension)>0)
            	  foreach ($value->Extension as $ext) {
              	  if($ext['name']=="Frequency"){
                    $enum->addAttribute("frequency",$ext['value']);
                  }
                }
            }
          }
        }else{ 
          /*TRANSFORMATION DICTIONARY*/   
          if (count($xml->TransformationDictionary[0]->DerivedField)>0){
            foreach ($xml->TransformationDictionary[0]->DerivedField as $derivedField) {
              //TODO - overit, jestli je to spravne...        
              if (((string)$derivedField["name"]!=$colName)&&((string)$derivedField->MapValues[0]->FieldColumnPair[0]["column"]!=$colName)){
                continue;
              }
            	//výčtové tabulky
              if (count(@$derivedField->MapValues[0]->InlineTable[0]->row)>0){
                $enumeration=$col->addChild('enumeration');
                foreach ($derivedField->MapValues[0]->InlineTable[0]->row as $derivedRow) {
                	$enumeration->addChild('value',$derivedRow->column);
                }
              }
              //intervaly
              if (count($derivedField->Discretize[0]->DiscretizeBin)>0){
                $intervals=$col->addChild('intervals');
                foreach ($derivedField->Discretize[0]->DiscretizeBin as $discretizeBin) {
                	if (count($discretizeBin->Interval)==1){
                    $interval=$intervals->addChild('interval');
                    $origInt=$discretizeBin->Interval[0];
                    if ($origInt['closure']=='closedOpen'){
                      $lBound='closed';
                      $rBound='open';
                    }elseif($origInt['closure']=='openClosed'){
                      $lBound='open';
                      $rBound='closed';
                    }elseif($origInt['closure']=='closedClosed'){
                      $lBound='closed';
                      $rBound='closed';
                    }else{
                      $lBound='open';
                      $rBound='open';
                    }
                    $interval->addAttribute('lbound',$lBound);
                    $interval->addAttribute('lvalue',(string)$discretizeBin->Interval[0]['leftMargin']);
                    $interval->addAttribute('rbound',$rBound);
                    $interval->addAttribute('rvalue',(string)$discretizeBin->Interval[0]['rightMargin']);
                    if (count($discretizeBin->Extension)>0){
                      //zkusime zjistit statistiky
                      foreach ($discretizeBin->Extension as $ext) {
                      	if ($ext['name']=="Frequency"){
                          $interval->addAttribute('frequency',(string)$ext['value']);
                        }
                      }
                    }
                  }
                }
              }
            }
          }  
          /**/
        }
                     
      }           
      return $xml2->asXML();
    }else{
      return false;
    }
  }

}

?>

<?php
/**********************************************************************************************************************************************/
class XmlConnector
{ // BEGIN class xmlConnector
	// variables

	/**
	 *   Funkce pro načtení XML dat z výstupu porovnávacího algoritmu a uložení do SESSION
	 *   @param $xmlString - XML dokument...
	 *   @param $workData - instance třídy workData	 
	 *   @return boolean   	 
	 */   	
	public static function loadMatchingXML($xmlString){           
	  //require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
	  $workData=new WorkDataModel();
     @$xml=simplexml_load_string($xmlString);
    if ($xml){                      
      //klíče dokumentu A
      $keysArr=array();
      $legendArr=array();
      $valuesArr=array();
      $legendArr_A=array();
      $legendArr_B=array();
      $index=0;
      if (count($xml->keysA->key)>0)
        foreach ($xml->keysA->key as $key) {
          //projdeme všechny klíče
          $keyName=(string)$key["name"];
        	$keysArr[]='a'.$index;
        	$legendArr['a'.$index]=$keyName;
        	$legendArr_A[$keyName]='a'.$index;
        	/*hodnoty*/
        	if (count(@$key->enumeration[0]->value)>0){
            foreach ($key->enumeration[0]->value as $value) {
              $valuesArr['a'.$index][]=(string)$value;	
            }
          } //TODO ostatni datove typy
        	/*--hodnoty*/
          $index++;
        }
      //$_SESSION["keysArr"]=$keysArr;
      $workData->save('keysArr',$keysArr);
      //klíče dokumentu B
      $keys2Arr=array();
      $index=0;
      if (count($xml->keysB->key)>0)
        foreach ($xml->keysB->key as $key) {
        	//projdeme všechny klíče
        	$keyName=(string)$key["name"];
        	$keys2Arr[]='b'.$index;
        	$legendArr['b'.$index]=$keyName;
        	$legendArr_B[$keyName]='b'.$index;
        	/*hodnoty*/
        	if (count(@$key->enumeration[0]->value)>0){
            foreach ($key->enumeration[0]->value as $value) {
              $valuesArr['b'.$index][]=(string)$value;	
            }
          } //TODO ostatni datove typy
        	/*--hodnoty*/
        	$index++;
        }
      $workData->save('keys2Arr',$keys2Arr);
      $workData->save('valuesArr',$valuesArr);
      $workData->save('valuesMappingArr',array());  
      //$_SESSION["keys2Arr"]=$keys2Arr;  
      //matchovací data
      $dataArr=array();
      if (count($xml->match->keyA)>0){
        foreach ($xml->match->keyA as $keyA) {
        	$keyAname=(string)$keyA["name"];
        	$keyAname_legend=$legendArr_A[$keyAname];
        	$arr=array();
        	if (count($keyA->keyB)>0){
            foreach ($keyA->keyB as $keyB) {
            	$keyBname=(string)$keyB["name"];
            	$keyBname_legend=$legendArr_B[$keyBname];
              $arr[$keyBname_legend]=array('ratio'=>(string)$keyB["ratio"],'ratioArr'=>array('names'=>(string)$keyB["ratioNames"],'values'=>(string)$keyB["ratioValues"],'expirience'=>(string)$keyB["ratioExpirience"]));
            }
            $dataArr[$keyAname_legend]=$arr;
          }
        }
      }
      $workData->save('legendArr',$legendArr);
      $workData->save('dataArr',$dataArr);
      //$_SESSION["defaultDataArr"]=$_SESSION["dataArr"];
      $workData->save('defaultDataArr',$dataArr);
      //$_SESSION["userIgnoreArr"]=array();
      $workData->save('userIgnoreArr',array());   
      return true;
    }else{
      //nepodarilo se nacist XML data
      return false;
    }
  }
  
  public static function loadFMLUserFields($fml,$returnRichArray){
    require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnectorFDML.php'); 
    return XmlConnectorFDML::loadFMLUserFields($fml,$returnRichArray);
  }
  
  public static function getValuesPairs($fml,$fmId,$dict1id,$dict2id){
    require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnectorFDML.php'); 
    return XmlConnectorFDML::getValuesPairs($fml,$fmId,$dict1id,$dict2id);
  }
    
  
  public static function generateFML($finalArr,$legendArr,$valuesMapArr,$userDataArr,$art1,$art2){
    require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnectorFDML.php'); 
    return XmlConnectorFDML::generateFDML($finalArr,$legendArr,$valuesMapArr,$userDataArr,$art1,$art2);
  }
  
  public static function generateFML_OLD($finalArr,$legendArr,$valuesMapArr,$userDataArr,$art1,$art2){            
    //$xml=simplexml_load_string('<FML xmlns="http://keg.vse.cz/fml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://keg.vse.cz/fml ../validation/fml.xsd"></FML>');
    $xml=simplexml_load_string('<FML xmlns="http://keg.vse.cz/fml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"></FML>');
    if ((string)$art1["sourceType"]=="PMML"){
      //mapujeme PMML na ...
      if ((string)$art2["sourceType"]=="BKEF"){
        //...na BKEF
        $main=$xml->addChild("PMMLtoBKEF");
        $main->addChild("AppliesTo");
        $PMML=$main->AppliesTo[0]->addChild("PMML");
        $BKEF=$main->AppliesTo[0]->addChild("BKEF");
        $PMML->addChild("Title",$art1["title"]);
        $PMML->addChild("ArticleID",$art1["id"]);
        $PMML->addAttribute("ID",1);
        $BKEF->addChild("Title",$art2["title"]);
        $BKEF->addChild("ArticleID",$art2["id"]);
        $BKEF->addAttribute("ID",2);
        /**/
        $fieldMappings=$main->addChild("FieldMappings");
        $id=0;
        if (count($finalArr)>0){
          foreach ($finalArr as $key=>$arr) {  
            $id1=$id+1;
            $id2=$id+2;
            $id=$id+2;
            if (@$arr["name"]==""){
              continue;
            }
            $fieldMapping=$fieldMappings->addChild("FieldMapping");
            //projdeme finalArr a přesypeme výsledky do FML
          	$key1name=$legendArr[$key];
          	$key2name=$arr['name'];
          	$key2nameArr=split("###",$legendArr[$key2name],2);
            $mappingInfo=$fieldMapping->addChild('MappingInfo');
            $confidence=$mappingInfo->addChild("Confidence");      
            $confidence->addChild("names",@$arr["match"]["ratioArr"]["names"]);
            $confidence->addChild("values",@$arr["match"]["ratioArr"]["values"]);
            $confidence->addChild("expiriences",@$arr["match"]["ratioArr"]["expirience"]);
            if (isset($userDataArr[$key])){$matchingType="mapping";}else{$matchingType="suggestion";}
            $mappingInfo->addChild("Type",$matchingType);
            //TODO - mappingInfo  
            $field1=$fieldMapping->addChild('Field');
            $field1->addAttribute("type",'bkef');
            $field1->addAttribute('id',$id2);
            $field1->addAttribute("documentId",'1');
            $metafield=$field1->addChild("Metafield");
            $metafield->addAttribute("metaattribute",$key2nameArr[0]);
            $metafield->addAttribute("format",$key2nameArr[1]);
            $metafield->addChild("AllowedValues");
            $field2=$fieldMapping->addChild("Field");
            $field2->addAttribute("type",'pmml');
            $field2->addAttribute("documentId",'2');
            $field2->addAttribute("id",$id1);
            $field2->addChild("DataField")->addAttribute("name",$key1name);
            //mapovani hodnot           
            if ((isset($arr['mappedValues']))||(isset($arr['autoMappedValues']))){
              /* mapujeme výčet hodnot */
              $valueToValueMapping=$fieldMapping->addChild("ValueToValueMapping");
              //uživatelsky namapované hodnoty
              if (count(@$arr["mappedValues"])>0){
                $valueMappings=$valueToValueMapping->addChild("ValueMappings");
                foreach($arr["mappedValues"] as $valueA=>$valuesArr){
                  $valueMapping=$valueMappings->addChild("ValueMapping");
                  $field1=$valueMapping->addChild("Field");
                  $field1->addAttribute("fieldid",$id1);
                  $field1->addChild("Value",$valuesMapArr[$valueA]);
                  if (count($valuesArr)>0){
                    $field2=$valueMapping->addChild("Field");
                    $field2->addAttribute("fieldid",$id2);
                    foreach ($valuesArr as $valueB) {
                    	$field2->addChild("Value",$valuesMapArr[$valueB]);
                    }
                  }
                }  
              }
              //automaticky namapované hodnoty
              if (count(@$arr["autoMappedValues"])>0){
                $valueMappings=$valueToValueMapping->addChild("ValueMappingSuggestions");
                foreach($arr["autoMappedValues"] as $valueA=>$valuesArr){
                  $valueMapping=$valueMappings->addChild("ValueMapping");
                  $field1=$valueMapping->addChild("Field");
                  $field1->addAttribute("fieldid",$id1);
                  $field1->addChild("Value",$valuesMapArr[$valueA]);
                  if (count($valuesArr)>0){
                    $field2=$valueMapping->addChild("Field");
                    $field2->addAttribute("fieldid",$id2);
                    foreach ($valuesArr as $valueB) {
                    	$field2->addChild("Value",$valuesMapArr[$valueB]);
                    }
                  }
                }  
              }
              /**/
            }
          }
        }
      }else{
        //...na PMML
        $main=$xml->addChild('PMMLtoPMML');      
        $main->addChild("AppliesTo");
        $PMML1=$main->AppliesTo[0]->addChild("PMML");
        $PMML2=$main->AppliesTo[0]->addChild("PMML");
        $PMML1->addChild("Title",$art1["title"]);
        $PMML1->addChild("ArticleID",$art1["id"]);
        $PMML1->addAttribute("ID",1);
        $PMML2->addChild("Title",$art2["title"]);
        $PMML2->addChild("ArticleID",$art2["id"]);
        $PMML2->addAttribute("ID",2);
        /**/
        /**/
        $fieldMappings=$main->addChild("FieldMappings");
        $id=0;
        if (count($finalArr)>0){
          foreach ($finalArr as $key=>$arr) {       
            $id1=$id+1;
            $id2=$id+2;
            $id=$id+2;
            if (@$arr["name"]==""){
              continue;
            }
            $fieldMapping=$fieldMappings->addChild("FieldMapping");
            //projdeme finalArr a přesypeme výsledky do FML
          	$key1name=$legendArr[$key];
          	$key2name=$arr['name'];
          	$key2name=$legendArr[$key2name];                                
            $mappingInfo=$fieldMapping->addChild('MappingInfo');
            $confidence=$mappingInfo->addChild("Confidence");
            $confidence->addChild("names",@$arr["match"]["ratioArr"]["names"]);
            $confidence->addChild("values",@$arr["match"]["ratioArr"]["values"]);
            $confidence->addChild("expiriences",@$arr["match"]["ratioArr"]["expirience"]);
            if (isset($userDataArr[$key])){$matchingType="mapping";}else{$matchingType="suggestion";}
            $mappingInfo->addChild("Type",$matchingType);
            //TODO - mappingInfo  
            $field1=$fieldMapping->addChild("Field");
            $field1->addAttribute("type",'pmml');
            $field1->addAttribute("documentId",'1');
            $field1->addAttribute("id",$id1);
            $field1->addChild("DataField")->addAttribute("name",$key1name);
            $field2=$fieldMapping->addChild("Field");
            $field2->addAttribute("type",'pmml');
            $field2->addAttribute("documentId",'2');
            $field2->addAttribute("id",$id1);
            $field2->addChild("DataField")->addAttribute("name",$key2name);
            //mapovani hodnot           
            if ((isset($arr['mappedValues']))||(isset($arr['autoMappedValues']))){
              /* mapujeme výčet hodnot */
              $valueToValueMapping=$fieldMapping->addChild("ValueToValueMapping");
              //uživatelsky namapované hodnoty
              if (count(@$arr["mappedValues"])>0){
                $valueMappings=$valueToValueMapping->addChild("ValueMappings");
                foreach($arr["mappedValues"] as $valueA=>$valuesArr){
                  $valueMapping=$valueMappings->addChild("ValueMapping");
                  $field1=$valueMapping->addChild("Field");
                  $field1->addAttribute("fieldid",$id1);
                  $field1->addChild("Value",$valuesMapArr[$valueA]);
                  if (count($valuesArr)>0){
                    $field2=$valueMapping->addChild("Field");
                    $field2->addAttribute("fieldid",$id2);
                    foreach ($valuesArr as $valueB) {
                    	$field2->addChild("Value",$valuesMapArr[$valueB]);
                    }
                  }
                }  
              }
              //automaticky namapované hodnoty
              if (count(@$arr["autoMappedValues"])>0){
                $valueMappings=$valueToValueMapping->addChild("ValueMappingSuggestions");
                foreach($arr["autoMappedValues"] as $valueA=>$valuesArr){
                  $valueMapping=$valueMappings->addChild("ValueMapping");
                  $field1=$valueMapping->addChild("Field");
                  $field1->addAttribute("fieldid",$id1);
                  $field1->addChild("Value",$valuesMapArr[$valueA]);
                  if (count($valuesArr)>0){
                    $field2=$valueMapping->addChild("Field");
                    $field2->addAttribute("fieldid",$id2);
                    foreach ($valuesArr as $valueB) {
                    	$field2->addChild("Value",$valuesMapArr[$valueB]);
                    }
                  }
                }  
              }
              /**/
            }
          }
        }
      }
    }else{
      return null;
    }
    return $xml->asXML();
  }
  
  /**
   *  Funkce pro načtení vstupního textu-XML
   *  @param $xmlString - vstupní data
   *  @return string XML v uniformátu 
   */         
  public static function loadInputXML($xmlString,&$dataFormat){    
    if (strpos(strtolower($xmlString),'bkef')){       
      //budeme parsovat jako BKEF
      require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnectorBKEF.php');
      $dataFormat='BKEF';
      return XmlConnectorBKEF::transformXML($xmlString);
    }elseif(strpos(strtolower($xmlString),'<pmml')){
      //budeme parsovat jako PMML
      require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnectorPMML.php');
      $dataFormat='PMML';
      return XmlConnectorPMML::transformXML($xmlString);
    }elseif(strpos(($xmlString),'<dbtable')){
      //budeme parsovat jako TASK DBTABLE
      require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnectorTASK.php');
      $dataFormat='PMML';
      return XmlConnectorTASK::transformXML($xmlString);
    }
    return false;
  }
	
} // END class xmlConnector
?>

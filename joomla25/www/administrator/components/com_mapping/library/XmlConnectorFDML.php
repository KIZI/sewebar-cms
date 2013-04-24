<?php
  class XmlConnectorFDML{
  
    /**
     *  Funkce pro zjištění názvu Field z Dictionary z FML
     */         
    private static function getFieldName($field,$sourceFormat){
      if ($sourceFormat=='BKEF'){
        $ma='';
        $format='';
        if (count($field->Identifier)>0){
          foreach ($field->Identifier as $ident) {
          	if ((string)$ident['name']=='MetaAttribute'){
              $ma=(string)$ident;
            }elseif ((string)$ident['name']=='Format'){
              $format=(string)$ident;
            }
          }
        }
        if (($ma!='')&&($format!='')){
          return $ma.'###'.$format;
        }
      }elseif($sourceFormat=='PMML'){
        if (count($field->Identifier)>0){
          foreach ($field->Identifier as $ident) {
          	if ((string)$ident['name']=='Field'){
              return (string)$ident;
            }
          }
        }
      }
      return (string)$field->Name;
    }
    
    /**
     *  Funkce pro zjištění konkrétní hodnoty kategorie podle ID
     *  @param $fml - simpleXML instance FML souboru
     *  @param $dictID - dictionary ID
     *  @param $fieldID - field ID     
     *  @param $catID - category ID
     *  @return string                         
     */         
    public static function getValue($fml,$dictId,$fieldId,$catId){
      foreach ($fml->Dictionary as $dictionary) {
      	if ((string)$dictionary['id']==$dictId){
          foreach ($dictionary->Field as $field) {
          	if ((string)$field['id']==$fieldId){
              foreach ($field->Category as $category) {
              	if ((string)$category['id']==$catId){
                  return (string)$category;
                }
              }
              break;
            }
          }
          break;
        }
      }
    } 
    
    /**
     *  Funkce pro získání kombinace mapování hodnot
     *  @param $fml - simpleXML instance FML souboru
     *  @param $fmId - pořadové číslo příslušného fieldMappingu   
     *  @param $dict1id - id prvního dictionary
     *  @param $dict2id - id druhého dictionary           
     *  @return array             
     */         
    public static function getValuesPairs($fml,$fmId,$dict1id,$dict2id){
      $fieldMapping=@$fml->DictionaryMapping->FieldMapping[$fmId];
      if (!$fieldMapping){return null;}
      
      //potrebujeme ID jednotlivých fieldů
      foreach ($fieldMapping->AppliesTo->FieldRef as $fieldRef) {
      	if ((string)$fieldRef['dictID']==$dict1id){
          $fieldId1=(string)$fieldRef['id'];
        }elseif ((string)$fieldRef['dictID']==$dict2id){
          $fieldId2=(string)$fieldRef['id'];
        }
      }
      if ((!$fieldId1)||(!$fieldId2)){return null;}
      
      $outputArr=array();
      //vyresime jednotlive hodnoty
      if (count($fieldMapping->ValueMappings->ValueMapping)>0){
        foreach ($fieldMapping->ValueMappings->ValueMapping as $valueMapping) {
          if ((string)$valueMapping['type']!='user'){
            continue;
          }
          $catRef1='';
          $catRef2='';
          $val1='';
          $val2='';
        	foreach ($valueMapping->Field as $field) {
         	  $fDictID=(string)$field['dictID'];
            $fID=(string)$field['id'];
            if (($fDictID==$dict1id)&&($fID==$fieldId1)){
              $catRef1=$field->CatRef;
            }elseif (($fDictID==$dict2id)&&($fID==$fieldId2)){
              $catRef2=$field->CatRef;
            }
          }
          if ((!$catRef1)||(!$catRef2)){continue;}

          //máme reference na dvojici konkrétních hodnot
          if ((string)$catRef1!=''){
            $val1=(string)$catRef1;
          }elseif(isset($catRef1['id'])){
            $val1=XmlConnectorFDML::getValue($fml,$dict1id,$fieldId1,(string)$catRef1['id']);
          }
          if ((string)$catRef2!=''){
            $val2=(string)$catRef2;
          }elseif(isset($catRef2['id'])){
            $val2=XmlConnectorFDML::getValue($fml,$dict2id,$fieldId2,(string)$catRef2['id']);
          }
          $outputArr[]=array($val1,$val2);          
        }
      }
      return $outputArr;
    }  
  
    /**
     *  Funkce pro načtení uživatelských mapování
     */         
    public static function loadFMLUserFields($fml,$returnRichArray){      
    
      $dictionary1=$fml->Dictionary[0];
      $dictionary2=$fml->Dictionary[1];
      $dict1=array('id'=>(string)$dictionary1['id'],'sourceFormat'=>(string)$dictionary1['sourceFormat']);
      $dict2=array('id'=>(string)$dictionary2['id'],'sourceFormat'=>(string)$dictionary2['sourceFormat']);
      $mappingArr=array();
      
      if ($dict1['sourceFormat']=='BKEF'){
        //pokud bychom měli chybně uložené pořadí sloupců, tak je pro zpracování prohodíme...
        $dictX=$dict2;
        $dict2=$dict1;
        $dict1=$dictX;
      }else{
        //TODO meli bychom vyresit to, jak při mapování PMML-PMML rozhodnout, který soubor je který!
      }
      
      //připravíme si pole s mapováními pro dictionary 1
      $fieldNames1=array();
      if(count($dictionary1->Field)>0){
        foreach ($dictionary1->Field as $field) {
          //potrebujeme vyhodnotit název položky
        	$fieldId=(string)$field['id'];
          $fieldName=XmlConnectorFDML::getFieldName($field,$dict1['sourceFormat']);
          $fieldNames1[$fieldId]=$fieldName;
        }
      }
      //připravíme si pole s mapováními pro dictionary 2
      $fieldNames2=array();
      if(count($dictionary2->Field)>0){
        foreach ($dictionary2->Field as $field) {
          //potrebujeme vyhodnotit název položky
        	$fieldId=(string)$field['id'];
          $fieldName=XmlConnectorFDML::getFieldName($field,$dict2['sourceFormat']);
          $fieldNames2[$fieldId]=$fieldName;
        }
      }
      
      //projdeme jednotliva mapovani
      $fieldMappingId=0;
      if (count($fml->DictionaryMapping[0]->FieldMapping)>0){
        foreach ($fml->DictionaryMapping[0]->FieldMapping as $fieldMapping) {
        	if (@(string)$fieldMapping->MappingInfo[0]->Type=="user"){
            //jde o uzivatelske mapovani...
            $appliesTo1Name='';
            $appliesTo2Name='';
            
            foreach ($fieldMapping->AppliesTo[0]->FieldRef as $fieldRef) {
            	$dictID=(string)$fieldRef['dictID'];
              $id=(string)$fieldRef['id'];
              if ($dictID==$dict1['id']){
                if (isset($fieldNames1[$id])) $appliesTo1Name=$fieldNames1[$id];
              }elseif($dictID==$dict2['id']){
                if (isset($fieldNames2[$id])) $appliesTo2Name=$fieldNames2[$id];
              }
            }
            
            if (($appliesTo1Name!='')&&($appliesTo2Name!='')){
              $mappingArr[]=array('0'=>$appliesTo1Name,'1'=>$appliesTo2Name,'fmId'=>$fieldMappingId);
            }
          }
          $fieldMappingId++;
        }
      }
      
      if ($returnRichArray){
        //vratime mapovaci pole a informace o prubehu popis mapovani...
        return array(
                 'mappingArr'=>$mappingArr,
                 'dict1id'=>$dict1['id'],
                 'dict2id'=>$dict2['id']
               );
      }else{
        //vratime jenom zakladni mapovaci pole
        return $mappingArr;
      }
    }
  
  
    /*-------------------------------------------------------------------------------------------------------------------*/
  
    /**
     *  Funkce pro vytvoření klíče pro novou hodnotu
     */         
    private static function addPMMLField(&$xmlDictionary,$id,$name){
      $field=$xmlDictionary->addChild('Field');
      $field->addAttribute('id',$id);
      $field->addChild('Name',$name);
      $ident1=$field->addChild('Identifier',$name);
      $ident1->addAttribute('name','Field');
      //TODO doplneni udaju
    }
    /**
     *  Funkce pro vytvoření klíče pro novou hodnotu
     */         
    private static function addBKEFField(&$xmlDictionary,$id,$metaattribute,$format){
      $field=$xmlDictionary->addChild('Field');
      $field->addAttribute('id',$id);
      $field->addChild('Name',$metaattribute.'###'.$format);
      $ident1=$field->addChild('Identifier',$metaattribute);
      $ident1->addAttribute('name','MetaAttribute');
      $ident2=$field->addChild('Identifier',$format);
      $ident2->addAttribute('name','Format');
      //TODO doplneni udaju
    } 
    
    private static function addValueToDictionary(&$xmlDictionary,$fieldId,$valueId,$value){
      if (count($xmlDictionary->Field)>0){
        foreach ($xmlDictionary->Field as $field) {
        	if ((string)$field['id']==$fieldId){
            $cat=$field->addChild('Category',(string)$value);
            $cat->addAttribute('id','v'.$fieldId.'_'.$valueId);
          }
        }
      }
    }

    public static function generateFDML($finalArr,$legendArr,$valuesMapArr,$userDataArr,$art1,$art2){            
      //pole pro uložení vazby mezi jednotlivymi field
      $art1_NameIdMap=array();
      $xml=simplexml_load_string('<FDML xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://keg.vse.cz/ns/fdml0_2 http://sewebar.vse.cz/schemas/FDML0_2.xsd" xmlns="http://keg.vse.cz/ns/fdml0_2"></FDML>');
      
      //vygenerujeme hlavicku
      $header=$xml->addChild('Header');
      $header->addChild('Timestamp',date('c'));
      //TODO doplnit jmeno uzivatele atp.
      
      /**informace o prvnim ze zdroju**************************************************************/
      $dictionary1=$xml->addChild('Dictionary');
      //TODO $dictionary1->addAttribute('numberOfFields');
      $dictionary1->addAttribute('sourceFormat','PMML');
      $dictionary1->addAttribute('sourceDictType','DataDictionary');
      $dictionary1->addAttribute('id','a');
      
      //vypsani identifikatoru
      if ($art1['taskId']){
        //jde o generovani na zaklade ulohy
        $identifierId=$dictionary1->addChild('Identifier');
        $identifierId->addAttribute('taskId',$art1['taskId']);
        $dictionary1->addAttribute('sourceName',$art1['title']);
      }elseif($art1['id']){
        //jde o generovani na zaklade clanku
        $identifierId=$dictionary1->addChild('Identifier');
        $identifierId->addAttribute('articleId',$art1['id']);
        $dictionary1->addAttribute('sourceName',$art1['title']);
      }
      
      /**informace o druhem ze zdroju**************************************************************/
      if ((string)$art2["sourceType"]=="BKEF"){
        //TODO doplnit pro BKEF
        $dictionary2=$xml->addChild('Dictionary');
        $dictionary2->addAttribute('sourceFormat',"BKEF");
        $dictionary2->addAttribute('sourceName',$art2['title']);
        
        $dictionary2->addAttribute('sourceDictType','Range');
        $dictionary2->addAttribute('id','b');
        
        $identifier2Id=$dictionary2->addChild('Identifier');
        $identifier2Id->addAttribute('articleId',$art2['id']);
      }else{
        $dictionary2=$xml->addChild('Dictionary');
        $dictionary2->addAttribute('sourceFormat',"PMML");
        $dictionary2->addAttribute('sourceName',$art2['title']);
        
        $dictionary2->addAttribute('sourceDictType','DataDictionary');
        $dictionary2->addAttribute('id','b');
        
        $identifier2Id=$dictionary2->addChild('Identifier');
        $identifier2Id->addAttribute('articleId',$art2['id']);
      }       //TODO - KONTROLA, jestli to takhle muze fungovat!
        /********************************************************************************************/
        $dictionaryMapping=$xml->addChild('DictionaryMapping');
        /********************************************************************************************/
                
        //pomocna pole pro info o tom, ktere klice uz byly vytvoreny
        $writtenFieldsA=array();
        $writtenFieldsB=array();   
        $writtenValues=array();    
                      
        if (count($finalArr)>0){
          foreach ($finalArr as $key=>$arr) {
            if (@$arr["name"]=="") continue;           
            $fieldMapping=$dictionaryMapping->addChild("FieldMapping");
            //projdeme finalArr a přesypeme výsledky do FML
          	$key1name=$legendArr[$key];
          	$key2=$arr['name'];
            $key2name=$legendArr[$key2];
          	$key2nameArr=split("###",$key2name,2);
          	
            
            //overime, jestli uz byl dany klic uveden v daném dictionary (aby bylo na co odkazovat)
            if (!in_array($key,$writtenFieldsA)){
              XmlConnectorFDML::addPMMLField($dictionary1,$key,$key1name);
              $writtenFieldsA[]=$key;  
            }
            if (!in_array($key2name,$writtenFieldsB)){   
              if (count($key2nameArr)>1){
                XmlConnectorFDML::addBKEFField($dictionary2,$key2,$key2nameArr[0],$key2nameArr[1]);
              }else{                  
                XmlConnectorFDML::addPMMLField($dictionary2,$key2,$key2name);
              }
              $writtenFieldsB[]=$key2;
            }
            //
            $appliesTo=$fieldMapping->addChild('AppliesTo');
            $fieldRef1=$appliesTo->addChild('FieldRef');
            $fieldRef1->addAttribute('dictID','a');
            $fieldRef1->addAttribute('id',$key);
            //TODO naplneni atributu
            $fieldRef2=$appliesTo->addChild('FieldRef');
            $fieldRef2->addAttribute('dictID','b');
            $fieldRef2->addAttribute('id',$key2);
            
            /*vyplnime mapping info*/
              $mappingInfo=$fieldMapping->addChild('MappingInfo');
              //typ mapovani
              if (isset($userDataArr[$key])){$matchingType="user";}else{$matchingType="autosuggestion";}
              $mappingInfo->addChild('Type',$matchingType);
              //hodnoty autosuggestion
              $autosuggestion=$mappingInfo->addChild("Autosuggestion");      
              $autosuggestion->addChild("Names",@$arr["match"]["ratioArr"]["names"]);
              $autosuggestion->addChild("Values",@$arr["match"]["ratioArr"]["values"]);
              $autosuggestion->addChild("Experience",@$arr["match"]["ratioArr"]["expirience"]);
            /*--vyplnime mapping info*/
            /*mapovani hodnot*/         
            if ((isset($arr['mappedValues']))||(isset($arr['autoMappedValues']))){
              // mapujeme výčet hodnot - připravíme si klíč, do kterého budeme tato mapování ukládat 
              $valueMappings=$fieldMapping->addChild("ValueMappings");
              //uživatelsky namapované hodnoty
              if (count(@$arr["mappedValues"])>0){
                foreach($arr["mappedValues"] as $valueA=>$valuesArr){      
                  if (count($valuesArr)>0){
                    foreach ($valuesArr as $valueB) {
                    	$valueMapping=$valueMappings->addChild("ValueMapping");
                      $valueMapping->addAttribute('type','user');
                      
                      $field1=$valueMapping->addChild("Field");
                      $field1->addAttribute('id',$key);
                      $field1->addAttribute('dictID','a');
                      $catRef1=$field1->addChild('CatRef',$valuesMapArr[$valueA]);
                      $catRef1->addAttribute('id','v'.$key.'_'.$valueA);
                      
                      $field2=$valueMapping->addChild("Field");
                      $field2->addAttribute('id',$key2);
                      $field2->addAttribute('dictID','b');
                      $catRef2=$field2->addChild('CatRef',$valuesMapArr[$valueB]);
                      $catRef2->addAttribute('id','v'.$key2.'_'.$valueB);
                    }
                  }
                }  
              }
              //automaticky namapované hodnoty
              if (count(@$arr["autoMappedValues"])>0){
                foreach($arr["autoMappedValues"] as $valueA=>$valuesArr){   
                  if (count($valuesArr)>0){
                    foreach ($valuesArr as $valueB){
                    	$valueMapping=$valueMappings->addChild("ValueMapping");
                      $valueMapping->addAttribute('type','autosuggestion');
                      
                      $field1=$valueMapping->addChild("Field");
                      $field1->addAttribute('id',$key);
                      $field1->addAttribute('dictID','a');
                      $catRef1=$field1->addChild('CatRef',$valuesMapArr[$valueA]);
                      $catRef1->addAttribute('id','v'.$key.'_'.$valueA);
                      
                      $field2=$valueMapping->addChild("Field");
                      $field2->addAttribute('id',$key2);
                      $field2->addAttribute('dictID','b');
                      $catRef2=$field2->addChild('CatRef',$valuesMapArr[$valueB]);
                      $catRef2->addAttribute('id','v'.$key2.'_'.$valueB);
                      
                      //pokud odkazujeme na referenci, ktera jeste nebyla vytvorena, tak ji doplnime...
                      if (!in_array($valueA,$writtenValues)){  
                        XmlConnectorFDML::addValueToDictionary($dictionary1,$key,$valueA,$valuesMapArr[$valueA]);
                        $writtenValues[]=$valueA;
                      }
                      if (!in_array($valueB,$writtenValues)){
                        XmlConnectorFDML::addValueToDictionary($dictionary2,$key2,$valueB,$valuesMapArr[$valueB]);
                        $writtenValues[]=$valueB;             
                      }
                    }
                  }
                }  
              }
            }
            /*--mapovani hodnot*/
            
          }
        }
        /********************************************************************************************/
      //TODO tady byla ukoncovaci zavorka
      /////////////////////////////////-----------------------------------------------/////////////////////////////
     
      return $xml->asXML();
    }
  }
?>
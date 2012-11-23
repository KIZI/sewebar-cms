<?php

  /**
   *  Třída řešící FML z pohledu namapovaného PMML na BKEF
   */     
  class dbconnectModelGenerator extends JModel{
    const EQUIDISTANT_INTERVALS_COUNT=10;
    private $bkef;
    private $fml;
    private $isPreprocessed;
    private $userName;
    
    public function isPreprocessed(){
      return $this->isPreprocessed;
    }
    
    /**
     *  Funkce vracející vygenerovaný BKEF serializované do XML
     */         
    public function getBkefXML($bkefName){
      $this->bkef->Header->Title=$bkefName;
      return $this->bkef->asXML();
    }
    
    /**
     *  Funkce nastavující IDčka BKEFu a TASKu a vracející FML serializované do XML
     */         
    public function getFmlXML($bkefArticleId,$bkefName,$taskId,$taskName){
      $pmmlDictionary=null;
      $bkefDictionary=null;
      if (count($this->fml->Dictionary)>0){
        foreach ($this->fml->Dictionary as $dictionary) {
        	if ((string)$dictionary['sourceFormat']=='PMML'){
            $pmmlDictionary=$dictionary;
          }elseif((string)$dictionary['sourceFormat']=='BKEF'){
            $bkefDictionary=$dictionary;
          }
        }
      }
      if (($pmmlDictionary)&&($bkefDictionary)){
        //máme zjištěné dictionaries pro pmml i bkef - nastavíme názvy a id
        $pmmlDictionary['sourceName']=$taskName;
        $pmmlDictionary->Identifier['taskId']=$taskId;
        $bkefDictionary['sourceName']=$bkefName;
        $pmmlDictionary->Identifier['articleId']=$bkefArticleId;
      }
      
      return $this->fml->asXML();
    }
              
    public function processData($contentXML,$tableName,$generatePreprocessing=false){  
      $content=simplexml_load_string($contentXML);
      $this->bkef=$this->getNewBkef();
      $this->fml=$this->getNewFml();
      if ((!$content)||(!$this->bkef)||(!$this->fml)){return null;}
      /*připravíme /dictionary */
      $pmmlDictionary=$this->fml->addChild('Dictionary');
      $bkefDictionary=$this->fml->addChild('Dictionary');
      $dictionaryMapping=$this->fml->addChild('DictionaryMapping');
      
      $pmmlDictionary->addAttribute('sourceFormat','PMML');
      $pmmlDictionary->addAttribute('sourceDictType','DataDictionary');
      $pmmlDictionary->addAttribute('id','a');
      $pmmlDictionary->addAttribute('sourceName','');
      $pmmlIdentifier=$pmmlDictionary->addChild('Identifier');
      $pmmlIdentifier->addAttribute('taskId','');    
      
      $bkefDictionary->addAttribute('sourceFormat','BKEF');
      $bkefDictionary->addAttribute('sourceDictType','Range');
      $bkefDictionary->addAttribute('id','b');
      $bkefDictionary->addAttribute('sourceName',''); 
      $bkefIdentifier=$bkefDictionary->addChild('Identifier');
      $bkefIdentifier->addAttribute('articleId','');            
      /*--připravíme /dictionary */
      
      
      /*projdeme jednotlive sloupce datoveho zdroje a najednou generujeme jak BKEF, tak PMML*/
      $preprocessed=true;
      if (count($content->col)>0){
        $idCounter=0;
        foreach ($content->col as $col) {
        	/*vytvorime zaznamy v BKEF*/
          $metaAttribute=$this->newBasicMetaAttribute((string)$col->name);
          $format=$this->newFormat($metaAttribute,$tableName,$col,$generatePreprocessing);
          if (!isset($format->PreprocessingHints->DiscretizationHint)){
            $preprocessed=false;
          }
          /*vytvorime dictionaries v FML*/
          $pmmlField=$pmmlDictionary->addChild('Field');
          $pmmlField->addAttribute('id','a'.$idCounter);
          $pmmlField->addChild('Name',(string)$col->name);
          $pmmlFieldIdentifier=$pmmlField->addChild('Identifier',(string)$col->name);
          $pmmlFieldIdentifier->addAttribute('name','Field');
          
          $bkefField=$bkefDictionary->addChild('Field');
          $bkefField->addAttribute('id','b'.$idCounter);
          $maName=(string)$metaAttribute->Name;
          $fName=(string)$format->Name;
          $bkefField->addChild('Name',$maName.'###'.$fName);
          $maIdentifier=$bkefField->addChild('Identifier',$maName);
          $maIdentifier->addAttribute('name','MetaAttribute');
          $fIdentifier=$bkefField->addChild('Identifier',$fName);
          $fIdentifier->addAttribute('name','Format');
          /*vytvoříme mapování*/
          $fieldMapping=$dictionaryMapping->addChild('FieldMapping');
          $fieldMapping->addChild('AppliesTo');
          $fieldRefPmml=$fieldMapping->AppliesTo->addChild('FieldRef');
          $fieldRefPmml->addAttribute('dictID','a');
          $fieldRefPmml->addAttribute('id','a'.$idCounter);
          $fieldRefBkef=$fieldMapping->AppliesTo->addChild('FieldRef');
          $fieldRefBkef->addAttribute('dictID','b');
          $fieldRefBkef->addAttribute('id','b'.$idCounter);
          $fieldMapping->addChild('MappingInfo')->addChild('Type','auto');
          //ověříme, jestli máme doplnit mapování hodnot
          if ((string)$format->Range['type']=='enumeration'){
            if (count($format->Range->Value)>0){
              $valuesArr=array();
              $valueMappings=$fieldMapping->addChild('ValueMappings');
              foreach ($format->Range->Value as $value) {
                $valueStr=(string)$value;
                if (!in_array($valueStr,$valuesArr)){$valuesArr[]=$valueStr;}
                $valueId=array_search($valueStr,$valuesArr);
              	$valueMapping=$valueMappings->addChild('ValueMapping');
                $valueMapping->addAttribute('type','auto');
                $fieldPmml=$valueMapping->addChild('Field');
                $fieldPmml->addAttribute('id','a'.$idCounter);
                $fieldPmml->addAttribute('dictID','a');
                $catRefPmml=$fieldPmml->addChild('CatRef',$valueStr);
                $catRefPmml->addAttribute('id','va'.$idCounter.'_'.$valueId);
                $fieldBkef=$valueMapping->addChild('Field');
                $fieldBkef->addAttribute('id','b'.$idCounter);
                $fieldBkef->addAttribute('dictID','b');
                $catRefBkef=$fieldBkef->addChild('CatRef',$valueStr);
                $catRefBkef->addAttribute('id','vb'.$idCounter.'_'.$valueId);
              }
              unset($valuesArr);
            }
          }
          if (count(@$format->PreprocessingHints->DiscretizationHint)>0){
            $preprocessingHintName=(string)$format->PreprocessingHints->DiscretizationHint[0]->Name;
            $fieldMapping->addChild('PreprocessingHint',$preprocessingHintName);
          }
          /*zvýšíme počítadlo pro IDčka*/
          $idCounter++;
        }
      }else{
        return null;
      }  
      $this->isPreprocessed=$preprocessed;
      /*--projdeme jednotlive sloupce datoveho zdroje a najednou generujeme jak BKEF, tak PMML*/
      return true; 
    }
    
    /**
     *  Funkce vracející datový typ v kódování BKEFu
     */         
    private function getBkefDatatype($datatype){    
      switch ($datatype) {
        case "boolean":return 'String';break;
        case "integer":return 'Integer';break;
        case "float":return 'Float';break; 
        default: return 'String';break;
      }        
    }
    
    /**
     *  Funkce pro založení nového formátu
     */         
    public function newFormat(&$metaAttribute,$formatName,$col=null,$generatePreprocessing=false){
      if (!isset($metaAttribute->Formats)){$metaAttribute->addChild('Formats');}
      //kontrola, jestli formát se zadaným názvem už neexistuje...        
      if (count(@$metaAttribute->Formats->Format)>0){
        $formatNamesArr=array();
        foreach ($metaAttribute->Formats->Format as $format) {
        	$formatNamesArr[]=(string)$format->Name;
        }
        $suffix=0;
        while(in_array($formatName.(($suffix==0)?'':'_'.$suffix))){
          $suffix++;
        }
        if ($suffix>0){
          $formatName.='_'.$suffix;
        }
        unset($formatNamesArr);
      }            
      
      $format=$metaAttribute->Formats->addChild('Format');
      $format->addChild('Name',$formatName);
      $this->prepareBkefTimestamps($format);
      $format->addChild('Annotations');
      $dataType=$this->getBkefDatatype($col->type);
      $format->addChild('DataType',$dataType);
      if ($dataType=='String'){
        $format->addChild('ValueType','Nominal');
      }else{
        $format->addChild('ValueType','Ordinal');
      }    
      $collation=$format->addChild('Collation');
      $collation->addAttribute('sense','Ascending');
      $range=$format->addChild('Range');
      $preprocessingHints=$format->addChild('PreprocessingHints');
      $format->addChild('ValueDescriptions');

      if (($dataType=='Float')||($dataType=='Integer')){
        //jde o čísla => zadáme interval
        $collation->addAttribute('type','Numerical');
        $range->addAttribute('type','interval');
        if (isset($col->statistics)){
          $interval=$range->addChild('Interval');
          $interval->addAttribute('closure','closedClosed');
          $interval->addAttribute('leftMargin',(string)$col->statistics->min);
          $interval->addAttribute('rightMargin',(string)$col->statistics->max);
        }
        if ($generatePreprocessing){
          $this->generatePreprocessing_equidistant($preprocessingHints,(string)$col->statistics->min,(string)$col->statistics->max,self::EQUIDISTANT_INTERVALS_COUNT);
        }
      }else{
        //jde o výčet hodnot
        $collation->addAttribute('type','Enumeration');
        $range->addAttribute('type','enumeration');
        if (count(@$col->items->item)>0){
          foreach ($col->items->item as $item) {
          	$range->addChild('Value',(string)$item);
          }
          if ($generatePreprocessing){
            $this->generatePreprocessing_eachOne($preprocessingHints);
          }
        }
      }              
      return $format;
    }   
    
    /**
     *  Funkce pro vygenerování discretization hintu each value-one category
     */         
    private function generatePreprocessing_eachOne(&$preprocessingHints){
      $discretizationHint=$preprocessingHints->addChild('DiscretizationHint');
      $discretizationHint->addChild('Name',JText::_('EACH_VALUE_ONE_BIN'));
      $this->prepareBkefTimestamps($discretizationHint);
      $discretizationHint->addChild('EachValueOneBin');
    }
    
    /**
     *  Funkce pro vygenerování discretization hintu each value-one category
     *  @param &$preprocessingHints
     *  @param $min - počátek celého děleného intervalu
     *  @param $max - konec celého děleného intervalu
     *  @param $intervalsCount - počet intervalů, na které se má celý interval rozdělit                         
     */         
    private function generatePreprocessing_equidistant(&$preprocessingHints,$min,$max,$intervalsCount){ 
      $discretizationHint=$preprocessingHints->addChild('DiscretizationHint');
      $discretizationHint->addChild('Name',JText::_('EQUIDISTANT_INTERVALS'));
      $this->prepareBkefTimestamps($discretizationHint);
      $equidistant=$discretizationHint->addChild('EquidistantInterval');
      
      if ($min>$max){$change=$min;$min=$max;$max=$change;unset($change);}
      $step=(($max-$min)/$intervalsCount);
      $equidistant->addChild('Start',$min);
      $equidistant->addChild('End',$max);
      $equidistant->addChild('Step',$step);
    }    
    
    /**
     *  Funkce pro založení nového metaatributu
     *  @return simplexml object node     
     */         
    public function newBasicMetaAttribute($name){
      $metaAttributes=$this->bkef->MetaAttributes;
      $id=0;
      if (count(@$metaAttributes->MetaAttribute)>0){
        $idsArr=array();
        foreach ($metaAttributes->MetaAttribute as $metaAttribute) {
          $idsArr[]=(string)$metaAttribute['id'];
        }
        while (in_array($id, $idsArr)) {
        	$id++;
        }
        unset($idsArr);
      }
      $newMetaAttribute=$metaAttributes->addChild('MetaAttribute');
      $newMetaAttribute->addAttribute('id',$id);
      $newMetaAttribute->addAttribute('level',0);
      $newMetaAttribute->addChild('Name',$name);
      $this->prepareBkefTimestamps($newMetaAttribute);
      $newMetaAttribute->addChild('Annotations');
      $newMetaAttribute->addChild('Variability','Stable');
      $newMetaAttribute->addChild('Formats');
      return $newMetaAttribute;
    }
    
    /**
     *  Funkce pro vygenerování timestampů ve formátu pro BKEF
     */         
    private function prepareBkefTimestamps(&$node){
      if (!$this->userName){$this->userName=$this->getUserName();}
      if (!$this->timestamp){$this->timestamp=date('c');}
      
      if (!isset($node->Created)){$node->addChild('Created');}
      if (!isset($node->Created->Timestamp)){$node->Created->addChild('Timestamp',$this->timestamp);}else{$node->Created->Timestamp[0]=$this->timestamp;}
      if (!isset($node->Created->Author)){$node->Created->addChild('Author',$this->userName);}else{$node->Created->Author[0]=$this->userName;}
      if (!isset($node->LastModified)){$node->addChild('LastModified');}
      if (!isset($node->LastModified->Timestamp)){$node->LastModified->addChild('Timestamp',$this->timestamp);}else{$node->LastModified->Timestamp[0]=$this->timestamp;}
      if (!isset($node->LastModified->Author)){$node->LastModified->addChild('Author',$this->userName);}else{$node->LastModified->Author[0]=$this->userName;}
    }
    
    /**
     *  Funkce pro založení prázdného BKEF dokumentu
     *  @return simplexml object     
     */         
    private function getNewBkef($title){
      return simplexml_load_string('<'.'?xml version="1.0" encoding="UTF-8"?'.'>
                                     <'.'?xml-stylesheet type="text/xsl" href="bkef-styl.xsl"?'.'>
                                     <BKEFData xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://keg.vse.cz/bkef_data http://sewebar.vse.cz/schemas/BKEF1_1_Data.xsd" xmlns="http://keg.vse.cz/bkef_data">
                                       <Header>
                                         <Application name="DBConnect" version="1.0"/>
                                         <Title>'.htmlentities($title).'</Title>
                                         <Created>
                                           <Timestamp>'.date('c').'</Timestamp>
                                           <Author>'.$this->getUserName().'</Author>
                                         </Created>
                                         <LastModified>
                                           <Timestamp>'.date('c').'</Timestamp>
                                           <Author>'.$this->getUserName().'</Author>
                                         </LastModified>
                                       </Header>
                                       <MetaAttributes></MetaAttributes>
                                     </BKEFData>');
    }
    
    /**
     *  Funkce pro založení nového FML dokumentu
     *  @return simplexml object     
     */         
    private function getNewFml(){
      $xml=simplexml_load_string('<FDML xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://keg.vse.cz/ns/fdml0_2 http://sewebar.vse.cz/schemas/FDML0_2.xsd" xmlns="http://keg.vse.cz/ns/fdml0_2"><Header><Timestamp>'.date('c').'</Timestamp></Header></FDML>');
      return $xml;
    } 
    
    /**
     *  Funkce vracející jméno aktuálního uživatele
     */         
    private function getUserName(){
      $user=& JFactory::getUser();
      return $user->name;
    }
  }
  
?>
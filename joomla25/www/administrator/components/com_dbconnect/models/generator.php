<?php

  /**
   *  Třída řešící FML z pohledu namapovaného PMML na BKEF
   */     
  class dbconnectModelGenerator extends JModel{
    const EQUIDISTANT_INTERVALS_COUNT=10;
    const MAX_NUMERIC_ITEMS=15;
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
                                                          
    public function processData($contentXML,$tableName,$generatePreprocessing=false,$fmlId=0,$bkefId=0,$dataModel=null,$fmlModel=null,$bkefModel=null){
      $content=simplexml_load_string($contentXML);   //exit('sem');      
      //načteme existující struktury FML a BKEF, nebo vytvoříme nové
      if (($bkefId>0)&&($dataModel)){
        $this->bkef=$dataModel->loadArticleXML($bkefId);
      }
      if (($fmlId>0)&&($dataModel)){
        $this->fml=$dataModel->loadArticleXML($fmlId);
      }
      if (!(($this->bkef)&&($this->fml))){
        $this->bkef=$this->getNewBkef($tableName);
        $this->fml=$this->getNewFml();
      }                        
      
      if ((!$content)||(!$this->bkef)||(!$this->fml)){return null;}
                                      
      // potřebujeme najít /dictionary, nebo vytvořit nové
        // připravíme /dictionary 
        if (isset($this->fml->Dictionary)){
          $fmlModel->setFml($this->fml);
          $pmmlDictionary=&$fmlModel->getPmmlDictionary();
          $bkefDictionary=&$fmlModel->getBkefDictionary();
          $dictionariesIdsArr=$fmlModel->getDictionariesIdsArr();
        }else{
          $dictionariesIdsArr=array();
        }
                                    
        if (!$pmmlDictionary){ 
          $pmmlDictionary=$this->fml->addChild('Dictionary');
          $pmmlDictionary->addAttribute('sourceFormat','PMML');
          $pmmlDictionary->addAttribute('sourceDictType','DataDictionary');
          $pmmlId='a';
          while(in_array($pmmlId,$dictionariesIdsArr)){
            $pmmlId.='x';
          }
          $pmmlDictionary->addAttribute('id',$pmmlId);
          $pmmlDictionary->addAttribute('sourceName','');
          $pmmlIdentifier=$pmmlDictionary->addChild('Identifier');
          $pmmlIdentifier->addAttribute('taskId','');
          $pmmlMaxCounter=0;
        }else{            
          //potřebujeme zjistit maximální hodnotu counteru pro pmml
          $pmmlFieldNamesArr=$fmlModel->getPmmlFieldNamesArr();
          $pmmlId=$fmlModel->getPmmlDictionaryId();
          $pmmlId_length=(strlen($pmmlId));
          $pmmlMaxCounter=0;
          foreach ($pmmlFieldNamesArr as $name=>$arr) {
          	$id=@$arr['id'];
            $id=substr($id,$pmmlId_length); 
            if ($id>$pmmlMaxCounter){
              $pmmlMaxCounter=$id;
            }
          }
        }               
        if (!$bkefDictionary){  
          $bkefDictionary=$this->fml->addChild('Dictionary');
          $bkefDictionary->addAttribute('sourceFormat','BKEF');
          $bkefDictionary->addAttribute('sourceDictType','Range');
          $bkefId='b';
          while(in_array($bkefId,$dictionariesIdsArr)){
            $bkefId.='x';
          }
          $bkefDictionary->addAttribute('id',$bkefId);
          $bkefDictionary->addAttribute('sourceName',''); 
          $bkefIdentifier=$bkefDictionary->addChild('Identifier');
          $bkefIdentifier->addAttribute('articleId','');
          $bkefMaxCounter=0;
        }else{
          //potřebujeme zjistit maximální hodnotu counteru pro bkef
          $bkefFieldsArr=$fmlModel->getBkefFieldsArr();
          $bkefId=$fmlModel->getBkefDictionaryId();
          $bkefId_length=(strlen($bkefId));
          $bkefMaxCounter=0;
          foreach ($bkefFieldsArr as $id=>$field) {
          	$id=@$arr['id'];
            $id=substr($id,$bkefId_length);
            if ($id>$bkefMaxCounter){
              $bkefMaxCounter=$id;
            }
          }
        }          
        
        $maxCounter=max($pmmlMaxCounter,$bkefMaxCounter);
        
        if (isset($this->fml->DictionaryMapping)){
          $dictionaryMapping=$this->fml->DictionaryMapping;
        }else{
          $dictionaryMapping=$this->fml->addChild('DictionaryMapping');
        }                    
        //--připravíme /dictionary 
      //--potřebujeme najít /dictionary, nebo vytvořit nové
      
      //projdeme jednotlive sloupce datoveho zdroje a najednou generujeme jak BKEF, tak PMML
      $preprocessed=true;
      if (count($content->col)>0){
        $idCounter=$maxCounter+1;
        $pmmlFieldNamesArr=$fmlModel->getPmmlFieldNamesArr();
        foreach ($content->col as $col){
          $colName=(string)$col->name;
          if ($pmmlFieldNamesArr[$colName]){
            //sloupec už v FML datech existuje...
            continue;
          }
        	//vytvorime zaznamy v BKEF
          $metaAttribute=$this->newBasicMetaAttribute($colName);
          $format=$this->newFormat($metaAttribute,$tableName,$col,$generatePreprocessing);
          if (!isset($format->PreprocessingHints->DiscretizationHint)){
            $preprocessed=false;
          }
          //vytvorime dictionaries v FML
          $pmmlField=$pmmlDictionary->addChild('Field');
          $pmmlField->addAttribute('id',$pmmlId.$idCounter);
          $pmmlField->addChild('Name',(string)$col->name);
          $pmmlFieldIdentifier=$pmmlField->addChild('Identifier',(string)$col->name);
          $pmmlFieldIdentifier->addAttribute('name','Field');
          
          $bkefField=$bkefDictionary->addChild('Field');
          $bkefField->addAttribute('id',$bkefId.$idCounter);
          $maName=(string)$metaAttribute->Name;
          $fName=(string)$format->Name;
          $bkefField->addChild('Name',$maName.'###'.$fName);
          $maIdentifier=$bkefField->addChild('Identifier',$maName);
          $maIdentifier->addAttribute('name','MetaAttribute');
          $fIdentifier=$bkefField->addChild('Identifier',$fName);
          $fIdentifier->addAttribute('name','Format');
          //vytvoříme mapování
          $fieldMapping=$dictionaryMapping->addChild('FieldMapping');
          $fieldMapping->addChild('AppliesTo');
          $fieldRefPmml=$fieldMapping->AppliesTo->addChild('FieldRef');
          $fieldRefPmml->addAttribute('dictID',$pmmlId);
          $fieldRefPmml->addAttribute('id',$pmmlId.$idCounter);
          $fieldRefBkef=$fieldMapping->AppliesTo->addChild('FieldRef');
          $fieldRefBkef->addAttribute('dictID',$bkefId);
          $fieldRefBkef->addAttribute('id',$bkefId.$idCounter);
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
                $fieldPmml->addAttribute('id',$pmmlId.$idCounter);
                $fieldPmml->addAttribute('dictID',$pmmlId);
                $catRefPmml=$fieldPmml->addChild('CatRef',$valueStr);
                $catRefPmml->addAttribute('id','v'.$pmmlId.$idCounter.'_'.$valueId);
                $fieldBkef=$valueMapping->addChild('Field');
                $fieldBkef->addAttribute('id',$bkefId.$idCounter);
                $fieldBkef->addAttribute('dictID',$bkefId);
                $catRefBkef=$fieldBkef->addChild('CatRef',$valueStr);
                $catRefBkef->addAttribute('id','v'.$bkefId.$idCounter.'_'.$valueId);
              }
              unset($valuesArr);
            }
          }
          if (count(@$format->PreprocessingHints->DiscretizationHint)>0){
            $preprocessingHintName=(string)$format->PreprocessingHints->DiscretizationHint[0]->Name;
            $preprocessings=$fieldMapping->addChild('Preprocessings');
            $preprocessing=$preprocessings->addChild('Preprocessing');
            $preprocessing->addChild('PreprocessingHint',$preprocessingHintName);
            $preprocessing->addChild('AttributeName',$maName);
          }
          //zvýšíme počítadlo pro IDčka
          $idCounter++;
        }
      }else{
        return null;
      }                                       
      $this->isPreprocessed=$preprocessed;        
      //--projdeme jednotlive sloupce datoveho zdroje a najednou generujeme jak BKEF, tak PMML
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
        while(in_array($formatName.(($suffix==0)?'':'_'.$suffix),$formatNamesArr)){
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
          $preprocessed=false;    
          if (isset($col->items)){
            //mame hodnoty
            $colItemsCount=count($col->items->item);  
            if (($colItemsCount>0)&&($colItemsCount<=self::MAX_NUMERIC_ITEMS)){
              $this->generatePreprocessing_eachOne($preprocessingHints);
              $preprocessed=true;
            }
          }
          if (!$preprocessed){
            $this->generatePreprocessing_equidistant($preprocessingHints,(string)$col->statistics->min,(string)$col->statistics->max,self::EQUIDISTANT_INTERVALS_COUNT);
          }          
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
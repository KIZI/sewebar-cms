<?php

  /**
   *  Třída řešící FML z pohledu namapovaného PMML na BKEF
   */     
  class dbconnectModelFml extends JModel{
    private $fml;
    private $fieldMappingsArr; 
    private $dictionariesArr;
    private $pmmlFieldNamesArr;
    private $bkefFieldsArr;
    
    /**
     *  Funkce pro nastavení aktuálního simplexml s načteným FML
     */         
    public function setFml($fml){     
      $this->fml=$fml;                
      //$this->prepareFieldMappingsArr();
      //spustíme přípravné metody                                     
      if (!($this->prepareDictionariesArr()&&$this->preparePmmlFieldNamesArr())){   
        return false;
      }else{
        $this->prepareBkefFieldsArr();
        $this->prepareFieldMappingsArr();
        return true;
      }
      //
    }
    
    /**
     *  Funkce vracející FML jako instanci simpleXML
     */         
    public function getFml(){
      return $this->fml;
    } 
    
             
    public function getPmmlFieldNamesArr(){
      return $this->pmmlFieldNamesArr;
    }
    
    /**
     *  Metoda vracející mapovací pole
     */         
    public function getFieldMappingsArr(){     
      return $this->fieldMappingsArr;
    } 
    
    /**
     *  Funkce vracející pole s mapováním hodnot pro vybrané mapování PMML na BKEF
     */         
    public function getValuesMappingsArr($pmmlFieldName){ 
      if (!isset($this->pmmlFieldNamesArr[$pmmlFieldName])){return null;}
      $columnId=$this->pmmlFieldNamesArr[$pmmlFieldName]['id'];
      $bkefId=$this->fieldMappingsArr[$columnId]['bkefId'];
      if (!isset($this->bkefFieldsArr[$bkefId])){return null;}
      $bkefField=$this->bkefFieldsArr[$bkefId];
      
      $mapping=$this->getFieldMappingByNames($pmmlFieldName,$bkefField['Metaattribute'],$bkefField['Format']);
      if (@count($mapping->ValueMappings->ValueMapping)==0){return null;}
      
      //budeme zpracovavat mapovani hodnot
      $valuesArr=array();
      $pmmlDictId=$this->dictionariesArr['pmml'];
      $bkefDictId=$this->dictionariesArr['bkef'];
      
      foreach ($mapping->ValueMappings->ValueMapping as $valueMapping) {
      	//zpracovavame jedno konkretni mapovani hodnot
        $pmmlValue='';
        $bkefValue='';
        if (@count($valueMapping->Field)>0){
          foreach ($valueMapping->Field as $field) {
          	if ((string)$field['dictID']==$pmmlDictId){
              //jde o hodnotu z pmml           
              if ((string)$field['id']==$columnId){
                $pmmlValue=(string)$field->CatRef;    
              }
            }elseif((string)$field['dictID']==$bkefDictId){
              //jde o hodnotu z bkef               
              if ((string)$field['id']==$bkefId){
                $bkefValue=(string)$field->CatRef;
              }      //TODO - jde odkazovat také jen přes IDčka???
            }
          }
        }
        if (($pmmlValue!='')&&($bkefValue!='')){
          $valuesArr[$pmmlValue]=$bkefValue;
        }
      }
      return $valuesArr;  
    }
    
    
    /**
     *  Funkce pro ověření, zda máme nastavené mapování pro PMML field se zadaným názvem
     *  @param $name - název sloupce
     *  @return boolean          
     */         
    public function hasPreprocessingForPmmlField($pmmlFieldName){
      if (!isset($this->pmmlFieldNamesArr[(string)$pmmlFieldName])){return false;}
      $columnId=$this->pmmlFieldNamesArr[$pmmlFieldName]['id'];
      //kontrola, zda máme mapování pro tento sloupec
      return ((isset($this->fieldMappingsArr[$columnId]))&&(@$this->fieldMappingsArr[$columnId]['preprocessingHint']!=""));
    }
    
    /**
     *  Funkce pro zjištění mapování a zároveň pro vrácení identifikace preprocessing hintu
     *  @param $pmmlFieldName - název PMML sloupce
     *  @return array {"bkefMetaattributeName","bkefFormatName","preprocessingName"}      
     */
    public function getPmmlPreprocessing($pmmlFieldName){
      if (!isset($this->pmmlFieldNamesArr[$pmmlFieldName])){return null;}
      $columnId=$this->pmmlFieldNamesArr[$pmmlFieldName]['id'];
      $bkefId=$this->fieldMappingsArr[$columnId]['bkefId'];
      if (!isset($this->bkefFieldsArr[$bkefId])){return null;}
      $bkefField=$this->bkefFieldsArr[$bkefId];
      $preprocessingName=$this->fieldMappingsArr[$columnId]['preprocessingHint'];
      return array('metaattributeName'=>@$bkefField["MetaAttribute"],"formatName"=>@$bkefField["Format"],'preprocessingHint'=>$preprocessingName);
    }          
    
    /**
     *  Funkce pro uložení preprocessing hintu do FML
     */         
    public function setPreprocessingHint($pmmlFieldName,$bkefMetaattributeName,$bkefFormatName,$preprocessingName){
      $fieldMapping=$this->getFieldMappingByNames($pmmlFieldName,$bkefMetaattributeName,$bkefFormatName);
      if (!$fieldMapping){
        return false;
      }
      if (isset($fieldMapping->PreprocessingHint)){
        $fieldMapping->PreprocessingHint=$preprocessingName;
      }else{
        $fieldMapping->addChild('PreprocessingHint',$preprocessingName);
      }
      return true;
    } 
     
    /**
     *  Funkce vracející 
     */          
    public function getFieldMappingByNames($pmmlFieldName,$bkefMetaattributeName,$bkefFormatName){
      //zjistíme ID pro bkef
      $selectedBkefId="";
      if (count($this->bkefFieldsArr)>0){
        foreach ($this->bkefFieldsArr as $bkefId=>$bkefArr) {
        	if (($bkefArr['MetaAttribute']==$bkefMetaattributeName)&&($bkefArr['Format']==$bkefFormatName)){
            $selectedBkefId=$bkefId;
            break;
          }
        }
      }
      //zjistíme ID pro pmml
      if (isset($this->pmmlFieldNamesArr[$pmmlFieldName])){
        $selectedPmmlId=$this->pmmlFieldNamesArr[$pmmlFieldName]['id'];
      }else{
        $selectedPmmlId="";
      }
               
      if (($selectedPmmlId=="")||($selectedBkefId=="")){
        return null;
      }
        
      //zkusíme projít jednotlivá mapování a pokud najdeme to správné, tak ho vrátíme
      if (count(@$this->fml->DictionaryMapping->FieldMapping)>0){
        foreach ($this->fml->DictionaryMapping->FieldMapping as $fieldMapping) {
        	$bkefId=-1;
          $pmmlId=-1;                                     
          if (@count($fieldMapping->AppliesTo->FieldRef)>0){         
            foreach ($fieldMapping->AppliesTo->FieldRef as $fieldRef){     
            	if ((string)$fieldRef['dictID']==$this->dictionariesArr['pmml']){
                $pmmlId=(string)$fieldRef['id'];
              }elseif((string)$fieldRef['dictID']==$this->dictionariesArr['bkef']){
                $bkefId=(string)$fieldRef['id'];
              }
            }
          }
          
          if (($pmmlId==$selectedPmmlId)&&($bkefId==$selectedBkefId)){
            return $fieldMapping;
          }
        }
      }
    } 
    
    /**
     *  Metoda připravující pole se všemi dílčími mapováními
     */
    private function prepareFieldMappingsArr(){       
      $this->fieldMappingArr=array();               
      if (count(@$this->fml->DictionaryMapping->FieldMapping)>0){
        foreach ($this->fml->DictionaryMapping->FieldMapping as $fieldMapping) {
          //vynulujeme identifikatory
          $pmmlFieldId=-1;
          $bkefFieldId=-1;
        	//projdeme jednotlivá mapování a připravíme je do pole 
          if (count(@$fieldMapping->AppliesTo->FieldRef)==2){           
            foreach ($fieldMapping->AppliesTo->FieldRef as $fieldRef) {
            	if ((string)$fieldRef['dictID']==$this->dictionariesArr['pmml']){
                $pmmlFieldId=(string)$fieldRef['id'];
              }elseif((string)$fieldRef['dictID']==$this->dictionariesArr['bkef']){
                $bkefFieldId=(string)$fieldRef['id'];
              }
            }
          }      
          //pokud mame oba identifikatory,doplnime pole s prehledem vsech mapovani
          if (($pmmlFieldId>-1)&&($bkefFieldId>-1)){
            if (isset($fieldMapping->PreprocessingHint)&&((string)$fieldMapping->PreprocessingHint!='')){
              $preprocessingHint=(string)$fieldMapping->PreprocessingHint;
            }else{
              $preprocessingHint='';
            }
            $this->fieldMappingsArr[$pmmlFieldId]=array('bkefId'=>$bkefFieldId,'preprocessingHint'=>$preprocessingHint);
          }
        }
      }
    }
    
    /**
     *  Funkce vracející pole se záznamy o jednotlivých Dictionaries na základě BKEF
     */         
    public function getBkefFieldsArr(){       
      return $this->bkefFieldsArr;
    }
    
    /**
     *  Funkce připravující pole se záznamy jednotlivých  Dictionaries vztahující se k BKEF 
     */         
    private function prepareBkefFieldsArr(){
      $this->bkefFieldsArr=array();
      $bkefDictionary=$this->getDictionary($this->dictionariesArr['bkef']);
      if (count($bkefDictionary->Field)>0){
        foreach ($bkefDictionary->Field as $field) {
        	$fieldId=(string)$field['id'];
          $fieldMA='';
          $fieldFormat='';
          foreach($field->Identifier as $identifier) {
          	if((string)$identifier['name']=='MetaAttribute'){
              $fieldMA=(string)$identifier;
            }elseif((string)$identifier['name']=='Format'){
              $fieldFormat=(string)$identifier;
            }
          }
          if (($fieldMA!='')&&($fieldFormat!='')){
            $this->bkefFieldsArr[$fieldId]=array('MetaAttribute'=>$fieldMA,'Format'=>$fieldFormat);
          }        	
        }
      }
    }
    
    /**
     *  Funkce pro přípravu pole obsahujícího odkazy na IDčka přiřazená jednotlivým názvům fieldů
     */         
    private function preparePmmlFieldNamesArr(){
      $this->pmmlFieldNamesArr=array();                   
      $dictionary=$this->getDictionary($this->dictionariesArr['pmml']);   //exit(var_dump($dictionary));
      if (!count(@$dictionary->Field)){return false;}            
      //projdeme jednotlive field, zkusime najit jejich jmena a nahazime je do pole
      foreach ($dictionary->Field as $field) {        
        $name='';
        if (isset($field->Name)){
          $name=(string)$field->Name;
        }elseif(count($field->Identifier)>0){
          foreach ($field->Identifier as $identifier) {
          	if ((string)$identifier['name']=='Field'){
              $name=(string)$identifier;
            }
          }
        }
        if ($name!=''){
          //máme nastavené jméno - můžeme s tím dál pracovat
          $this->pmmlFieldNamesArr[$name]=array('id'=>(string)$field['id']);              //TODO zkontrolovat, proč se nedoplňují další položky
          if (isset($field['optype'])){
            $this->pmmlFieldNamesArr[$name]['optype']=(string)$field['optype'];
          }
          if (isset($field['dataType'])){
            $this->pmmlFieldNamesArr[$name]['dataType']=(string)$field['dataType'];
          }
        }
      }            
      return (count($this->pmmlFieldNamesArr)>0);
    }
    
    /**
     *  Funkce, která zjistí, jestli soubor s mapováními obsahuje PMML i BKEF odkazy, připraví IDčka jednotlivých dictionary
     */         
    private function prepareDictionariesArr(){          
      if (!count(@$this->fml->Dictionary)){return false;}
      foreach ($this->fml->Dictionary as $dictionary) {    
      	if ((string)$dictionary['sourceFormat']=='BKEF'){
          $this->dictionariesArr['bkef']=(string)$dictionary['id'];
        }elseif((string)$dictionary['sourceFormat']=="PMML"){
          $this->dictionariesArr['pmml']=(string)$dictionary['id'];
        }
      }                                                                                             
      return (isset($this->dictionariesArr['bkef'])&&isset($this->dictionariesArr['pmml']));
    }
    
    /**
     *  Funkce pro najití konkrétního dictionary
     */         
    private function getDictionary($dictId){
      if (!count(@$this->fml->Dictionary)){return null;}
      foreach ($this->fml->Dictionary as $dictionary) {
      	if ((string)$dictionary['id']==$dictId){
          return $dictionary;
        }
      }
    } 
    
    
    
  }
  
?>
<?php

  /**
   *  Třída řešící FML z pohledu namapovaného PMML na BKEF
   */     
  class dbconnectModelBkef extends JModel{
    private $bkef;
    private $basicMeta_namesArr;
    private $basicMeta_idsArr; 
    
    /**
     *  Funkce pro nastavení aktuálního simplexml s načteným FML
     */         
    public function setBkef($bkef){  
      $this->bkef=$bkef;                                  
      //spustíme přípravné metody                                     
      if (!($this->prepareBasicMetaArr())){   
        return false;
      }else{
        return true;
      }
      //
    }
    
    public function getBkef($xml=false){
      if ($xml){
        return $this->bkef->asXML();
      }else{
        return $this->bkef;
      }
    }
    
    /**
     *  Metoda pro předpřipravení polí pro vyhledávání základních metaattributů podle name a id
     *  pole jsou indexována pozicí metaattributu ve struktuře BKEF souboru     
     */         
    private function prepareBasicMetaArr(){  
      $this->basicMeta_namesArr=array();
      $this->basicMeta_idsArr=array();                         
      if (count($this->bkef->MetaAttributes->MetaAttribute)>0){   
        $maId=0;
        foreach ($this->bkef->MetaAttributes->MetaAttribute as $metaAttribute) {
        	if ((string)$metaAttribute['level']=='0'){
            $this->basicMeta_namesArr[$maId]=(string)$metaAttribute->Name;
            $this->basicMeta_idsArr[$maId]=(string)$metaAttribute['id'];
          }
          $maId++;
        }
      }      
      return ((count($this->basicMeta_namesArr)>0)&&(count($this->basicMeta_namesArr)==count($this->basicMeta_idsArr)));
    }
    
    /**
     *  Metoda vracející preprocessing hints pro daný metaatribut a formát ve struktuře simpleXML
     *  @param $metaattributeName název základního metaattributu
     *  @param $formatName název vybraného formátu          
     */         
    public function getPreprocessingHints($metaattributeName,$formatName){
      $format=$this->getFormat($metaattributeName,$formatName);
      if (!$format){return null;}
      return (@$format->PreprocessingHints);
    }
    
    /**
     *  Metoda vracející preprocessing hints pro daný metaatribut a formát ve struktuře simpleXML
     *  @param $metaattributeName název základního metaattributu
     *  @param $formatName název vybraného formátu          
     */         
    public function getFormat($metaattributeName,$formatName){    
      $maId=array_search($metaattributeName,$this->basicMeta_namesArr); 
      if ($maId===false){
        return null;
      }
      $metaAttribute=@$this->bkef->MetaAttributes->MetaAttribute[$maId];
      if (count(@$metaAttribute->Formats->Format)>0){
        foreach ($metaAttribute->Formats->Format as $format) {
        	if ((string)$format->Name==$formatName){
            return $format;
          }
        }
      }
      return null;
    }    
             
    
    /**
     *  Metoda ověřující, zda zvolený preprocessing hint existuje
     *  @param $metaattributeName název metaatributu
     *  @param $formatName název formátu
     *  @param $preprocessingName název zvoleného discretization (preprocessing) hintu               
     *  @return bool     
     */                  
    public function preprocessingHintExists($metaattributeName,$formatName,$preprocessingName){
      $preprocessingHints=$this->getPreprocessingHints($metaattributeName,$formatName);
      if (count($preprocessingHints->DiscretizationHint)>0){
        foreach ($preprocessingHints->DiscretizationHint as $discretizationHint) {
        	if ((string)$discretizationHint->Name==$preprocessingName){
            return true;
          }
        }
      }
      return false;
    }
    
    /**
     *  Metoda vracející konkrétní preprocessing hint
     *  @param $metaattributeName název metaatributu
     *  @param $formatName název formátu
     *  @param $preprocessingName název zvoleného discretization (preprocessing) hintu               
     *  @return bool     
     */                  
    public function getPreprocessingHint($metaattributeName,$formatName,$preprocessingName){
      $preprocessingHints=$this->getPreprocessingHints($metaattributeName,$formatName);   
      if (count($preprocessingHints->DiscretizationHint)>0){              
        foreach ($preprocessingHints->DiscretizationHint as $discretizationHint) {  
        	if ((string)$discretizationHint->Name==$preprocessingName){
            return $discretizationHint;
          }
        }
      }
      return null;
    }
    
    /**
     *  Funkce pro odstranění preprocessing hintu  
     */ 
    public function deletePreprocessingHint($metaattributeName,$formatName,$preprocessingName){
      $format=$this->getFormat($metaattributeName,$formatName);
      $preprocessingHints=$format->PreprocessingHints;
      $finalBinId=-1;
      if (count($preprocessingHints)>0){
        $binId=0;
        foreach ($preprocessingHints as $discretizationBin){
        	if ($discretizationBin->Name==$preprocessingName){
            $finalBinId=$binId;
            break;
          }
          $binId++;
        }
      }
      if ($finalBinId>-1){
        unset($format->PreprocessingHints[$finalBinId]);
        return true;
      }
      return false;
    }
    
    /**
     *  Funkce pro přidání nového preprocessing hintu
     */          
    private function addNewPreprocessingHint($metaattributeName,$formatName,$name,$createdInfo=null){
      $counter=1;
      $newName=$name;
      //vyreseni unikatnosti jmena preprocessing hintu
      while($this->preprocessingHintExists($metaattributeName,$formatName,$newName)){
        $counter++;
        $newName=$name.' ('.$counter.')';
      }
      //--vyreseni unikatnosti jmena preprocessing hintu
      
      $format=$this->getFormat($metaattributeName,$formatName);
      if (isset($format->PreprocessingHints)){
        $preprocessingHints=$format->PreprocessingHints;  
      }else{
        $preprocessingHints=$format->addChild('PreprocessingHints');
      }
      
      $timestampStr=date('r');
      $user=& JFactory::getUser();
      $userName=$user->get('name');
      
      $discretizationHint=$preprocessingHints->addChild('DiscretizationHint');
      $discretizationHint->addChild('Name',$newName);
      $created=$discretizationHint->addChild('Created');
      if ($createdInfo){
        $createdInfo=(array)$createdInfo;
        $created->addChild('Timestamp',$createdInfo['timestamp']);
        $created->addChild('Author',$createdInfo['author']);
      }else{
        $created->addChild('Timestamp',$timestampStr);
        $created->addChild('Author',$userName);
      }
      $lastModified=$discretizationHint->addChild('LastModified');
      $lastModified->addChild('Timestamp',$timestampStr);
      $lastModified->addChild('Author',$userName);
      return $discretizationHint;
    }    
    
    /**
     *  Funkce pro přidání each value one bin preprocessingu
     */         
    public function addNewPreprocessingHint_EachValueOneBin($metaattributeName,$formatName,$name='Each-One'){     //TODO doplnit detekci již existujícího preprocessing hintu stejného typu
      $preprocessingHints=$this->getPreprocessingHints($metaattributeName,$formatName);
      //kontrola, jestli už stejný PH neexistuje
      $existingName='';
      if (count($preprocessingHints->DiscretizationHint)>0){
        foreach ($preprocessingHints->DiscretizationHint as $discretizationHint) {
        	if (isset($discretizationHint->EachValueOneBin)){
            $existingName=(string)$discretizationHint->Name;
          }
        }
      }
      //kontrola, jestli už stejný PH neexistuje
      if ($existingName!=''){
        return $this->getPreprocessingHint($metaattributeName,$formatName,$existingName);
      }else{
        $discretizationHint=&$this->addNewPreprocessingHint($metaattributeName,$formatName,$name);
        $discretizationHint->addChild('EachValueOneBin');
        return $discretizationHint;
      }
    } 
    
    /**
     *  Funkce pro přidání nominal enumeration preprocessingu
     *  @param $dataArr = array(nominalBinName=>array(value,value,...),...)     
     */         
    public function addNewPreprocessingHint_NominalEnumeration($metaattributeName,$formatName,$name,$dataArr,$createdInfo=null){
      $discretizationHint=&$this->addNewPreprocessingHint($metaattributeName,$formatName,$name,$createdInfo);
      $nominalEnumeration=$discretizationHint->addChild('NominalEnumeration');
      
      $namesArr=array();
      
      if (count($dataArr)>0){
        foreach ($dataArr as $name=>$valuesArr) {
          $binName=trim($name);
          $nameIndex=0;
          while(in_array($binName,$namesArr)){
            $nameIndex++;
            $binName=$name.' '.$nameIndex;
          }
          $namesArr[]=$binName;
        
        	if (count($valuesArr)>0){
            $nominalBin=$nominalEnumeration->addChild('NominalBin');
            $nominalBin->addChild('Name',$binName);
            foreach ($valuesArr as $value) {
              $nominalBin->addChild('Value',$value);	
            }
          }
        }
      }
      return $discretizationHint;
    }        
    
    /**
     *  Funkce pro přidání interval enumeration preprocessingu
     *  @param $metaattributeName
     *  @param $formatName
     *  @param $name     
     *  @param $dataArr = array(intervalBinName=>array(array('closure','leftMargin','rightMargin'),...),...)     
     */
    public function addNewPreprocessingHint_IntervalEnumeration($metaattributeName,$formatName,$name,$dataArr,$createdInfo=null){
      $discretizationHint=&$this->addNewPreprocessingHint($metaattributeName,$formatName,$name,$createdInfo);
      $intervalEnumeration=$discretizationHint->addChild('IntervalEnumeration');
                                   
      $namesArr=array();
      
      if (count($dataArr)>0){
        foreach ($dataArr as $name=>$intervalsArr) {
          $binName=trim($name);
          $nameIndex=0;
          while(in_array($binName,$namesArr)){
            $nameIndex++;
            $binName=$name.' '.$nameIndex;
          }
          $namesArr[]=$binName;
                          
        	if (count($intervalsArr)>0){
            $intervalBin=$intervalEnumeration->addChild('IntervalBin');
            $intervalBin->addChild('Name',$binName);  
            foreach ($intervalsArr as $intervalArr) {
              $interval=$intervalBin->addChild('Interval');
              $interval->addAttribute('closure',$intervalArr['closure']);
              $interval->addAttribute('leftMargin',$intervalArr['leftMargin']);
              $interval->addAttribute('rightMargin',$intervalArr['rightMargin']);	
            } 
          }
        }
      }
      return $discretizationHint;
    }
    
    /**
     *  Funkce pro přidání equidistant interval preprocessingu
     *  @param $paramsArr = array(start=>{number},end=>{number},step=>{number})     
     */         
    public function addNewPreprocessingHint_EquidistantInterval($metaattributeName,$formatName,$name,$paramsArr,$createdInfo=null){
      $discretizationHint=&$this->addNewPreprocessingHint($metaattributeName,$formatName,$name,$createdInfo);
      $equidistantInterval=$discretizationHint->addChild('EquidistantInterval');
      
      //uprava parametru
      $start=$paramsArr['start'];
      $end=$paramsArr['end'];
      if ($start>$end){
        $x=$end;
        $end=$start;
        $start=$end;
      }
      if ($start==$end){
        $end=$start+1;
      }
      $step=$paramsArr['step'];
      while($step>($end-$start)){
        $step=$step/2;
      }
      
      $equidistantInterval->addChild('Start',$start);
      $equidistantInterval->addChild('End',$end);
      $equidistantInterval->addChild('Step',$step);
      
      return $discretizationHint;
    }
    
             
    
    
  }
  
?>
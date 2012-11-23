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
    
    
  }
  
?>
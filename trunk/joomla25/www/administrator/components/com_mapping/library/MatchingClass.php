<?php
/**
 *  Třída pro stanovení podobností
 */ 
class MatchingClass{
  var $xml1;
  var $xml2;
  var $xml2trigramsArr;
  var $xgramLength;//délka "trigramu" - načítá se z nastavení
  var $minusInf;
  var $plusInf;
  var $expClass;
  
  /**
   *  Výchozí inicializace - načtení potřebných tříd...
   */     
  function __construct(){                                          
    require_once(JPATH_COMPONENT.DS.'library'.DS.'StringClass.php');
    require_once(JPATH_COMPONENT.DS.'library'.DS.'NumericClass.php');
    require_once(JPATH_COMPONENT.DS.'library'.DS.'ExpirienceClass.php');
    require_once(JPATH_COMPONENT.DS.'models'.DS.'config.php');
    $configModel=new ConfigModel();
    $constants=$configModel->loadConfigs('matchRate');
    if (count($constants)>0){
      foreach ($constants as $constant) {
      	define($constant->name,$constant->value);
      }
    }
    $this->expClass=new ExpirienceClass(false);
    $this->xgramLength=$configModel->loadConfigValue('constant','COLUMN_XGRAM_LENGTH');
    $this->minusInf=$configModel->loadConfigValue('constant','MINUS_INFINITE');
    $this->plusInf=$configModel->loadConfigValue('constant','PLUS_INFINITE');
  }   
  
  public function loadXML($xml1,$xml2){
    $this->xml1=simplexml_load_string($xml1);
    $this->xml2=simplexml_load_string($xml2);
    if ((!$this->xml1)||($this->xml2)){
      return false;
    }else{
      return true;
    }
  }
  /**
   *  Funkce pro napárování  
   */     
  public function getMatchingXML(){          
    if ((count($this->xml1->col)>0)&&(count($this->xml2->col)>0)){
      $outXml=simplexml_load_string('<matchData><keysA /><keysB /><match /></matchData>');
      //vypiseme nazvy klicu z 
      $keysA=$outXml->keysA[0];
      foreach ($this->xml1->col as $col) {
      	$key=$keysA->addChild('key');
        $key->addAttribute('name',(string)$col->name.(isset($col->formatName)?'###'.((string)$col->formatName):''));
        if (isset($col->enumeration[0])){      
          $enum=$key->addChild("enumeration");
          foreach ($col->enumeration[0]->value as $value) {
          	$enum->addChild("value",$value);
          }
        }elseif(isset($col->numberEnumeration[0])){
          $enum=$key->addChild("numberEnumeration");
          foreach ($col->enumeration[0]->value as $value) {
          	$enum->addChild("value",$value);
          }
        }elseif(isset($col->intervals[0])){
          $intervals=$key->addChild('intervals');
          foreach ($col->intervals[0]->interval as $origInterval){
            $interval=$intervals->addChild('interval');
            $interval->addAttribute('lbound',(string)$origInterval['lbound']);
            $interval->addAttribute('lvalue',(string)$origInterval['lbound']);
            $interval->addAttribute('rbound',(string)$origInterval['rbound']);
            $interval->addAttribute('rvalue',(string)$origInterval['rvalue']);
          }
        }   
      }        
      $keysB=$outXml->keysB[0];
      foreach ($this->xml2->col as $col) {
      	$key=$keysB->addChild('key');
        $key->addAttribute('name',(string)$col->name.(isset($col->formatName)?'###'.((string)$col->formatName):''));
      	if (isset($col->enumeration)){
          $enum=$key->addChild("enumeration");
          foreach (@$col->enumeration[0]->value as $value) {
          	$enum->addChild("value",$value);
          }
        }elseif(isset($col->numberEnumeration[0])){
          $enum=$key->addChild("numberEnumeration");
          foreach ($col->enumeration[0]->value as $value) {
          	$enum->addChild("value",$value);
          }
        }elseif(isset($col->intervals[0])){
          $intervals=$key->addChild('intervals');
          foreach ($col->intervals[0]->interval as $origInterval){
            $interval=$intervals->addChild('interval');
            $interval->addAttribute('lbound',(string)$origInterval['lbound']);
            $interval->addAttribute('lvalue',(string)$origInterval['lbound']);
            $interval->addAttribute('rbound',(string)$origInterval['rbound']);
            $interval->addAttribute('rvalue',(string)$origInterval['rvalue']);
          }
        }
      }
      //
      $match=$outXml->match[0];
      foreach ($this->xml1->col as $colA){     
        $keyA=$match->addChild('keyA');
        $keyA->addAttribute('name',(string)$colA->name.(isset($colA->formatName)?'###'.((string)$colA->formatName):''));
        foreach ($this->xml2->col as $colB){
          /********************************************/
          /*porovnání jmen*/
          $similarityNames=$this->matchNames((string)$colA->name.(isset($colA->formatName)?'###'.((string)$colA->formatName):''),(string)$colB->name.(isset($colB->formatName)?'###'.((string)$colB->formatName):''))*MATCH_NAMES_RATE;
          /*zjištění datového typu a porovnání podle nich*/
          $similarityValues=0;
          if ((string)$colA->dataType=='string'){
            //pracujeme s textovými řetězci
            if ((string)$colB->dataType=="string"){      
              if (((string)$this->xml1->sourceType=="PMML")&&((string)$this->xml2->sourceType=="BKEF")){
                $similarityValues=$this->matchTrigrams($colA,$colB,true)*MATCH_DATATYPE_RATE;
              }else{
                $similarityValues=$this->matchTrigrams($colA,$colB,false)*MATCH_DATATYPE_RATE;
              }
            }
          }else{
            //pracujeme s čísly
            if (((string)$colA->dataType!="string")&&((string)$colB->dataType!="string")){
              //zjistime, jestli porovnavame rovnocenne soubory, nebo PMML a BKEF
              if (((string)$this->xml1->sourceType=="PMML")&&((string)$this->xml2->sourceType=="BKEF")){
                $coverageMode=true; 
              }else{
                $coverageMode=false;
              }
              if ((isset($colA->numberEnumeration[0]))&&(isset($colB->numberEnumeration[0]))){              
                /*porovnavame vzajemne 2 vycty cisel*/
                $similarityValues=$this->matchNumberEnumerationsCoverage($colA,$colB,$coverageMode)*MATCH_DATATYPE_RATE;
              }elseif ((isset($colA->numberEnumeration[0]))&&(isset($colB->intervals[0]))){   
                /*porovnavame vycet cisel vuci intervalu(m) - tato varianta neni dostupna u porovnavani BKEF:BKEF(BKEF neumi numberEnumeration)*/
                $similarityValues=$this->matchEnumIntervalCoverage($colA,$colB,$coverageMode)*MATCH_DATATYPE_RATE;
              }else{
                /*porovnavame vzajemne 2 sady intervalu*/
                $similarityValues=$this->matchIntervalsCoverage($colA,$colB,$coverageMode)*MATCH_DATATYPE_RATE;
              }
              
            }
          }
          /*porovnání podle předchozích zkušeností aplikace*/
//          $similarity+=MATCH_EXPIRIENCE_RATE*$this->getExpirience((string)$colA->name.(isset($colA->formatName)?'###'.((string)$colA->formatName):''),(string)$colA->dataType,(string)$colB->name.(isset($colB->formatName)?'###'.((string)$colB->formatName):''),(string)$colB->dataType);
          $similarityExpirience=MATCH_EXPIRIENCE_RATE*$this->getExpirience((string)$colA->name.(isset($colA->formatName)?'###'.((string)$colA->formatName):''),(string)$colB->name.(isset($colB->formatName)?'###'.((string)$colB->formatName):''));
          /********************************************/
          $similarity=($similarityNames+$similarityValues+$similarityExpirience);
          if ($similarity>0){
            $keyB=$keyA->addChild('keyB');
            $keyB->addAttribute('name',(string)$colB->name.(isset($colB->formatName)?'###'.((string)$colB->formatName):''));
            $keyB->addAttribute('ratioNames',$similarityNames);
            $keyB->addAttribute('ratioValues',$similarityValues);
            $keyB->addAttribute('ratioExpirience',$similarityExpirience);
            $keyB->addAttribute('ratio',$similarity);
          }
        }     
      }             
      return $outXml->asXML();
    }else{
      return false;
    }
  } 
  
  
  /**
   *  Funkce pro porovnání jmen sloupců
   */     
  private function matchNames($name1,$name2){           
    return StringClass::similarity($name1,$name2);
  }
  
  /**
   *  Funkce pro zjištení podobnosti výčtových sloupců na základě trigramů
   */     
  private function matchTrigrams($column1,$column2,$coverage=false){     
    $colATrigramsArr=StringClass::getTrigramsArr($this->getValues($column1));
    $col2name=(string)$column2->name.(isset($column2->formatName)?'###'.((string)$column2->formatName):'');
    if (!isset($this->xml2trigramsArr[$col2name])){
      //zatím nemáme určené trigramy pro sloupec ze druhého souboru
      $this->xml2trigramsArr[$col2name]=StringClass::getTrigramsArr($this->getValues($column2));
      $this->xml2trigramsSizeArr[$col2name]=StringClass::getVectorSize($this->xml2trigramsArr[$col2name]);  
    }           
    if ($coverage){
      return StringClass::getTrigramsCoverage($colATrigramsArr,StringClass::getVectorSize($colATrigramsArr),$this->xml2trigramsArr[$col2name],$this->xml2trigramsSizeArr[$col2name]);
    }else{
      return StringClass::getTrigramsSimilarity($colATrigramsArr,StringClass::getVectorSize($colATrigramsArr),$this->xml2trigramsArr[$col2name],$this->xml2trigramsSizeArr[$col2name]);
    }
    
  }
  
  /**
   *  Funkce vracející pole s hodnotami...
   */     
  private function getValues($column){
    $returnArr=array();
    if (count($column->enumeration[0]->value)>0){
      foreach ($column->enumeration[0]->value as $value) {
      	$returnArr[]=(string)$value;
      }
    }elseif (count($column->numberEnumeration[0]->value)>0){
      foreach ($column->numberEnumeration[0]->value as $value) {
      	$returnArr[]=(string)$value;
      }
    }
    return $returnArr;
  }
  
  /**
   *  Funkce vracející pole s intervaly
   */     
  private function getIntervals($column){
    $returnArr=array();
    if (count($column->intervals[0]->interval)>0){
      foreach ($column->intervals[0]->interval as $interval) {
      	$returnArr[]=new Interval($interval["lvalue"]+0,$interval["rvalue"]+0,($interval["lbound"]=="closed"),($interval["rbound"]=="closed"),$this->minusInf,$this->plusInf);
      }
    }
    return $returnArr;
  }
  
  /**
   *  Funkce pro porovnání jmen sloupců
   */     
  private function getExpirience($name1,$name2){   
    return $this->expClass->getExpirience($name1,$name2);          
  }
  
  /*---funkce pro porovnavani cisel---*/
  private function matchNumberEnumerationsCoverage($col1,$col2,$coverageMode=false){
    return NumericClass::numericEnumsCoverage($this->getValues($col1),$this->getValues($col2),$this->minusInf,$this->plusInf);
  }
  
  private function matchEnumIntervalCoverage($col1,$col2,$coverageMode=false){
    return NumericClass::IntEnumCoverage($this->getValues($col1),$this->getIntervals($col2));
  }
  
  private function matchIntervalsCoverage($col1,$col2,$coverageMode=false){
    return NumericClass::IntIntMatch($this->getIntervals($col1),$this->getIntervals($col2));
  }
      
} 
?>

<?php
/**********************************************************************************************************************************************/
abstract class AbstractValuesAssignClass
{ 
  //variables
  var $valuesMapArr;
  var $finalArr;
  
  /**
	 *   Konstruktor
	 */   	
  function __construct($valuesMapArr,$finalArr) {
    $this->valuesMapArr=$valuesMapArr;   
    $this->finalArr=$finalArr;
  }
  
  /**
   *  Funkce pro zadání mapování hodnot
   *  finalArr - pole s mapováním      
   *  keyMainA - klíč A souboru v mapování
   *  valueA - hodnota z A
   *  valueB - hodnota z B          
   */ 
  public function addValuesMap($keyMainA,$valueA,$valueB){  
    if (($valueA!='')&&($valueB!=0)&&(count($this->finalArr)>0)){         
      //máme zadané všechny potřebné parametry - zadáme mapování
      $keyA=array_search($valueA,$this->finalArr[$keyMainA]['valuesA']);
      $keyB=array_search($valueB,$this->finalArr[$keyMainA]['valuesB']);
      if (!(($keyA===false)||($keyB===false))){       
        //pridame hodnotu do namapovani
        if (!is_array($this->finalArr[$keyMainA]['mappedValues'][$valueA])){
          $this->finalArr[$keyMainA]['mappedValues'][$valueA]=array();
        }
        $this->finalArr[$keyMainA]['mappedValues'][$valueA][]=$valueB;
        //vyresime situaci, kdy byla dana hodnota v automatickych navrzich mapovani...
        if (@in_array($valueB, @$this->finalArr[$keyMainA]['autoMappedValues'][$valueA])){
          if (!is_array($this->finalArr[$keyMainA]['usedAutoMappedValues'])){
            $this->finalArr[$keyMainA]['usedAutoMappedValues']=array();
          }                                                                   
          $this->finalArr[$keyMainA]['usedAutoMappedValues'][$valueA][]=$valueB;    
          $valueBIndex=array_search($valueB, $this->finalArr[$keyMainA]["autoMappedValues"][$valueA]);
          if (!($valueBIndex===false)){
            unset($this->finalArr[$keyMainA]['autoMappedValues'][$valueA][$valueBIndex]);
            if (count($this->finalArr[$keyMainA]['autoMappedValues'][$valueA])<1){
              unset($this->finalArr[$keyMainA]['autoMappedValues'][$valueA]);
            }
          }
        }
        //
      }
    }
  } 
  
  public function removeValuesMap($keyMainA,$valueA,$valueB){
    if (($valueA!='')&&($valueB!=0)&&(count($this->finalArr)>0)){
      //máme zadané všechny potřebné parametry - zrušíme mapování
      if (@in_array($valueB,$this->finalArr[$keyMainA]['mappedValues'][$valueA])){
        $this->finalArr[$keyMainA]['valuesA'][]=$valueA;
        $this->finalArr[$keyMainA]['valuesB'][]=$valueB;
        //odebereme hodnotu z namapovanych dvojic
        $valueBIndex=array_search($valueB, $this->finalArr[$keyMainA]["mappedValues"][$valueA]);
        if (!($valueBIndex===false)){
          unset($this->finalArr[$keyMainA]['mappedValues'][$valueA][$valueBIndex]);
          if (count($this->finalArr[$keyMainA]['mappedValues'][$valueA])<1){
            unset($this->finalArr[$keyMainA]['mappedValues'][$valueA]);
          }
        }
      }
      //mame parametry, zkusime zrusit naparovani z automatickeho namapovani...
      if (@in_array($valueB,$this->finalArr[$keyMainA]['autoMappedValues'][$valueA])){
        $this->finalArr[$keyMainA]['valuesA'][]=$valueA;
        $this->finalArr[$keyMainA]['valuesB'][]=$valueB;
        //odebereme hodnotu z namapovanych dvojic
        $valueBIndex=array_search($valueB, $this->finalArr[$keyMainA]["autoMappedValues"][$valueA]);
        if (!($valueBIndex===false)){
          unset($this->finalArr[$keyMainA]['autoMappedValues'][$valueA][$valueBIndex]);
          if (count($this->finalArr[$keyMainA]['autoMappedValues'][$valueA])<1){
            unset($this->finalArr[$keyMainA]['autoMappedValues'][$valueA]);
          }
        }
      }
    }
  }


  public function unconfirmValuesMap($keyMainA,$valueA,$valueB){        //  exit('sem');
    if (($valueA!='')&&($valueB!=0)&&(count($this->finalArr)>0)){      //echo(var_dump($finalArr[$keyMainA]));
      //máme zadané všechny potřebné parametry - zrušíme mapování
      if (@in_array($valueB,$this->finalArr[$keyMainA]['mappedValues'][$valueA])){
        $this->finalArr[$keyMainA]['valuesA'][]=$valueA;
        $this->finalArr[$keyMainA]['valuesB'][]=$valueB;

        //odebereme hodnotu z namapovanych dvojic
        $valueBIndex=array_search($valueB, $this->finalArr[$keyMainA]["mappedValues"][$valueA]);
        if (!($valueBIndex===false)){
          unset($this->finalArr[$keyMainA]['mappedValues'][$valueA][$valueBIndex]);
          if (count($this->finalArr[$keyMainA]['mappedValues'][$valueA])<1){
            unset($this->finalArr[$keyMainA]['mappedValues'][$valueA]);
          }
        }
        //pokud mame hodnotu v automatickych navrzich mapovani - event. ji vrátíme do automatickych navrhu
        if (is_array(@$this->finalArr[$keyMainA]['usedAutoMappedValues'][$valueA])){
          if (in_array($valueB,$this->finalArr[$keyMainA]['usedAutoMappedValues'][$valueA])){ //  exit('sem2');
            //pridame hodnotu zpatky - nejdriv zjistime, jestli mame pole...
            if (!is_array($this->finalArr[$keyMainA]['autoMappedValues'])){
              $this->finalArr[$keyMainA]['autoMappedValues']=array();
            }
            if (!is_array($this->finalArr[$keyMainA]['autoMappedValues'][$valueA])){
              $this->finalArr[$keyMainA]['autoMappedValues'][$valueA]=array();
            }
            $this->finalArr[$keyMainA]['autoMappedValues'][$valueA][]=$valueB;      //  exit(var_dump($finalArr[$keyMainA]['autoMappedValues']));
            /*
            //zjistime,pod jakym indexem mame ValueB a odstranime ji z pole usedAutoMappedValues
            $valueBIndex=array_search($valueB,$finalArr[$keyMainA]['autoMappedValues'][$valueA]);
            if (!($valueBIndex===false)){
              unset($finalArr[$keyMainA]['autoMappedValues'][$valueA][$valueBIndex]);
            }*/
          }
          //pokud je velikost pole nulova,tak uz ho nepotrebujeme...
          if (count($this->finalArr[$keyMainA]['mappedValues'][$valueA])==0){
            unset($this->finalArr[$keyMainA]['mappedValues'][$valueA]);
          }
        }
      }
    }
  }
                   
  /**
   *  Funkce vracející finalArr
   */                        
  public function getFinalArr(){
    return $this->finalArr;
  }
  
  /**
   *  Inicializační mapování...
   */     
  public abstract function initMapping();
} // END class Values


?>
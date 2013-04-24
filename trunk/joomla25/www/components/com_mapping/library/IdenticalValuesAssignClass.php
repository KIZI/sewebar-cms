<?php
/**
 *  Třída pro automatické přiřazení identických hodnot
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractValuesAssignClass.php');

class IdenticalValuesAssignClass extends AbstractValuesAssignClass{ 
	
  
  public function initMapping(){        
    //plně manuální režim - nic neřešíme...
    $this->finalArr=$this->identicalValues($this->finalArr);
  }
	
	
	  /**
   *  Funkce, která projde finalArr a pokud jsou uvedené hodnoty pro A i B, tak mezi nimi najde identické a ty přesune do mappedValues
   */     
  private static function identicalValues($finalArr){
    if (count($finalArr)>0){
      foreach ($finalArr as $key=>$arr) {
      	//projdeme cele finalArr
      	if ((isset($arr['valuesA']))&&(isset($arr['valuesB']))){
          //máme definované hodnoty pro A i B => můžeme je porovnávat
          if (count($arr['valuesA'])>0){
            foreach ($arr['valuesA'] as $keyA=>$valueA) {
              $keyB=array_search($valueA,$arr['valuesB']);
            	if (!($keyB===false)){
                if (!@in_array($valueA,$finalArr[$key]['mappedValues'][$valueA])){
                  //budeme to resit jen v okamziku, kdy nemame dany par v uzivatelem namapovanych hodnotach  
                  if(!isset($finalArr[$key]['autoMappedValues'])){$finalArr[$key]['mappedValues']=array();}
                  $finalArr[$key]['autoMappedValues'][$valueA]=array($valueA);
                }  
              }
            }
          }
        }
      }
    }
    return $finalArr;
  }
	
}       
?>

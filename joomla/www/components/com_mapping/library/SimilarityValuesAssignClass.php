<?php
/**
 *  Třída pro automatické přiřazení identických hodnot
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractValuesAssignClass.php');
require_once(JPATH_COMPONENT.DS.'library'.DS.'StringClass.php');

class SimilarityValuesAssignClass extends AbstractValuesAssignClass{ 
  
  var $ngramsLength=2;//TODO - načítání délky
  
  public function initMapping(){        
    $this->finalArr=$this->mapValues($this->finalArr);
  }
	
	
	/**
   *  Funkce, která projde finalArr a pokud jsou uvedené hodnoty pro A i B, tak mezi nimi najde identické a ty přesune do mappedValues
   */     
  private function mapValues($finalArr){ 
    if (count($finalArr)>0){
      foreach ($finalArr as $key=>$arr) {
      	//projdeme cele finalArr
      	if ((isset($arr['valuesA']))&&(isset($arr['valuesB']))){
          if (!(count($arr['valuesA']>0))&&(count($arr['valuesB']>0))){continue;}
          //máme definované hodnoty pro A i B => můžeme je porovnávat
          foreach($arr['valuesA'] as $keyA=>$valueA){
            $maxSimilarityValue=-1;
            $maxSimilarityKey='';
            foreach($arr['valuesB'] as $keyB=>$valueB){
              $similarity=StringClass::similarity($this->valuesMapArr[$valueA],$this->valuesMapArr[$valueB]);
              if($similarity>$maxSimilarityValue){
                $maxSimilarityValue=$similarity;
                $maxSimilarityKey=$keyB;
              }
            }
            if ($maxSimilarityValue>0){
              $valueA=$arr["valuesA"][$keyA];        
              $valueB=$arr["valuesB"][$maxSimilarityKey];        
              if (!@in_array($valueA,$finalArr[$key]['mappedValues'][$valueA])){
                //budeme to resit jen v okamziku, kdy nemame dany par v uzivatelem namapovanych hodnotach  
                if(!isset($finalArr[$key]['autoMappedValues'])){$finalArr[$key]['mappedValues']=array();}
                $finalArr[$key]['autoMappedValues'][$valueA]=array($valueB);
              }
            }
          }
          /**/
        }
      }
    }                
    return $finalArr;
  }
	
}       
?>

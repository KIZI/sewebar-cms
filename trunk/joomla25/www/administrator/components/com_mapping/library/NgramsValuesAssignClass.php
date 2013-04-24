<?php
/**
 *  Třída pro automatické přiřazení identických hodnot
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractValuesAssignClass.php');
require_once(JPATH_COMPONENT.DS.'library'.DS.'StringClass.php');

class NgramsValuesAssignClass extends AbstractValuesAssignClass{ 
  
  var $ngramsLength;
  
  public function initMapping(){        
    require_once(JPATH_COMPONENT.DS.'models'.DS.'config.php');
    $configModel=new ConfigModel();
    $this->ngramsLength=$configModel->loadConfigValue('constant','VALUES_MAPPING_NGRAMS_LENGTH');
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
          $ngramsArrA=$this->generateNgrams($arr['valuesA']);   
          $sizesArrA=$this->generateSizes($ngramsArrA);       
          $ngramsArrB=$this->generateNgrams($arr['valuesB']);
          $sizesArrB=$this->generateSizes($ngramsArrB);
          /*samotné mapování ngramových vektorů*/
          foreach($ngramsArrA as $keyA=>$ngramA){
            $maxSimilarityValue=-1;
            $maxSimilarityKey='';       
            foreach($ngramsArrB as $keyB=>$ngramB){   
              $value=StringClass::getTrigramsSimilarity($ngramA,$sizesArrA[$keyA],$ngramB,$sizesArrB[$keyB]);
              if ($value>$maxSimilarityValue){
                $maxSimilarityValue=$value;
                $maxSimilarityKey=$keyB;
              }
            }     
            if ($maxSimilarityValue>0){
              /*ulozeni naparovani dle podobnosti*/
              $valueA=$arr["valuesA"][$keyA];        
              $valueB=$arr["valuesB"][$maxSimilarityKey];        
              if (!@in_array($valueA,$finalArr[$key]['mappedValues'][$valueA])){
                //budeme to resit jen v okamziku, kdy nemame dany par v uzivatelem namapovanych hodnotach  
                if(!isset($finalArr[$key]['autoMappedValues'])){$finalArr[$key]['mappedValues']=array();}
                $finalArr[$key]['autoMappedValues'][$valueA]=array($valueB);
              }
              /**/
            }
          }
          /**/
        }
      }
    }                
    return $finalArr;
  }
  
  /**
   *  Funkce pro vygenerování ngramů
   */     
  private function generateNgrams($arr){        
    $outputArr=array();                         
    foreach ($arr as $key=>$value) {       
      $outputArr[$key]=StringClass::getTrigramsArr(array($this->valuesMapArr[$value]),true,$this->ngramsLength);          	
    }
    return $outputArr;
  }
	
	/**
	 *  Funkce pro spočítání velikostí u všech vektorů
	 */   	
	private function generateSizes($ngramsArr){
    $outputArr=array();
    foreach ($ngramsArr as $key=>$ngram) {        
    	$outputArr[$key]=StringClass::getVectorSize($ngram);
    }
    return $outputArr;
  }
	
}       
?>

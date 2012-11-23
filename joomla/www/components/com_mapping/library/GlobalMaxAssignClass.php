<?php
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractAssignClass.php');
/**
 *  Třída pro automatizované přiřazování nejlepší kombinace propojení klíčů
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
class GlobalMaxAssignClass extends AbstractAssignClass{ // BEGIN class Assign     
	
	var $bestArr;
	var $dataArrX;
	
	/**
	 *   Konstruktor
	 */   	
  function __construct($dataArr,$finalArr) {
    parent::__construct($dataArr,$finalArr);      
  }
	
	
	/**
	 *   Funkce vracející asociační pole dle automatického napárování
	 *   @return $finalArr;	 
	 */   	
	public function getAssignArr(){                                  
    $this->execute();
    return $this->bestArr;    
  }
	
	/**
	 *   Funkce pro vytvoření pole s asociačním namapováním
	 */   	
	public function execute(){     
	  if (isset($this->finalArr)){
      $this->bestArr=$this->finalArr;
    }else{
      $this->bestArr=array();
    }
    
    $this->dataArrX=$this->removeFinalKeys($this->dataArr,$this->finalArr);
    //pripravime pole s nejlepsimi ohodnocenimi
    $bestValuesArr=$this->prepareBestValuesArr($this->dataArrX);  
    //vyhodnotime globalne nejlepe ohodnocene polozky
    while((count($this->dataArrX)>0)){
      //prochazime tak dlouho, dokud mame nejake polozky
      $key2=$this->getArrMax($bestValuesArr);
      $key=$bestValuesArr[$key2]['key'];     
      while(!isset($this->bestArr[$key])){    
        //dokud plati, ze vime o nejlepsi kombinaci, tak ji pouzijeme
        $ratio=$bestValuesArr[$key2]['ratio'];
        unset($bestValuesArr[$key2]);
        //ulozeni                                     
        $this->bestArr[$key]=array('name'=>$key2,'value'=>$this->dataArr[$key][$key2]);
        //procisteni pracovniho pole
        $this->dataArrX=$this->removeKeys($this->dataArrX,$key2);    
        $this->dataArrX=$this->removeBlankKeys($this->dataArrX);
        unset($this->dataArrX[$key]);
        //prechod k dalsimu prvku
        $key2=$this->getArrMax($bestValuesArr); 
        $key=$bestValuesArr[$key2]['key']; 
      }  
      //znovuvygenerovani seznamu                                 
      unset($bestValuesArr);
      $bestValuesArr=$this->prepareBestValuesArr($this->dataArrX);
      $key2=$this->getArrMax($bestValuesArr);
      $key=$bestValuesArr[$key2]['key'];
    }         
  }
  
	
	/**
	 * Funkce vracející klíč, pod kterým je uložena nejlépe ohodnocená položka
	 */   	
	private static function getArrMax($array){
    $maxValue=-1;
    $maxKey='';
    foreach ($array as $key=>$arr) {
    	if ($arr['ratio']>$maxValue){
        $maxKey=$key;
        $maxValue=$arr['ratio'];
      }
    }
    return $maxKey;
  }
  
  /**
   *  Funkce pro přípravu pole s nejlépe ohodnocenými podobnostmi napříč celým souborem
   */     
  private function prepareBestValuesArr($array){
    $bestValuesArr=array();       
    if (count($array)>0){                   
      foreach ($array as $key=>$arr) {
      	if (count($arr)>0){
          //subpole má nějaké přiřaditelné položky
          foreach ($arr as $key2=>$value) {
            if (!isset($bestValuesArr[$key2])){
              $bestValuesArr[$key2]=array('ratio'=>$value['ratio'],'key'=>$key);
            }else{
              if($bestValuesArr[$key2]['ratio']<$value['ratio']){
                $bestValuesArr[$key2]['key']=$key;
                $bestValuesArr[$key2]['ratio']=$value['ratio'];
              }
            }
          }
        }
      }
    }                             
    return $bestValuesArr;
  }
  
  /**
   *   Funkce, která vrací část $dataArr, která ještě nemá výsledky v $finalArr
   */               
  private function removeFinalKeys($dataArr,$finalArr){
    $keysArr=array();
    $namesArr=array();
    //pripravime info o tom, jaké klíče a jejich přiřazené subklíče máme už ve final
    if (count($finalArr)>0)
      foreach ($finalArr as $key=>$arr) {
      	$keysArr[]=$key;
     	  if (!in_array($arr['name'],$namesArr)){
          $namesArr[]=$arr['name'];
        }
      } 
    //profiltrujeme $dataArr
    if (count($dataArr)>0){
      if (count($keysArr)>0){
        foreach ($dataArr as $key=>$arr) {
       	  if (in_array($key,$keysArr)){
            unset($dataArr[$key]); 
          }
        }
      }
      if (count($namesArr)){
        foreach ($namesArr as $name) {
       	  $dataArr=$this->removeKeys($dataArr,$name);
        }
      }
    }
    return $this->removeBlankKeys($dataArr);    
  }
	
}      
?>

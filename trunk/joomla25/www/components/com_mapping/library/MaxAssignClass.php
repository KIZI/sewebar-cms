<?php
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractAssignClass.php');
/**
 *  Třída pro automatizované přiřazování nejlepší kombinace propojení klíčů
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
class MaxAssignClass extends AbstractAssignClass{ // BEGIN class Assign     
	
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
	  //if (!$this->finalArr){
      $this->execute();
    //}
    return $this->finalArr;    
  }
	
	/**
	 *   Funkce pro vytvoření pole s asociačním namapováním
	 */   	
	public function execute(){     
	  if (count($this->dataArr)>0){                   
      foreach ($this->dataArr as $key=>$arr) {
      	if (count($arr)>0){
          //subpole má nějaké přiřaditelné položky
          $maxValue=-1;
          $maxKey2='';
          foreach ($arr as $key2=>$value) {
            if ($value['ratio']>$maxValue){
              $maxValue=$value['ratio'];
              $maxKey2=$key2;
            }
          }
          if ($maxValue>0){
            $this->finalArr[$key]=array('name'=>$maxKey2,'value'=>$arr[$maxKey2]);
          }
        }
      }
    }
  } 
	       
}      
?>

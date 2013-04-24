<?php
/**
 *  Třída pro přiřazování propojení klíčů
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
abstract class AbstractAssignClass
{ 
	// variables
	var $dataArr,$finalArr;
	
	/**
	 *   Konstruktor
	 */   	
  function __construct($dataArr,$finalArr) {
    $this->dataArr=$dataArr;    
    $this->finalArr=$finalArr;
  }
	
	/**
	 *   Funkce pro přidání uživatelského namapování
	 */   	
	public function addUserMerge($key,$key2){
    $this->finalArr[$key]=array('name'=>$key2,'value'=>$this->dataArr[$key][$key2]);
    unset($this->dataArr[$key]);
    $this->dataArr=$this->removeKeys($this->dataArr,$key2);
  }
  
  /**
   *   Funkce pro zrušení uživatelského namapování 
   */     
  public function removeUserMerge($key,$key2,$defaultDataArr,$ignoreArr=array()){
    //zrušíme položku z $finalArr
    unset($this->finalArr[$key]);
    //obnovíme položku v $dataArr z $defaultDataArr
    $this->dataArr[$key]=$defaultDataArr[$key];
    //projdeme $defaultDataArr
    if (count($defaultDataArr)>0){
      foreach ($defaultDataArr as $keyX=>$arr) {                              
      	if (isset($arr[$key2]) && !isset($this->finalArr[$keyX]) && !in_array($keyX,$ignoreArr)){ 
          $this->dataArr[$keyX][$key2]=$arr[$key2];
        }
      }
    }
  }
  
  /**
   *   Funkce pro nastavení klíče do ignore
   */      
  public function addUserIgnore($key,$ignoreArr){
    $ignoreArr[]=$key;
    unset($this->dataArr[$key]);
    return $ignoreArr;
  } 
  
  /**
   *   Funkce pro zrušení klíče z ignore
   */      
  public function removeUserIgnore($key,$ignoreArr,$defaultDataArr){
    $this->dataArr[$key]=$defaultDataArr[$key];
    unset($ignoreArr[$key]);
    return $ignoreArr;
  } 
  
  /**
   *   Funkce pro odebrání klíčů ze subpolí / vrací pole bez příslušných klíčů
   */                 
  static function removeKeys($dataArr,$key2){
    $dataArr2=$dataArr;
    if (count($dataArr2)>0)
      foreach ($dataArr2 as $key=>$arr) {
     	  unset($dataArr2[$key][$key2]);
      }    
    return $dataArr2;  
  }
  
  /**
   *   Funkce pro odebrání klíčů, které nemají žádné přiřaditelné položky...
   *   @param $dataArr
   *   @return $dataArr bez prázdných klíčů      
   */               
  static function removeBlankKeys($dataArr){
    if (count($dataArr)>0){
      foreach ($dataArr as $key=>$arr) {
     	  if (count($arr)==0){
          unset($dataArr[$key]); 
        }
      }
    }
    return $dataArr;
  }
	
		
	/**
	 *   Funkce vracející asociační pole dle automatického napárování
	 *   @return $finalArr;	 
	 */   	
	abstract public function getAssignArr();
	
}       
?>

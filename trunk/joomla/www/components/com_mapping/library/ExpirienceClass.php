<?php
/**
 *  Třída pro zjišťování znalostí z předchozích úspěšných napárování
 */ 
class ExpirienceClass{
  
  var $model;
  var $userPlusRatio,$autoPlusRatio; //konstanty pro zvětšování podobnosti
  
  /**
   *  Výchozí inicializace - načtení potřebných tříd...
   */     
  function __construct($updateMode=true){                                          
    require_once(JPATH_COMPONENT.DS.'models'.DS.'expirience.php');
    $this->model=new ExpirienceModel();
    if ($updateMode){
      require_once(JPATH_COMPONENT.DS.'models'.DS.'config.php');
      $configModel=new ConfigModel();
      $this->autoPlusRatio=$configModel->loadConfigValue('constant','EXPIRIENCE_AUTO_RATIO');
      $this->userPlusRatio=$configModel->loadConfigValue('constant','EXPIRIENCE_USER_RATIO');
    }
  }
  
  /**
   *  Funkce vracející hodnotu učící podobnosti z DB
   */     
  public function getExpirience($name1,$name2){
    return $this->model->loadExpirience($name1,$name2);
  }
  
  /**
   *  Funkce pro aktualizaci záznamu uživatelských zkušeností
   */     
  public function updateExpirience($name1,$name2,$userMapped=false){
    if ($userMapped){
      $this->model->updateExpirience($name1,$name2,$this->userPlusRatio);
    }else{
      $this->model->updateExpirience($name1,$name2,$this->autoPlusRatio);
    }
  }
  
  /**
   *  Funkce pro uložení zkušeností ze všech mapování
   */     
  public function saveExpiriences($finalArr,$userDataArr,$legendArr){      
    if (count($finalArr)>0){
      foreach ($finalArr as $key=>$arr) {
        //zjistime, jestli jde o uzivatelsky namapovany klic
      	if (isset($userDataArr[$key])){$userMapped=true;}else{$userMapped=false;}
        $key1name=$legendArr[$key];
      	$key2name=$arr['name'];
        $key2name=$legendArr[$key2name];
        $this->updateExpirience($key1name,$key2name,$userMapped);
      }
    }                                                        
  } 
  
}
?>

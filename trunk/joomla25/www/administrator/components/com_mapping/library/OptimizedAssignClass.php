<?php
require_once(JPATH_COMPONENT.DS.'library'.DS.'AbstractAssignClass.php');
/**
 *  Třída pro automatizované přiřazování nejlepší kombinace propojení klíčů
 *  @author Stanislav Vojíř
 *  @version 1.0 
 */         
class OptimizedAssignClass extends AbstractAssignClass{ // BEGIN class Assign     

	// variables
	var $bestArr;
	
	/**
	 *   Konstruktor
	 */   	
  function __construct($dataArr,$finalArr) {
    parent::__construct($dataArr,$finalArr);
    require_once(JPATH_COMPONENT.DS.'models'.DS.'config.php');
    $configModel=new ConfigModel();
    define('IGNORE_MERGE',$configModel->loadConfigValue('constant','OPTIMIZED_IGNORE_MERGE'));
    define('BOTTOM_MERGE_STEP',$configModel->loadConfigValue('constant','OPTIMIZED_BOTTOM_MERGE_STEP'));
    define('MAX_ARR_VALUES',$configModel->loadConfigValue('constant','OPTIMIZED_MAX_ARR_VALUES')); 
  }
	
	/**
	 *   Funkce vracející asociační pole dle automatického napárování
	 *   @return $finalArr;	 
	 */   	
	public function getAssignArr(){                                  
	  return $this->execute($this->dataArr,$this->finalArr);    
  }
	
	/**
	 *   Funkce pro vytvoření pole s asociačním namapováním
	 *   	 
	 *   @param $dataArr - zdrojové pole
	 *   @param finalArr - pole s výsledky
	 *   @return doplněné $finalArr      	 
	 */   	
	public function execute($dataArr,$finalArr){                           
	  $preparedData=$this->prepareData($this->removeSmallValuesKeys($dataArr,IGNORE_MERGE));  
    $this->bestArr=$this->combineData($preparedData,$finalArr);                                       
    $this->bestArr=$this->combineData($dataArr,$this->bestArr);
    return $this->bestArr;
  } 
	 

  /**
   *   Funkce pro výpočet value - hodnoty shody dat v dané kombinaci
   */               
  private function combineData($dataArr,$finalArr,&$maxValue=0){ 
    $dataArr=$this->removeFinalKeys($dataArr,$finalArr); 
    $workArr=$this->execOneValueKeys($dataArr,$finalArr);
    $dataArr=$workArr['dataArr'];
    $finalArr=$workArr['finalArr'];      
    if (count($dataArr)>0){
      $bestArr=null;
      foreach ($dataArr as $key=>$arr) { 
      	if (count($arr)>0){
          //subpole má nějaké přiřaditelné položky
          foreach ($arr as $key2=>$value) {  
            $maxValueX=$maxValue; 
            $finalArrX=$finalArr;        
            $finalArrX[$key]=array('name'=>$key2,'value'=>$value);
         	  $dataArrX=$dataArr;              
         	  unset($dataArrX[$key]);
         	  $dataArrX=OptimizedAssignClass::removeKeys($dataArrX,$key2);             
            $returnArr=$this->combineData($dataArrX,$finalArrX,$maxValueX);
            if (($returnArr)&&($maxValueX>$maxValue)){  
              $bestArr=$returnArr;
              $maxValue=$maxValueX;
            }
          }
        }else{
          unset($dataArr[$key]);
          $maxValueX=$maxValue;
          $returnArr=$this->combineData($dataArr,$finalArr,$maxValueX);
          if (($returnArr)&&($maxValueX>$maxValue)){  
            $bestArr=$returnArr;
            $maxValue=$maxValueX;
          }
        } 
      }
      return $bestArr;
    }else{        
      $value=$this->countDataValue($finalArr);  
       
      if ($value>$maxValue){
        $maxValue=$value;                     
        return $finalArr;
      }else{
        return null;
      }              
    }
    
  }

  /**
   *   Funkce pro spočítání hodnoty z finalArr 
   */               
   private static function countDataValue($finalArr){
     $sum=0;
     if (count($finalArr)>0){
       foreach ($finalArr as $item) {
         $sum+=$item['value']['ratio'];
       }
     }
     return $sum;
   }  

	/**
   *   Funkce pro odebrání dat s malými hodnotami
   */               
  private static function removeSmallValuesKeys($dataArr,$minValue){ 
    if (count($dataArr)>0){
      foreach ($dataArr as $key=>$arr) {
     	  if (count($arr)>0)
     	    foreach ($arr as $key2=>$value) {
         	  if ($value<$minValue){
              unset($dataArr[$key][$key2]);
            }
          }
      }
    }
    return $dataArr;
  } 
 
 /**
  *   Funkce, která projde pole se zadáním a pokud najde klíč, který má jen jednu variantu a tato navíc nejde přiřadit k žádnému jinému klíči, tak ji uloží do finalArr a odebere z dataArr
  */   
  private static function execOneValueKeys($dataArr,$finalArr){
    $dataArrX=$dataArr;     
    $finalArrX=$finalArr;
    if (count($dataArrX)>0){
      foreach ($dataArrX as $key=>$arr) {
      	if (count($arr)==1){
      	  //dane pole moznych prirazeni ma jen jednu polozku...
      	  foreach ($arr as $keyX=>$valueX) {
         	  $keyName=$keyX;
         	  $keyValue=$valueX;
          }
      	  $nalezeno=false;
          foreach ($dataArrX as $key2=>$arr2) {
            //projdeme ostatni polozky a zjistime, jestli je nase polozka unikatni
          	if ($key2!=$key){
              if (isset($arr2[$keyName])){
              	$nalezeno=true;
              }
            }
          }
          if (!$nalezeno){
            //polozka je unikatni => pridame ji do finalArr a odebereme ji z dataArr
            $finalArrX[$key]=array('name'=>$keyName,'value'=>$keyValue);
            unset($dataArrX[$key]);
          }
        }
      }
    }          
    return array("dataArr"=>$dataArrX,'finalArr'=>$finalArrX);
  }
 

 
  /**
   *   Funkce, která vrací část $dataArr, která ještě nemá výsledky v $finalArr
   */               
  private static function removeFinalKeys($dataArr,$finalArr){
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
       	  $dataArr=OptimizedAssignClass::removeKeys($dataArr,$name);
        }
      }
    }
    return OptimizedAssignClass::removeBlankKeys($dataArr);    
  }
	
	/**
   *   Funkce pro předpřípravu dat pro zpracování - abychom snížili počet kombinací...
   *           
   */               
  private static function prepareData($dataArr){           
    $valuesArr=array();//$valuesArr - pole obsahující transponované pole hodnot dle přiřazení k jednotlivým klíčům
    //vybereme z pole $dataArr jen číselné hodnoty...
    if (count($dataArr)>0){
      foreach ($dataArr as $key=>$arr) { 
     	  if (count($arr)>0)               
     	    foreach ($arr as $key2=>$value) {
            $valuesArr[$key2][]=array('key'=>$key,'value'=>$value);
          }
      }
    }else{
      return $dataArr;
    }                 
    //seřadíme pole               
    foreach ($valuesArr as $key2=>$arr) {
   	  usort($valuesArr[$key2],array("OptimizedAssignClass","usort_subvalues"));
    }         
    //vybereme položky, které jsou významné a sestavíme z nich výstupní pole
    $outputArr=array();
    foreach ($valuesArr as $key2=>$arr) {                 
   	  $pocet=0;                                               
      if (count($arr)>0){              
        $bottomMerge=$arr[0]['value']['ratio']-BOTTOM_MERGE_STEP;       
        foreach ($arr as $subArr){
          $pocet++;                     
          if ((($pocet<MAX_ARR_VALUES)||($subArr['value']['ratio']==$currValue))&&($subArr['value']['ratio']>$bottomMerge)){
            $keyName=$subArr['key'];    
            $outputArr[$keyName][$key2]=$subArr['value'];
          }
        }
      }     
    }
    //jeste jednou projdeme jen vyznamne polozky - vynechame pripadne nepodstatne polozky... 
    foreach ($outputArr as $key=>$subArr) {
    	//zjistíme nejpodobnější položku
      $maxValue=0;
    	if (count($subArr)>0){
        foreach ($subArr as $key2=>$value) {
        	if ($maxValue<$value['ratio']){$maxValue=$value['ratio'];}
        }
      }       
      //odstranime nepodstatne položky    
      $bottomMergeValue=$maxValue-BOTTOM_MERGE_STEP;//TODO                    
      if (count($subArr)>0){
        foreach ($subArr as $key2=>$value) {
        	if ($value['ratio']<$bottomMergeValue){
            unset($outputArr[$key][$key2]);
          }
        }
      }
    }                          
    return $outputArr;
  }
  
  /**
   *  Vlastní řazení pro funkci usort
   */     
  private static function usort_subvalues($a,$b){
    if ($a['value']['ratio']>$b['value']['ratio']){
      return -1;
    }else{
      return 1;
    }
  }
	
} // END class Assign       
?>

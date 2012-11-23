<?php

  /**
   *  Třída pro zachycení struktury interval
   */     
  class Interval {
    var $leftBound,$rightBound,$leftBoundClosed,$rightBoundClosed;
    function __construct($leftBound,$rightBound,$leftBoundClosed,$rightBoundClosed,$minusInf=-999999999,$plusInf=999999999){
      $this->leftBound=$leftBound;
      $this->rightBound=$rightBound;
      $this->leftBoundClosed=$leftBoundClosed;
      $this->rightBoundClosed=$rightBoundClosed;
      /*vyřešení nekonečna*/
      if (strtoupper($this->leftBound)=='-INF'){
        $this->leftBound=$minusInf;
      }
      if ((strtoupper($this->rightBound)=='INF')||(strtoupper($this->rightBound)=='+INF')){
        $this->rightBound=$plusInf;
      }
    }
    /**
     *  Funkce pro ověření toho, zda interval zahrnuje dané číslo...
     *  $number - float
     *  return boolean          
     */         
    public function containts($number){
      if (($number>$this->leftBound)&&($number<$this->rightBound)){
        return true;
      }else{
        if ((($this->leftBoundClosed)&&($number==$this->leftBound))||(($this->rightBoundClosed)&&($number==$this->rightBound))){
          return true;
        }else{
          return false;
        }
      }
    }
    /**
     *  Funkce vracející hodnotu z <0;1> podle toho, jak je porovnávaný interval zahrnut v main intervalu
     *  $interval - instance of Interval
     *  return float [0;1]          
     */
    public function containtsInterval($interval){
      if ($interval->leftBound>$this->leftBound){
        $leftBound=$interval->leftBound;
      }else{
        $leftBound=$this->leftBound;
      }
      if ($interval->rightBound<$this->rightBound){
        $rightBound=$interval->rightBound;
      }else{
        $rightBound=$this->rightBound;
      }
      $originalSize=$interval->rightBound-$interval->leftBound;
      $coverageSize=$rightBound-$leftBound;
      
      return $coverageSize/$originalSize;
    }          
  } 
   
  class NumericClass {
    /**
     *  $int1Arr - array od Interval
     *  $int2Arr - array od Interval                          
     */         
    public static function intIntMatch($int1Arr,$int2Arr){
      $match=0;
      
      if ((count($int1Arr)>0)&&(count($int2Arr)>0)){
        foreach ($int2Arr as $int2) {
        	foreach ($int1Arr as $int1) {
         	  $match+=$int1->containtsInterval($int2);
          }
        }
        return $match/count($int2Arr);
      }else{
        return 0;
      }
    }
   
    /**
     *  $enumArr - array od real
     *  $intervalArr - array od Interval
     */         
    public static function intEnumCoverage($enumArr,$intervalArr){
      $match=0;
      if ((count($enumArr)>0)&&(count($intervalArr)>0)){
        //overeni normalnich hodnot
        foreach ($enumArr as $enumItem) {
          foreach ($intervalArr as $interval) {
         	  if ($interval->containts($enumItem)){
              //interval danou položku zahrnuje
              $match++;
              break;
            }
          }
        }
        
        return $match/count($enumArr);
      }else {
        return 0;
      }
    }
    
    /**
     *  Funkce pro porovnání 2 numerických veličin vyjádřených výčtem prvků
     */         
   	public static function numericEnumsCoverage($enumArr1,$enumArr2,$coverageMode,$minusInf=-999999999,$plusInf=999999999){
      if ((count($enumArr1)>0)&&(count($enumArr2)>0)){
        //shody hodnot
        $match1=0;
        foreach ($enumArr1 as $value1) {
        	if (!array_search($value1, $enumArr2)===false){
            $match1++;
          }
        }
        $match1=$match1/max(count($enumArr1),count($enumArr2));
        //velikost mnozin
          //zjistime min a max u jednotlivych mnozin
        $min1=999999999;
        $max1=-999999999;
        foreach ($enumArr1 as $value1) {
        	if ($value1<$min1){
            $min1=$value1;
          }
          if ($value1>$max1){
            $max1=$value1;
          }
        }
        $min2=999999999;
        $max2=-999999999;
        foreach ($enumArr2 as $value2) {
        	if ($value2<$min2){
            $min2=$value2;
          }
          if ($value2>$max2){
            $max2=$value2;
          }
        }
          //porovname je jako intervaly
        $interval1=new Interval($min1,$max1,true,true,$minusInf,$plusInf);
        $match2=$interval1->containtsInterval(new Interval($min2,$max2,true,true,$minusInf,$plusInf));
        return (2*$match2+$match1)/3;
      }else{
        return 0;
      }  
    }
   	
   	
  }
  

  
  
?>
<?php
/**
 *  Třída pro porovnávání textových řetězců
 */ 
class StringClass{
  
  /**
   *  Funkce vracející podobnost řetězců v intervalu [0;1]
   */     
  public static function similarity($str1,$str2){
    similar_text(strtolower($str1),strtolower($str2),$similarity);
    return $similarity/100;
  } 
  
  /**
   *  Funkce vracející pole (array) trigramů
   *  @param stringArr - pole se vstupními řetězci   
   *  @param nonTrigrams = true - zahrnout i řetězce, které nemají ani jeden trigram
   *  @return array - pole s trigramy (samotné trigramy jsou uvedené jako indexy pole, hodnoty jsou počty výskytů)      
   */     
  public static function getTrigramsArr($stringArr,$nonTrigrams=true,$trigramLength=3){
    $returnArr=array();
    if (count($stringArr)>0){
      foreach ($stringArr as $string) {
        $string=strtolower($string);
      	$start=strlen($string)-$trigramLength; //začátek posledního trigramu
      	while ($start>=0){        //projdeme vsechny trigramy a zapocteme je do pole
          $trigram=substr($string, $start, $trigramLength);
          @$returnArr[$trigram]+=1;
          $start--;
        }
      }
      if ($nonTrigrams){ //zahrneme i retezce kratsi nez jeden trigram
        foreach ($stringArr as $string) {
          $strLen=strlen($string);
          if (($strLen<$trigramLength)&&($strLen>0)) {
            $string=strtolower($string);
          	@$returnArr[$string]++;
          }
        }      
      }
    }
    return $returnArr;
  }
  
  /**
   *  Funkce vracející velikost vektoru
   */     
  public static function getVectorSize($arr){
    $sum=0;
    if (count($arr)>0){
      foreach ($arr as $value) {
      	$sum+=($value*$value);
      }
    }
    return sqrt($sum);
  }
  
  /**
   *  Funkce počítající skalární součin dvou vektorů
   */     
  private static function getScalarProduct($vector1,$vector2){
    $sum=0;
    if ((count($vector1)>0)&&(count($vector2)>0)){
      foreach ($vector1 as $key=>$value1) {
      	if (isset($vector2[$key])){
          $sum+=$value1*$vector2[$key];
        }
      }
    }
    return $sum;
  }
  
  /**
   *  Funkce vracející hodnotu kosínovy míry podobnosti vektorů
   */     
  public static function getTrigramsSimilarity($arr1,$size1,$arr2,$size2){  
    if (($size1==0)||($size2==0)){
      return 0;
    }
    return (StringClass::getScalarProduct($arr1,$arr2))/($size1*$size2);
  }
  
  /**
   *  Funkce vracející hodnotu kosínovy míry podobnosti vektorů - pro pokrytí vektoru z PMML vektorem v BKEF
   */     
  public static function getTrigramsCoverage($arr1,$size1,$arr2,$size2){
    if (($size1==0)||($size2==0)){
      return 0;
    }  
    return (StringClass::getScalarProduct($arr1,$arr2))/($size1*$size1);     //NERESIT!
  }
  
}
?>

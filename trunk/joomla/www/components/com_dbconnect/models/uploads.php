<?php
/**
* Model pro práci s tabulkami uloženými v databázi v podobě connection stringů
*/                            

jimport('joomla.application.component.model');
                   
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectModelUploads extends JModel
{

  const TMP_DIR='./components/com_dbconnect/tmp';
  const UNZIP_DIR='./components/com_dbconnect/tmp/unzip';
  const ROWS_COUNT=50;
  const MAX_COLUMNS_COUNT=300; //1000;

  public static function getFilePath($id){
    if (file_exists(self::TMP_DIR.'/'.$id.'.uploaded')){
      return self::TMP_DIR.'/'.$id.'.uploaded';
    }
    return null;
  }

	/**
	 *   Funkce pro uložení nového záznamu o připojitelné tabulce
	 */   	
  public function insertFile($filename,$tmpName){ 
    if (!file_exists($tmpName)){
      return false;
    }
    $db=$this->getDBO();
    $user =& JFactory::getUser();
    $db->setQuery('INSERT INTO #__dbconnect_uploads (`uid`,`filename`) VALUES ("'.$user->get('id').'","'.$db->getEscaped($filename).'");');
    if(!$db->query()){
      return false;
    }else{
      $insertId=$db->insertid();
      if (!move_uploaded_file($tmpName,self::TMP_DIR.'/'.$insertId.'.uploaded')){
        rename($tmpName,self::TMP_DIR.'/'.$insertId.'.uploaded');
      }      
      if ($this->needsUnzip($filename)){
        $this->unzipFile(self::TMP_DIR.'/'.$insertId.'.uploaded',$insertId);
      }
      
      return $insertId;
    }
  }

  /**
   *  Funkce, která se snaží podle přípony určit, zda daný soubor potřebuje rozbalit (zda jde o archív)
   */     
  private function needsUnzip($filename){
    $filenameParts=pathinfo($filename);
    $extension=strtolower($filenameParts['extension']);
    return ($extension=='zip');
  }
  
  /**
   *  Funkce pro rozbalení ZIP archívu s CSV souborem
   */      
  private function unzipFile($filename,$fileId){
    //rozbalení ZIPu do dočasné složky
    $zip=new ZipArchive();       
    $res=$zip->open($filename);   
    if ($res===true){
      if (!is_dir(self::UNZIP_DIR.'/'.$fileId)){
        mkdir(self::UNZIP_DIR.'/'.$fileId);
      }
      $zip->extractTo(self::UNZIP_DIR.'/'.$fileId);
    }
    $zip->close();
    //projití obsahu rozbalené složky - hledání souborů
    if ($dh = opendir(self::UNZIP_DIR.'/'.$fileId)) {
      $extractedFiles=array();
      while (($file = readdir($dh)) !== false) {
        if (($file!='.')&&($file!='..')){
          $extractedFiles[]=$file;
        }
      }
      closedir($dh);
    }
    //vybrani spravneho souboru
    $extractedFile='';
    if (count($extractedFiles)==1){
      $extractedFile=$extractedFiles[0];
    }elseif(count($extractedFiles)>1){
      foreach ($extractedFiles as $file){
      	$fileInfo=pathinfo($file);
        if (strtolower($fileInfo['extension'])=='csv'){
          $extractedFile=$file;
          break;
        }
      }
    }
    //pokud máme extrahovaný soubor, tak ho přesuneme místo originálu (tj. místo archívu)
    unlink($filename);   
    rename(self::UNZIP_DIR.'/'.$fileId.'/'.$extractedFile,$filename);
    //odstranění zbytků
    if (count($extractedFiles)>0){
      foreach ($extractedFiles as $file){
      	unlink(self::UNZIP_DIR.'/'.$fileId.'/'.$file);
      }
      rmdir(self::UNZIP_DIR.'/'.$fileId);
    }
  }
  
  /**
   *  Funkce vracející podrobnosti o jednom uloženém připojení
   */     
  public function getFile($id){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT * FROM #__dbconnect_uploads WHERE id='".$id."' AND uid='".$user->get('id')."' LIMIT 1;");
    return $db->loadObject();
  } 
  
  /**
   *  Funkce pro smazání připojení z DB
   */     
  public function deleteFile($id){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery('SELECT * FROM #__dbconnect_uploads WHERE id="'.$id.'" LIMIT 1;');
    $fileData=$db->loadObject();
    if ((!$fileData)||($fileData->uid==$user->get('id'))){
      unlink(self::TMP_DIR.'/'.$fileData->id.'.uploaded');
      unlink(self::TMP_DIR.'/'.$fileData->id.'.uploaded.original');
    }
  }
  
  /**
   *  Funkce pro změnu kódování souboru
   */
  public function iconvFile($id,$inCharset){
    $newFilePath=self::getFilePath($id);
    $originalFilePath=$newFilePath.'.original';
    if (!file_exists($originalFilePath)){
      rename($newFilePath,$originalFilePath);
    }
    
    if ($inCharset=='utf8'){
      copy($originalFilePath,$newFilePath);
      return;
    }
    
    $file=fopen($originalFilePath,'r');
    $file2=fopen($newFilePath,'w');
    while ($row=fgets($file)){
    	$rowNew=iconv($inCharset,'utf8',$row);
      fputs($file2,$rowNew);
    }
    fclose($file2);
    fclose($file);
  }      
  
  /**
   *  Funkce vracející počet řádků ze souboru
   */
  public function getRowsCount($id){
    $file=fopen(self::getFilePath($id),'r');
    $rowsCount=0;
    while (fgets($file)){
    	$rowsCount++;
    }
    fclose($file);
    return $rowsCount-1;
  }      
  
  /**
   *  Funkce vracející zvolený počet řádků z CSV souboru (ignoruje 1. řádek se záhlavím)
   */     
  public function getRowsFromCSV($id,$count=10000,$delimitier=',',$enclosure='"',$escapeCharacter='\\'){
    $file=fopen(self::getFilePath($id),'r');
    if ($file===false){return null;}
    $counter=$count;
    $outputArr=array();
    if ($delimitier=='\t'){
      $delimitier="\t";
    }
    ///while (($counter>0)&&($data=fgetcsv($file,0,$delimitier,$enclosure,$escapeCharacter))){//TODO povolit po updatu PHP
    while (($counter>0)&&($data=fgetcsv($file,0,$delimitier,$enclosure))){    
      if ($counter==$count){ 
        $counter--;                          
        continue;
      }                                               
      $outputArr[]=$data;
      $counter--;
    }  
    fclose($file);  
    return $outputArr;
  }
  
  /**
   *  Funkce vracející oddělovač, který je pravděpodobně použit v CSV souboru
   */     
  public function getCSVDelimitier($id){
    $file=fopen(self::getFilePath($id),'r');
    if ($file===false){return ',';}  
    if ($row=fgets($file)){      
      $stredniky=substr_count($row,';');
      $carky=substr_count($row,',');
      $svislitka=substr_count($row,'|');    
      $tabulatory=substr_count($row,"\t");  
      $max=max($stredniky,$carky,$svislitka,$tabulatory); 
      if ($max<2){return ',';}
      switch ($max) {
        case $stredniky:return ';';
        case $carky:return ',';
        case $svislitka:return '|';
        case $tabulatory:return "\t";
      }
    }
    fclose($file);
    return ',';
  }
  
  /**
   *  Funkce pro analýzu dat uložených v CSV, vrací pole s upravenými názvy sloupců a jejich hodnotami
   */     
  public function analyzeCSV($id,$delimitier=',',$enclosure='"',$escapeCharacter='\\'){
    $file=fopen(self::getFilePath($id),'r');
    if ($file===false){return null;} 
    ///$namesRow=fgetcsv($file,0,$delimitier, $enclosure,$escapeCharacter);//TODO povolit po updatu PHP
    if ($delimitier=='\t'){
      $delimitier="\t";
    }
    $namesRow=fgetcsv($file,0,$delimitier, $enclosure);
    $columnNamesArr=$this->prepareColumnNamesArr($namesRow);
    $numericalArr=array();
    $strlenArr=array();
    //výchozí inicializace počítacích polí
    $columnsCount=count($columnNamesArr);
    for($i=0;$i<$columnsCount;$i++){
      $numericalArr[$i]=true;
      $strlenArr[$i]=0;
    }                           
    //kontrola všech řádků v souboru
    ///while ($data=fgetcsv($file,0,$delimier,$enclosure,$escapeCharacter)){//TODO povolit po updatu PHP
    $rowsCount=0;
    while (($data=fgetcsv($file,0,$delimitier,$enclosure))&&($rowsCount<1000)){ 
      //načten další řádek
      for ($i=0;$i<$columnsCount;$i++){
        $value=@$data[$i];
      	if ($numericalArr[$i]){
          $numericalArr[$i]=$this->isNumeric($value);
        }
        $strlen=strlen($value);
        if ($strlen>$strlenArr[$i]){
          $strlenArr[$i]=$strlen;
        }
      }
      $rowsCount++;
    }
    //shromáždíme informace
    $outputArr=array();      
    for ($i=0;$i<$columnsCount;$i++){
      if ($numericalArr[$i]==2){
        $datatype='float';
      }elseif ($numericalArr[$i]==1){
        $datatype='int';
      }else{
        $datatype='string';
      }
      $outputArr[$i]=array(
                       'name'=>$columnNamesArr[$i],
                       'datatype'=>$datatype,
                       'length'=>$strlenArr[$i]
                     );
    }
    fclose($file);
    return $outputArr;
  }
  
  /**
   *  Funkce kontrolující, jestli  je zadaná hodnota číslem
   */     
  private function isNumeric($value){
    if (is_numeric($value)||(is_numeric(str_replace(',','.',$value)))){
      if (intval($value)==$value){
        return 1;
      }else{  
        return 2;
      }
    }else{
      return 0;
    }
  }
  
  /**
   *  Funkce pro úpravu jsme sloupců
   */     
  private function prepareColumnNamesArr($namesArr){   
    $outputArr=array();
    $i=0;
    foreach ($namesArr as $name){ 
    	//úprava jednotlivých názvů
      $outName=$this->cleanName($name);
      $outName=substr($outName,0,20);
      if ($outName==''){
        $outName='col'.$i;
      }
      $attach=0;
      while (in_array($outName.(($attach>0)?$attach:''),$outputArr)) {
      	$attach++;
      }
      $outputArr[]=$outName.(($attach>0)?$attach:'');
      $i++;
      if ($i>self::MAX_COLUMNS_COUNT){
        break;
      }
    }
    return $outputArr;    
  }
  
  /**
   *  Funkce pro vyčištění jména tak, aby bylo použitelné jako název sloupce či tabulky v DB
   */     
  public function cleanName($name){
    $prevodniTabulka = Array('-'=>'_','.'=>'_',','=>'_',';'=>'_','|'=>'_',' '=>'_','\''=>'','"'=>'','/'=>'_','\\'=>'_',':'=>'_','ä'=>'a','Ä'=>'A','á'=>'a','Á'=>'A','à'=>'a','À'=>'A','ã'=>'a','Ã'=>'A','â'=>'a','Â'=>'A','č'=>'c','Č'=>'C','ć'=>'c','Ć'=>'C','ď'=>'d','Ď'=>'D','ě'=>'e','Ě'=>'E','é'=>'e','É'=>'E','ë'=>'e','Ë'=>'E','è'=>'e','È'=>'E','ê'=>'e','Ê'=>'E','í'=>'i','Í'=>'I','ï'=>'i','Ï'=>'I','ì'=>'i','Ì'=>'I','î'=>'i','Î'=>'I','ľ'=>'l','Ľ'=>'L','ĺ'=>'l','Ĺ'=>'L','ń'=>'n','Ń'=>'N','ň'=>'n','Ň'=>'N','ñ'=>'n','Ñ'=>'N','ó'=>'o','Ó'=>'O','ö'=>'o','Ö'=>'O','ô'=>'o','Ô'=>'O','ò'=>'o','Ò'=>'O','õ'=>'o','Õ'=>'O','ő'=>'o','Ő'=>'O','ř'=>'r','Ř'=>'R','ŕ'=>'r','Ŕ'=>'R','š'=>'s','Š'=>'S','ś'=>'s','Ś'=>'S','ť'=>'t','Ť'=>'T','ú'=>'u','Ú'=>'U','ů'=>'u','Ů'=>'U','ü'=>'u','Ü'=>'U','ù'=>'u','Ù'=>'U','ũ'=>'u','Ũ'=>'U','û'=>'u','Û'=>'U','ý'=>'y','Ý'=>'Y','ž'=>'z','Ž'=>'Z','ź'=>'z','Ź'=>'Z');
    $outName=strtolower($name);
    $outName=strtr($outName,$prevodniTabulka);
    $outName=iconv('utf8','ascii//IGNORE',$outName);
    
    if ($outName=='id'){
      $outName.='X';
    }
    $outName2='';
    for($i=0;$i<strlen($outName);$i++){
      if (strpos(' abcdefghijklmnopqrstuvwxyz0123456789_',$outName[$i])){
        $outName2.=$outName[$i];
      }
    }           
    if (strpos(' 0123456789',$outName2[0])){
      $outName2='x_'.$outName2;
    }
    return $outName2;
  }
    
    
  /**
   *  Funkce pro analýzu dat uložených v CSV, vrací pole s upravenými názvy sloupců a jejich hodnotami
   */     
  public function importToDb($unidbModel,$tableName,$id,$delimitier=',',$enclosure='"',$escapeCharacter='\\'){
    $file=fopen(self::getFilePath($id),'r');        
    if ($file===false){return null;} 
    if ($delimitier=='\t'){
      $delimitier="\t";
    }
    $namesRow=fgetcsv($file,0,$delimitier, $enclosure,$escapeCharacter);
    $columnNamesArr=$this->analyzeCSV($id,$delimitier,$enclosure,$escapeCharacter);
                          
    //vytvoření tabulky v DB 
    $tableName=$this->cleanName($tableName);    
    $existingTables=$unidbModel->getTables();   
    $existingTablesNames=array();    
    foreach ($existingTables as $table) {
    	$existingTablesNames[]=$table['Name'];
    }                               
    $attach=0;
    while (in_array($tableName.(($attach>0)?$attach:''),$existingTablesNames)){
    	$attach++;
    }
    $tableName=$tableName.(($attach>0)?$attach:'');                                  
    $unidbModel->createTable($tableName,$columnNamesArr);
    //--vytvoření tabulky v DB
                           
    //import všech řádků ze souboru
    $importData=array();
    ///while ($data=fgetcsv($file,0,$delimier,$enclosure,$escapeCharacter)){
    while ($data=fgetcsv($file,0,$delimitier,$enclosure)){
      $importData[]=$data;
      if (count($importData)>self::ROWS_COUNT){
        $unidbModel->importData($tableName,$columnNamesArr,$importData);
        $importData=array();
      }
    }
    if (count($importData)>0){
      $unidbModel->importData($tableName,$columnNamesArr,$importData);
    }
    unset($importData);
     
    return $tableName;
  } 

}
?>

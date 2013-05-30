<?php
  jimport( 'joomla.application.component.model' );             
                     
  class dbconnectModelUnidb extends JModel{
    const MAX_LIST_ITEMS=50;
  	var $db;
  	
  	/**
  	 *   Funkce pro nastavení připojení k DB
  	 */   	
  	public function setDB($dbtype,$server,$username,$password,$dbname){ 
      $dsn=$dbtype.':dbname='.$dbname.';host='.$server;           
      try{    
        $this->db=new PDO($dsn,$username,$password);   
      }catch (PDOException $e) {
        return 'DB connection failed: ' . $e->getMessage();
      }
      if (!is_object($this->db)){
        return 'DB connection failed';  
      }
    }
    
    /**
     *  Funkce vracející přehled hodnot z konkrétního databázového sloupce
     */
    public function getColumnValuesPreview($tableName,$columnName,$order='hodnota'){
      try{
        $orderArr=explode('/',$order);
        if ($orderArr[0]=='pocet'){
          $orderStr='pocet';
          if (@$orderArr[1]=='desc'){
            $orderStr.=' DESC, hodnota DESC';
          }else{
            $orderStr.=', hodnota';
          }
        }else{
          $orderStr='hodnota';
          if (@$orderArr[1]=='desc'){
            $orderStr.=' DESC';
          }
        }
        
        $result=$this->db->prepare('SELECT '.$columnName.' AS hodnota,count('.$columnName.') AS pocet FROM '.$tableName.' GROUP BY '.$columnName.' ORDER BY '.$orderStr.';');
        $result->execute();
      }catch (PDOException $e){
        return null;
      }
      return $result->fetchAll();
    }          
    
    /**
     *  Funkce pro načtení seznamu dostupných tabulek
     */     
    public function getTables(){    
      try{
        $result =$this->db->prepare("SHOW TABLE STATUS;");
        $result->execute();           
      }catch (PDOException $e) {
        exit('DB connection failed: ' . $e->getMessage());
      }
      return $result->fetchAll();
    }
  	
  	/**
  	 *   Funkce pro načtení sloupců z tabulky
  	 */   	
  	public function getColumns($tableName){                         
      $result=$this->db->prepare("SHOW COLUMNS FROM `".$tableName."`");
      $result->execute();
      return $result->fetchAll();
    }
    
    /**
     *  Funkce pro načtení počtu řádků z tabulky
     */     
    public function getTableRowsCount($tableName){                                       
      $result=$this->db->prepare("SELECT count(*) AS pocet FROM `".$tableName."`;");
      $result->execute();
      $result=$result->fetchAll();       
      if (count($result)==1){            
        return $result[0]['pocet'];
      }else{
        return 0;
      }
      
    }
    
    /**
     *  Funkce pro načtení řádek z tabulky
     */     
    public function getRows($tableName,$skipCount,$rowsCount){
      $result =$this->db->prepare("SHOW TABLE STATUS;");
      $result->execute();
    
      $result=$this->db->prepare("SELECT * FROM `".$tableName."` LIMIT $skipCount,$rowsCount;");
      $result->execute();
      return $result->fetchAll();
    }
  	
  	/**
     *  Funkce pro vygenerování XML dat z tabulky
     */     
    public function getContentXML($task,$forceReload){
      $db=$this->getDBO();
      $user=&JFactory::getUser();
      $db->setQuery("SELECT content FROM #__dbconnect_task_table_content WHERE id='".((string)$task->id)."' LIMIT 1;");
      $cacheresult= $db->loadObjectList();
      
      //zjistime nazev tabulky
      $tableId=$task->db_table;
      $db->setQuery("SELECT `table` FROM #__dbconnect_tables WHERE id='".$tableId."' LIMIT 1;");
      $tableResult=$db->loadObject();
      $tableName=$db->loadObject()->table;
      //--
      
                 
      if (@$cacheresult->content!=''&&(!$forceReload)){
        //pokud mame vygenerovane XML s obsahem uz ulozene v DB, pouzijeme ho - tj. nebudeme ho generovat znovu...
        return $cacheresult->content;
      }          
    
      //pripravime XML pro vystupni data
      $xml=simplexml_load_string('<dbtable></dbtable>');
      //budeme zpracovavat jen platne sloupce z ulohy 
      $columns=simplexml_load_string($task->columns);
      if (!$columns){return $xml->asXML();}          
                                        
      foreach ($columns->column as $column){
        //zpracovavame jen sloupce, ktere se maji pouzit
        if ((string)$column['use']!='1'){continue;}
        //pripravime vystupni XML pro dany sloupec
        $columnXML=$xml->addChild('col'); 
        $columnXML->addChild('name',(string)$column['name']);
//        $columnXML->addChild('type',$this->getGenericDataType($column['Type']));
        $columnXML->addChild('type',(string)$column['type']);
        $this->prepareColItemsXML($tableName,$column['name'],$columnXML);
      }
                     
      $xmlResult=$xml->asXML();       
      if (!$cacheresult){
        $db->setQuery("INSERT INTO #__dbconnect_task_table_content (id,content) VALUES (".$db->quote((string)$task->id).",".$db->quote($xmlResult).")");
      }else{
        $db->setQuery("UPDATE #__dbconnect_task_table_content SET content=".$db->quote($xmlResult)." WHERE id='".((string)$task->id)."' LIMIT 1;");
      }
      $db->query();
                     
      return $xmlResult;
    } 
    
    public static function getGenericDataType($datatype){   //TODO kontrola datových typů
      $datatypeX=strtoupper(' '.$datatype);  
      if ((strpos($datatypeX,'INT')>0)||(strpos($datatypeX,'DOUBLE')>0)||(strpos($datatypeX,'DECIMAL')>0)){     
        return 'integer';
      }elseif((strpos($datatypeX,'FLOAT')>0)){
        return 'float';
      }else{
        return 'string';
      }
    }
    
    /**
     *  Funkce vracející všechny hodnoty z nějakého sloupce (max. 400)
     */         
    public function getColumnValues($tableName,$columnName,$unique=true){                                                        
      $items=$this->db->prepare("SELECT `$columnName` FROM `$tableName` ".($unique?'GROUP BY `'.$columnName.'`':'')." LIMIT 400; ");
      $items->execute();
      return $items->fetchAll();
    } 
     
    
    private function prepareColItemsXML($tableName,$columnName,&$columnXML){  
    
      $items=$columnXML->addChild('items');

      if ((@$columnXML->type=='integer')||(@$columnXML->type=='float')){      
        $resultPocet=$this->db->prepare("SELECT count($columnName) AS pocet FROM $tableName;");
        $resultPocet->execute();
        $result=$resultPocet->fetchAll(PDO::FETCH_ASSOC);
        if (count($result)==1){            
          $pocet=$result[0]['pocet'];//TODO - co se s tím má dělat dál?
        }
        $resultNum=$this->db->prepare("SELECT min($columnName) AS minimum,max($columnName) AS maximum,avg($columnName) AS prumer FROM $tableName;");
        $resultNum->execute();
        $result=$resultNum->fetchObject();
        if ($result){               
          $statistics=$columnXML->addChild('statistics');
          $statistics->addChild('min',$result->minimum);
          $statistics->addChild('max',$result->maximum);
          $statistics->addChild('avg',$result->prumer);
          $statistics->addChild('count',$pocet);
        }
      }
      $resultPocet=$this->db->prepare("SELECT ".$columnName.",count(".$columnName.") AS pocet FROM ".$tableName." GROUP BY ".$columnName." ORDER BY pocet DESC LIMIT ".self::MAX_LIST_ITEMS.";");
      
      $resultPocet->execute();
      $result=$resultPocet->fetchAll();
      foreach ($result as $item) {
      	$itemXML=$items->addChild('item',$item[(string)$columnName]);
      	$itemXML->addAttribute('count',$item['pocet']);
      }       
      
    }
    
    /**
     *  Funkce pro vytvoření tabulky v DB
     */
    public function createTable($tableName,$columnsData){
      $sql2='';       
      foreach ($columnsData as $columnData) { 
        if ($columnData['datatype']=='int'){
          $dataType='int('.$columnData['length'].')';
        }elseif($columnData['datatype']=='float'){
          $dataType='float';
        }else{
          $dataType='varchar('.$columnData['length'].')';
        }           
      	$sql2.='`'.$columnData['name'].'` '.$dataType.' DEFAULT NULL,';
      }
      $sql='CREATE TABLE `'.$tableName.'` (
              `id` int(11) NOT NULL auto_increment,
              '.$sql2.'
              PRIMARY KEY  (`id`)
            ) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';
                               
      $query=$this->db->prepare($sql);
      $query->execute();   
    }
    
    /**
     *  Funkce pro import dat
     */
    public function importData($tableNameX,$columnsData,$rowsData){ 
      $columnNamesArr=array();
      foreach ($columnsData as $column){
      	$columnNamesArr[]='`'.$column['name'].'`';
        $columnTypesArr[]=$column['datatype'];
      }
      
      $columnsCount=count($columnNamesArr);
      $sql2='';
      foreach ($rowsData as $rowData){
        $sql2.=',(';
        $sqlX='';
        for($i=0;$i<$columnsCount;$i++){
          if ($columnTypesArr[$i]=='float'){
            $rowData[$i]=str_replace(',','.',$rowData[$i]);
          }
          $sqlX.=','.$this->db->quote(@$rowData[$i]);
        }
        $sql2.=substr($sqlX,1).')';	
      }
      
      $sql='INSERT INTO '.$tableNameX.' ('.implode(',',$columnNamesArr).') VALUES '.substr($sql2,1).';';
      $query=$this->db->prepare($sql);
      $query->execute();
    }                    
  	
  }
?>

<?php
  jimport( 'joomla.application.component.model' );
             
  define("MAX_LIST_ITEMS",30);             
                     
  class dbconnectModelUnidb extends JModel{
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
     *  Funkce pro načtení seznamu dostupných tabulek
     */     
    public function getTables(){
      $result =$this->db->prepare("SHOW TABLE STATUS;");
      $result->execute();
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
    
    private function prepareColItemsXML($tableName,$columnName,&$columnXML){  //TODO - možná používat podle mapType
      $items=$columnXML->addChild('items');

      if ((@$columnXML->type=='integer')||(@$columnXML->type=='float')){
        $resultPocet=$this->db->prepare("SELECT count($columnName) AS pocet FROM $tableName;");
        $resultPocet->execute();
        if (count($result)==1){            
          $pocet=$result['pocet'];
        }
        $resultNum=$this->db->prepare("SELECT min($columnName) AS minimum,max($columnName) AS maximum,avg($columnName) AS prumer FROM $tableName;");
        $resultNum->execute();
        if (count($result)==1){
          $statistics=$columnXML->addChild('statistics');
          $statistics->addChild('min',$resultNum['min']);
          $statistics->addChild('max',$resultNum['max']);
          $statistics->addChild('avg',($resultNum['prumer']));
        }
        if ($pocet>MAX_LIST_ITEMS){
          //pokud máme víc položek, než požadované maximum, tak je vůbec nebudeme vypisovat (v případě numerického sloupce)
          return;
        }
      }
            
      $resultPocet=$this->db->prepare("SELECT ".$columnName.",count(".$columnName.") AS pocet FROM ".$tableName." GROUP BY ".$columnName." ORDER BY pocet DESC LIMIT ".MAX_LIST_ITEMS.";");
      
      $resultPocet->execute();
      $result=$resultPocet->fetchAll();
      foreach ($result as $item) {
      	$itemXML=$items->addChild('item',$item[(string)$columnName]);
      	$itemXML->addAttribute('count',$item['pocet']);
      }       
      
    }
  	
  }
?>

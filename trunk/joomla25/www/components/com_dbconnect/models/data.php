<?php
  //model načítáme z komponenty com_dbconnect
  require_once (JPATH_COMPONENT.DS.'../com_mapping/models'.DS.'data.php');
  //require_once './com_dbconnect/models/tasks.php';
  class dbconnectModelData extends DataModel{
    
    /**
     *  Funkce vracející pole s podporovanými typy dat - musí být nadefinovány v DB (sloupec enum)
     */         
    public static function getMinerDataTypes(){
      return array('','clipboard','hiddenAttr');
    }
    
    /**
     *  Funkce pro uložení dat mineru
     */
    public function saveMinerData($kbiId,$userId,$type,$data){
      $db=$this->getDBO();
      if (!is_string($data)){
        $data=json_encode($data);
      }
      
      if ($this->loadMinerData($kbiId,$userId,$type)){
        //update
        $db->setQuery('UPDATE #__dbconnect_miner_data SET data='.$db->quote($data).' WHERE kbi_source='.$db->quote($kbiId).' AND uid='.$db->quote($userId).' AND  `type`='.$db->quote($type).' LIMIT 1;');
        return $db->query();
      }else{
        //insert
        $db->setQuery('INSERT INTO #__dbconnect_miner_data (`kbi_source`,`uid`,`type`,`data`)VALUES('.$db->quote($kbiId).','.$db->quote($userId).','.$db->quote($type).','.$db->quote($data).')');
        return $db->query();
      }
    }          
    
    /**
     *  Funkce pro načtení dat mineru
     */         
    public function loadMinerData($kbiId,$userId,$type){
      $db=$this->getDBO();
      $db->setQuery('SELECT * FROM #__dbconnect_miner_data WHERE kbi_source='.$db->quote($kbiId).' AND uid='.$db->quote($userId).' AND `type`='.$db->quote($type).' LIMIT 1;');
      return $db->loadObject();
    }
    
    /**
     *  Funkce vracející seznam článků připojených k úloze (na základě taskId a typu článku)
     */
    public function getArticlesList($taskId,$type){
      $db=$this->getDBO();
      $db->setQuery('SELECT * FROM #__dbconnect_tasks_articles JOIN #__content ON #__dbconnect_tasks_articles.article=#__content.id WHERE #__dbconnect_tasks_articles.task='.$db->quote($taskId).' AND #__dbconnect_tasks_articles.`type`='.$db->quote($type).' ORDER BY #__content.title;');
      return $db->loadObjectList();
    }      
    
    /**
     *  Funkce pro uložení vazby mezi článkem a DM úlohou...
     */         
    public function saveTaskArticle($taskId,$articleId,$type){
      $db=$this->getDBO();
      $db->setQuery('INSERT INTO #__dbconnect_tasks_articles (article,task,`type`)VALUES('.$db->quote($articleId).','.$db->quote($taskId).','.$db->quote($type).');');
      exit( 'INSERT INTO #__dbconnect_tasks_articles (article,task,`type`)VALUES('.$db->quote($articleId).','.$db->quote($taskId).','.$db->quote($type).');');
      if (!$db->query()){
        $db->setQuery('UPDATE #__dbconnect_tasks_articles SET `type`='.$db->quote($type).' WHERE task='.$db->quote($taskId).' AND article='.$db->quote($articleId).' LIMIT 1;');
        $db->query();
      }
    }    
    
    
    
    /**
     *  Funkce pro vytvoření/uložení článku
     */         
    public function saveArticle($articleId,$title,$data,$sectionId=0,$userId=0){    
      $db=$this->getDBO();
      $db->setQuery('SELECT id FROM #__content WHERE id='.$db->quote($articleId).' LIMIT 1;');
      if (($articleId)&&$db->loadObject()){ 
        //budeme updatovat
        $db->setQuery('UPDATE #__content SET title='.$db->quote($title).',alias='.$db->quote($this->seoUrl($title)).', introtext='.$db->quote($data).', `fulltext`="", sectionId='.$db->quote($sectionId).', modified=NOW(), modified_by='.$userId.' WHERE id='.$db->quote($articleId).' LIMIT 1;');
        if ($db->query()){     
          return $articleId;
        }
      }else{ 
        //budeme ukládat
        $db->setQuery('INSERT INTO #__content (`title`,`alias`,`introtext`,`state`,`sectionid`,`created`,`created_by`,`modified`,`modified_by`) VALUES('.$db->quote($title).','.$db->quote($this->seoUrl($title)).','.$db->quote($data).',1,'.$db->quote($sectionId).',NOW(),'.$db->quote($userId).',NOW(),'.$db->quote($userId).');');
        //TODO asset!!!
        if ($db->query()){
          return $db->insertid();
        }else{
          return false;
        }
      }
    }
    
    /**
     *  Funkce pro vytvoření/uložení článku
     */         
    public function newArticle($title,$data,$sectionId=0,$userId=0){  
      return $this->saveArticle(0,$title,$data,$sectionId,$userId);
    }
    
    /**
     *  Funkce pro uložení obsahu článku    
     */    
    public function saveArticleData($articleId,$data){
      parent::saveArticle($articleId,$data);
    }
    
  /**
   *  Funkce pro uložení simpleXml do jednoho článku v DB
   */     
  function saveArticleXML($id,$xml){
    $this->saveArticleData($id,$xml->asXML());
  } 
    
    
    /**
     *  Funkce pro úpravu textu tak, aby byl použitelný jako alias
     */         
    private function seoUrl($url){
      $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
      $url = trim($url, "-");
      $url = strtr($url, array('á'=>'a','ä'=>'a','č'=>'c','ç'=>'c','ď'=>'d','é'=>'e','ě'=>'e','ë'=>'e','í'=>'i','ň'=>'n','ó'=>'o','ö'=>'o','ř'=>'r','š'=>'s','ť'=>'t','ú'=>'u','ů'=>'u','ü'=>'u','ý'=>'y','ž'=>'z'));
      $url = strtolower($url);
      $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
      return $url;
    }
    
    
  }
  
?>
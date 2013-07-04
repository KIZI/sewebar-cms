<?php
/**
* Model pro práci s tabulkami uloženými v databázi v podobě connection stringů
*/                            

jimport('joomla.application.component.model');
                                 
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectModelTasks extends JModel
{
	
	/**
	 *   Funkce pro uložení nového záznamu o úloze
	 */   	
  public function insertBasicTask($name,$db_table,$columns,$iziTask=0){
    $db=$this->getDBO();
    $user =& JFactory::getUser();    
    $db->setQuery('INSERT INTO #__dbconnect_tasks (`uid`,`name`,`db_table`,`columns`,`izi_task`)VALUES("'.$user->get('id').'","'.$db->getEscaped($name).'","'.$db->getEscaped($db_table).'","'.$db->getEscaped($columns).'","'.(($iziTask)?'1':'0').'")');
    if(!$db->query()){
      return false;
    }else{
      return $db->insertid();
    }
  }
  
  /**
   *   Funkce pro uložení upraveného záznamu o úloze
   */     
  public function updateBasicTask($taskId,$name,$db_table,$columns,$iziTask=0){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery('UPDATE #__dbconnect_tasks SET name="'.$db->getEscaped($name).'",db_table="'.$db->getEscaped($db_table).'",columns="'.$db->getEscaped($columns).'",izi_task="'.(($iziTask)?'1':'0').'" WHERE id="'.$taskId.'" AND uid="'.$user->get('id').'" LIMIT 1;');
    return $db->query();
  }
  
  /**
   *   Funkce pro smazání záznamu o úloze
   */     
  public function deleteTask($taskId){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery('DELETE FROM #__dbconnect_tasks WHERE id="'.$taskId.'" AND uid="'.$user->get('id').'" LIMIT 1;');
    return $db->query();
  }
  
  /**
   *  Funkce vracející podrobnosti o jedné uložené úloze
   */     
  public function getTask($taskId){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT * FROM #__dbconnect_tasks WHERE id='".$taskId."' AND uid='".$user->get('id')."' LIMIT 1;");
    return $db->loadObject();
  } 
  
  
  /**
   *  Funkce vracející jednu uloženou úlohu na základě ID KBI zdroje
   */
  public function getTaskByKbi($kbiId,$ignoreUserID=false){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT * FROM #__dbconnect_tasks WHERE kbi_source='".$kbiId."' ".($ignoreUserID?'':'AND uid="'.$user->get('id').'"')." LIMIT 1;");
    return $db->loadObject();
  }      

  /**
   *  Funkce pro vypsání všech záznamů pro aktuálního/všechny uživatele
   */     
  public function getTasks($orderBy='id'){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT * FROM #__dbconnect_tasks WHERE uid='".$user->get('id')."' ORDER BY $orderBy;");
    return $db->loadObjectList();
  }
 
  /**
   *   Akce vracející obsah tabulky pripojene k uloze
   */
  public function getTableContent($taskId){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT content FROM #__dbconnect_task_table_content WHERE id='".$taskId."' LIMIT 1;");
    $obj=$db->loadObject();
    return @$obj->content;
  }    
  
  /**
   *  Akce pro úpravu propojení s FML
   *  @param int $taskId   
   *  @param array $params - pole s indexy článků (fml,bkef)   
   */      
  public function updateTaskArticles($taskId,$params){   
    $sql='';
    if (isset($params['fml'])){
      if (intval($params['fml'])>0){
        $sql=' fml_article="'.intval($params['fml']).'"';
      }
    }
    if (isset($params['bkef'])){
      if (intval($params['bkef'])>0){ 
        if ($sql!=''){$sql.=',';}
        $sql.=' bkef_article="'.intval($params['bkef']).'"';
      }
    }
    if ($sql!=''){
      $db=$this->getDBO();
      $user=&JFactory::getUser();
      $db->setQuery('UPDATE #__dbconnect_tasks SET '.$sql.' WHERE id="'.$taskId.'" AND uid="'.$user->get('id').'" LIMIT 1;');
      return $db->query();
    }
    return false;
  } 
  
  /**
   *  Akce pro přiřazení identifikace KBI zdroje k úloze
   */     
  public function updateTaskKbiSource($taskId,$kbiSource){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery('UPDATE #__dbconnect_tasks SET kbi_source='.$db->quote($kbiSource).' WHERE id="'.$taskId.'" AND uid="'.$user->get('id').'" LIMIT 1;');
    return $db->query();
  } 
  
  /**
   *  Funkce pro naklonování DM úlohy
   */
  public function cloneTask($taskId,$newName){        
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery('SELECT * FROM #__dbconnect_tasks WHERE id='.$db->quote($taskId).' AND uid="'.$user->get('id').'" LIMIT 1;');
    $obj=$db->loadObject();                  
    if (!$obj){
      return false;
    }

    $db->setQuery('INSERT INTO #__dbconnect_tasks (`uid`,`name`,`db_table`,`columns`,`bkef_article`,`fml_article`)VALUES('.$db->quote($obj->uid).','.$db->quote($newName).','.$db->quote($obj->db_table).','.$db->quote($obj->columns).','.$db->quote($obj->bkef_article).','.$db->quote($obj->fml_article).')');
    return $db->query();    
  }      
  
}
?>

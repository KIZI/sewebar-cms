<?php
/**
* Model pro práci s tabulkami uloženými v databázi v podobě connection stringů
*/                            

jimport('joomla.application.component.model');
                   
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectModelConnections extends JModel
{
	public function getDBTypes(){
    return array('mysql'=>"MySQL",'mysqli'=>"MySQLi");
  }
	
	/**
	 *   Funkce pro uložení nového záznamu o připojitelné tabulce
	 */   	
  public function insertConnection($dbtype,$server,$username,$password,$dbname,$table,$primaryKey,$shared=false){
    $db=$this->getDBO();
    $user =& JFactory::getUser();  
    $db->setQuery('INSERT INTO #__dbconnect_tables (`uid`,`db_type`,`server`,`username`,`db_name`,`table`,`primary_key`,`shared`)VALUES("'.$user->get('id').'","'.$db->getEscaped($dbtype).'","'.$db->getEscaped($server).'","'.$db->getEscaped($username).'","'.$db->getEscaped($dbname).'","'.$db->getEscaped($table).'","'.$db->getEscaped($primaryKey).'","'.($shared?'1':'0').'");');
    if(!$db->query()){
      return false;
    }else{                  
      $returnId= $db->insertid();
      //TODO uložení hesla
      ///,"'.$db->getEscaped($password)
      return $returnId;
    }
  }
  
  /**
   *  Funkce vracející podrobnosti o jednom uloženém připojení
   */     
  public function getConnection($connectionId,$userSafe=true){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT * FROM #__dbconnect_tables WHERE id='".$connectionId."'".($userSafe?' AND (uid=\''.$user->get('id').'\' OR shared=1)':'')." LIMIT 1;");
    return $db->loadObject('DbConnection');
  } 
  
  /**
   *  Funkce pro smazání připojení z DB
   */     
  public function deleteConnection($connectionId,$adminMode){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    if ($adminMode){
      $db->setQuery("delete from #__dbconnect_tables WHERE uid='".$user->get('id')."' AND id='".($connectionId+0)."';");
    }else{
      $db->setQuery("delete from #__dbconnect_tables WHERE id='".($connectionId+0)."';");
    }
    return $db->query();
  }
  
  /**
   *  Funkce pro nastavení sdílení připojení
   */     
  public function setSharedConnection($connectionId,$shared,$adminMode=false){
    if ($shared){
      $state=1;
    }else{
      $state=0;
    }
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    if ($adminMode){
      $db->setQuery("UPDATE #__dbconnect_tables SET shared='".$state."' WHERE uid='".$user->get('id')."' AND id='".($connectionId+0)."';");
    }else{
      $db->setQuery("UPDATE #__dbconnect_tables SET shared='".$state."' WHERE id='".($connectionId+0)."';");
    }
    return $db->query();
  } 
  
  /**
   *  Funkce pro vypsání všech záznamů pro aktuálního/všechny uživatele
   */     
  public function getConnections($orderBy='id'){
    $db=$this->getDBO();
    $user=&JFactory::getUser();
    $db->setQuery("SELECT * FROM #__dbconnect_tables WHERE shared=1 OR uid='".$user->get('id')."' ORDER BY $orderBy;");
    return $db->loadObjectList();
  }
  
  /**
   *  Funkce pro vypsání všech záznamů pro aktuálního/všechny uživatele
   */     
  public function getConnectionsAdminList($orderBy='id'){
    $db=$this->getDBO(); 
    $db->setQuery("SELECT #__dbconnect_tables.*,#__users.name AS joomla_name,#__users.username AS joomla_username FROM #__dbconnect_tables LEFT JOIN #__users ON #__dbconnect_tables.uid=#__users.id ORDER BY $orderBy;");
    return $db->loadObjectList();
  }   


}

/**
 *  Class pro DB připojení
 */     
class DbConnection{
  /**
   *  Funkce vracející heslo konkrétního připojení
   */     
  public function getPassword(){
    //TODO
    return $this->password;
  }   /*
  public function setPassword(){
    //TODO
  }     */
}
?>

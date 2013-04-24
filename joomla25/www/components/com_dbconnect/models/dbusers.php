<?php
/**
* Model pro práci s tabulkami uloženými v databázi v podobě connection stringů
*/                            

jimport('joomla.application.component.model');
                   
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectModelDbusers extends JModel
{
  const DB_SERVER='db.lmcloud.vse.cz';
  const DB_ADMIN_USER='vojir';
  const DB_ADMIN_PASSWORD='V-3p58GDw_CehhYA';
  const DB_TYPE='mysql';
	
	/**
	 *   Funkce pro uložení informací o uživatelských přístupech do pracovní databáze
	 */   	
  public function setDbUser($dbtype,$server,$username,$password,$dbname){
    $db=$this->getDBO();
    $user =& JFactory::getUser();
    
    $db->setQuery('SELECT * FROM #__dbconnect_dbusers WHERE uid="'.$user->get('id').'" LIMIT 1;');
    if ($db->loadObject()){
      //update
      $db->setQuery('UPDATE #__dbconnect_dbusers  SET `db_type`="'.$db->getEscaped($dbtype).'",`server`="'.$db->getEscaped($server).'",`username`="'.$db->getEscaped($username).'",`password`="'.$db->getEscaped($password).'",`db_name`="'.$db->getEscaped($dbname).'" WHERE `uid`="'.$user->get('id').'" LIMIT 1;');
      $db->query();
    }else{
      $db->setQuery('INSERT INTO #__dbconnect_dbusers (`uid`,`db_type`,`server`,`username`,`password`,`db_name`)VALUES("'.$user->get('id').'","'.$db->getEscaped($dbtype).'","'.$db->getEscaped($server).'","'.$db->getEscaped($username).'","'.$db->getEscaped($password).'","'.$db->getEscaped($dbname).'");');
      $db->query();
    }
  }
  
  /**
   *  Funkce vracející podrobnosti DB účtu konkrétního uživatele
   */
  public function getDbUser($repeated=false){  
    $user=&JFactory::getUser();
    $db=$this->getDBO();                        
    $db->setQuery('SELECT * FROM #__dbconnect_dbusers WHERE uid="'.$user->get('id').'" LIMIT 1;');
    $dbUser=$db->loadObject();
    if ((!$dbUser)||(!$this->checkDbUser($dbUser))){
      if (!$repeated){     
        $newDbUser=$this->generateDbUser();
      }                
      if (@$newDbUser){
        $this->setDbUser($newDbUser['db_type'],$newDbUser['server'],$newDbUser['username'],$newDbUser['password'],$newDbUser['db_name']);
        return $this->getDbUser(true);
      }else{
        return false;
      }
    }
    return $dbUser;
  }      
  
  /**
   *  Funkce pro vytvoření uživatelského účtu a databáze
   */     
  public function generateDbUser(){
    $user=&JFactory::getUser();
    $userId=$user->get('id'); 
    
    try{            
      $dsn=dbconnectModelDbusers::DB_TYPE.':host='.dbconnectModelDbusers::DB_SERVER; 
      $db=new PDO($dsn,self::DB_ADMIN_USER,self::DB_ADMIN_PASSWORD);
      //kontrola na jméno uživatele
      $queryUsers=$db->prepare('SELECT DISTINCT User FROM mysql.user;');
      $queryUsers->execute();
      $usersArr=array();
      $usersRows=$queryUsers->fetchAll(PDO::FETCH_CLASS);
      if (count($usersRows)>0){
        foreach ($usersRows as $userRow) {
        	$usersArr[]=$userRow->User;
        }
      }
      $userName='user_'.$userId;
      $userCounter=1;
      while(in_array($userName,$usersArr)){
        $userCounter++;
        $userName='user_'.$userId.'_'.$userCounter;
      }
      //kontrola, jestli existuje daná DB
      $queryDatabases=$db->prepare('SHOW DATABASES;');
      $queryDatabases->execute();
      $databasesRows=$queryDatabases->fetchAll(PDO::FETCH_CLASS);
      $databasesArr=array();
      if (count($databasesRows)>0){
        foreach ($databasesRows as $dbRow){
        	$databasesArr[]=$dbRow->Database;
        }
      }
      $dbCounter=1;
      $dbName='usersdb_'.$userId;
      while (in_array($dbName,$databasesArr)){
      	$dbCounter++;
        $dbName='usersdb_'.$userId.'_'.$dbCounter;
      }
      //vygenerovani hesla
      $userPassword='';
      for($x=0;$x<4;$x++){     
        $userPassword.=chr(rand(97,115));
        $userPassword.=rand(0,9);
      }            
      //vygenerovani v DB
      $query1=$db->prepare('CREATE USER "'.$userName.'"@"%" IDENTIFIED BY "'.$userPassword.'";');
      $query2=$db->prepare("GRANT USAGE ON * . * TO '".$userName."'@'%' IDENTIFIED BY '".$userPassword."' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;");
      $query3=$db->prepare('CREATE DATABASE `'.$dbName.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;');
      $query4=$db->prepare('GRANT ALL PRIVILEGES ON `'.$dbName.'`.* TO "'.$userName.'"@"%";');
      
      $result1=$query1->execute();
      $result2=$query2->execute();
      $result3=$query3->execute();
      $result4=$query4->execute();
      
      if (($result1)&&($result2)&&($result3)&&($result4)){                        
        return array('username'=>$userName,'password'=>$userPassword,'db_name'=>$dbName,'db_type'=>dbconnectModelDbusers::DB_TYPE,'server'=>dbconnectModelDbusers::DB_SERVER);
      }   
    }catch (PDOException $e) {
      return false;
    }
    return false;
  }
  
  /**
   *  Funkce pro kontrolu, jestli je uživatelský přístup k DB stále platný
   */
  public function checkDbUser($dbUser){     
    try{                                                    
      $dsn=$dbUser->db_type.':dbname='.$dbUser->db_name.';host='.$dbUser->server; ///exit(var_dump(array($dsn,$dbUser->username,$dbUser->password)));
      $db=new PDO($dsn,$dbUser->username,$dbUser->password);   
      unset($db);   
    }catch (PDOException $e) {
      return false;
    }
    return true;
  }      

}
?>

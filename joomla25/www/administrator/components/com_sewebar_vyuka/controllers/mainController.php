<?php

jimport( 'joomla.application.component.controller' );
 
class dbconnectController extends JController{
  var $document;
	
	/**
	 *   Akce pro vytvoření nového záznamu o možném připojení tabulky
	 */   	
  public function newDatabase(){
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    //zjistime, jestli mame ukladat data, nebo jen zobrazit formular
    if ((JRequest::getString('save','')=='connection')&&(JRequest::getString('step','')=='3')){
      //uložíme záznam
      $connectionsModel->insertConnection($_POST['db_type'],$_POST['db_server'],$_POST['db_username'],$_POST['db_password'],$_POST['db_database'],$_POST['db_table'],$_POST['db_primary_key'],$_POST['db_shared_connection']);      
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=listConnections',false);
    }else{
      $view = &$this->getView('newDatabase',$this->document->getType());
      
      $view->setModel($connectionsModel,true);
      if (isset($_POST['db_type'])&&isset($_POST['db_username'])&&isset($_POST['db_password'])&&isset($_POST['db_server'])&&isset($_POST['db_database'])){
        $unidbModel=&$this->getModel('Unidb','dbconnectModel');       
        $view->assign("pdoError",$unidbModel->setDB($_POST['db_type'],$_POST['db_server'],$_POST['db_username'],$_POST['db_password'],$_POST['db_database']));
        $view->setModel($unidbModel,false);
      }
      
      $view->display();
    }                  
  }
  
  /**
   *  Akce pro zobrazení ukázky tabulky  
   */     
  public function showTable(){                  
    $view=&$this->getView('showTable',$this->document->getType());
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $unidbModel= & $this->getModel('Unidb', 'dbconnectModel');
    
    $connectionId=JRequest::getInt('connection_id',-1);
    $connection=$connectionsModel->getConnection($connectionId,true);
    if($connection){               
      //máme načíst připojení z databáze
      $dbtype=$connection->db_type;
      $dbserver=$connection->server;
      $dbusername=$connection->username;
      $dbpassword=$connection->password;
      $dbdatabase=$connection->db_name;
      $dbtable=$connection->table;
    }else{
      //pokusíme se načíst parametry připojení z URL
      $dbtype=JRequest::getVar('db_type','mysql');
      $dbserver=JRequest::getVar('db_server','localhost');
      $dbusername=JRequest::getVar('db_username','');       
      $dbpassword=base64_decode(urldecode(JRequest::getVar('db_password',''))); 
      $dbdatabase=JRequest::getVar('db_database');
      $dbtable=JRequest::getVar('db_table');
    } 
    
    $dbError=$unidbModel->setDB($dbtype,$dbserver,$dbusername,$dbpassword,$dbdatabase);
    if ($dbError!=''){
      JError::raiseError(500,$dbError);
    }else{
      //
      $view->assign('dbtype',$dbtype);
      $view->assign('dbserver',$dbserver);
      $view->assign('dbusername',$dbusername);
      $view->assign('dbpassword',$dbpassword);
      $view->assign('dbtable',$dbtable);
    }
    $view->setModel($unidbModel,false);
    $view->display();
  }
  
  /**
   *  Akce pro vylistovani všech připojení daného uživatele
   */     
  public function listConnections(){ 
    $view=&$this->getView('listConnections',$this->document->getType());
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $view->setModel($connectionsModel,true);
    $view->display();
  }
  
  /**
   *  Akce pro vylistovani všech připojení daného uživatele
   */     
  public function selectConnection(){ 
    $view=&$this->getView('selectConnection',$this->document->getType());
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $view->setModel($connectionsModel,true);
    $view->display();
  }  
  
  /**
   *  Akce pro vylistovani všech připojení daného uživatele
   */     
  public function adminConnections(){
    $view=&$this->getView('adminConnections',$this->document->getType());
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $view->setModel($connectionsModel,true);
    $view->display();
  }
  
  /**
   *  Funkce pro odstraneni pripojeni k DB
   */     
  public function deleteConnection(){
    $connectionId=JRequest::getInt('connection_id');
    $user =& JFactory::getUser();
    $userId=$user->get('id');
    $adminMode=@$_POST['admin_mode'];
    if ($adminMode){
      $task='adminConnections';
    }else{
      $task='listConnections';
    }
    
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connection=$connectionsModel->getConnection($connectionId);
    if ((@$connection->uid!=$userId)&&($adminMode!='ok')){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    //zobrazíme dotaz, nebo to rovnou smazeme?
    if (JRequest::getString('xx','xx')==JText::_('DELETE')){
      $connectionsModel->deleteConnection($connection->id,($adminMode=='ok'));
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task='.$task,false);
    }elseif (JRequest::getString('xx','xx')==JText::_('STORNO')){
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task='.$task,false);
    }else{
      $view=&$this->getView('deleteConnection',$this->document->getType());
      $view->assign('adminMode',$adminMode);
      $view->assign('connection',$connection);
      $view->display();
    }
  }
  
  /**
   *  Funkce pro nastavení sdílení připojení k DB
   */     
  public function shareConnection(){
    $connectionId=JRequest::getInt('connection_id');
    $user =& JFactory::getUser();
    $userId=$user->get('id');
    $adminMode=@$_POST['admin_mode'];
    if ($adminMode){$task='adminConnections';}else{$task='listConnections';}
    
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connection=$connectionsModel->getConnection($connectionId);
    if ((@$connection->uid!=$userId)&&($adminMode!='ok')){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    $connectionsModel->setSharedConnection($connection->id,($connection->shared==0),($adminMode=='ok'));
    $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task='.$task,false);
  }
  
  public function newDMTask(){
    return $this->newEditDMTask();
  }
  
  public function editDMTask(){
    return $this->newEditDMTask();
  }
  
  /**
   *  Akce pro založení nového uživatele
   */     
  private function newEditDMTask(){///TODO - dořešit mapování
    //overime opravneni a nacteme info o connection
    $taskId=JRequest::getInt('task_id',0);
    if ($taskId>0){
      $tasksModel=&$this->getModel('Tasks','dbconnectModel');
      $task=$tasksModel->getTask($taskId);
    if ($task)
      $connectionId=$task->db_table;
    }else{
      $connectionId=JRequest::getInt('connection_id');
    }
              
    
    $user=& JFactory::getUser();
    $userId=$user->get('id');
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connection=$connectionsModel->getConnection($connectionId);        
    if ((@$connection->uid!=$userId)&&($adminMode!='ok')){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }                                 
    
    //zjistime, jestli 
    if ((JRequest::getString('save','')=="ok")&&(@$_POST['name']!='')&&(@$_POST['connection_id']!='')){  
      //máme uložit výsledek
      $tasksModel=&$this->getModel('Tasks','dbconnectModel');
      //potrebujeme slozit XML s informacemi o sloupcich
      $columnsXml='<columns>';        
      foreach ($_POST as $key=>$value) {
        if (ereg("(.+)_useColDM",$key)){
          $columnName=substr($key,0,strlen($key)-9);
          if ($value==1){
            $useValue=1;
          }else{
            $useValue=0;
          }
          $columnsXml.='<column name="'.$columnName.'" use="'.$useValue.'" type="'.@$_POST[$columnName.'_type'].'" />';
        }
      }
      $columnsXml.='</columns>';
      //save
      if ($task){     
        $tasksModel->updateBasicTask($task->id,$_POST['name'],$connection->id,$columnsXml);
      }else{
        $tasksModel->insertBasicTask($_POST['name'],$connection->id,$columnsXml);
      }
      //redirect na vypis uloh
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=listDMTasks',false);
    }else{
      //máme zobrazit výběr sloupců
      $view=&$this->getView('newEditDMTask',$this->document->getType());
      //nastavíme uniDB model
      $unidbModel=&$this->getModel('Unidb','dbconnectModel');     
      $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->password,$connection->db_name);
      if ($dbError!=''){
        JError::raiseError(500,$dbError);
        return ;
      }                              
      //priradime connection do view
      $view->assignRef('connection',$connection);
      if ($task){
        $view->assignRef('dmTask',$task);
      }
      $view->setModel($connectionsModel,true);
      $view->setModel($unidbModel);
      $view->display();
    }
  }
  
  /**
   *  Akce pro vylistovani všech připojení daného uživatele
   */     
  public function listDMTasks(){        
    $view=&$this->getView('listDMTasks',$this->document->getType());
    $tasksModel= & $this->getModel('Tasks', 'dbconnectModel');
    $view->setModel($tasksModel,true);
    $view->display();
  }  
  
  
  /**
   *  Funkce pro odstraneni DM úlohy
   */     
  public function deleteDMTask(){
    $taskId=JRequest::getInt('task_id');
    $tasksModel= & $this->getModel('Tasks','dbconnectModel');
    
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    //zobrazíme dotaz, nebo to rovnou smazeme?
    if (JRequest::getString('xx','xx')==JText::_('DELETE')){
      $tasksModel->deleteTask($taskId);
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=listDMTasks',false);
    }elseif (JRequest::getString('xx','xx')==JText::_('STORNO')){
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=listDMTasks',false);
    }else{
      $view=&$this->getView('deleteDMTask',$this->document->getType());
      $view->assign('task',$task);
      $view->display();
    }
  }  
  
  
    
  /**
   *  Akce pro zobrazení informace o připojení k DB  
   */     
  public function showConnectionInfo(){                  
    $view=&$this->getView('showConnectionInfo',$this->document->getType());
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    
    $connectionId=JRequest::getInt('connection_id',-1);
    $connection=$connectionsModel->getConnection($connectionId,true);
    if (!$connection){
      JError::raiseError(500);
      return;
    }
    $view->assign('connection',$connection);
    
    $view->display();
  }
  
  /**
   *  Funkce pro spuštění mapování
   */     
  public function mapping(){
    $taskId=JRequest::getInt('task_id');
    $tasksModel= & $this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $connection=$connectionsModel->getConnection($task->db_table,true);
    if (!$connection){
      JError::raiseError(500);
      return;
    }
    
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');     
    $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->password,$connection->db_name);
    if ($dbError!=''){
      JError::raiseError(500,$dbError);
      return ;
    }

    //pripravime si obsah clanku - vystup aktualne nepotrebujeme...
    $unidbModel->getContentXML($task,false);
    
    $this->_redirect=JRoute::_('index.php?option=com_mapping&task=startTaskMapping&id='.$task->id,false);
  } 
  
  /**
   *  Funkce pro vybrání typu preprocessingu
   */      
  public function preprocessing(){
    $taskId=JRequest::getInt('task_id');
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    $fml=$dataModel->loadArticleXML($task->fml_article);
    
    
  
  } 
  
  
  /**
   *  Funkce pro vygenerování PMML a vytvoření KBI zdroje
   */     
  public function generatePMML(){
    $taskId=JRequest::getInt('task_id');
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    $fml=$dataModel->loadArticleXML($task->fml_article);
    
    
    $pmmlModel=&$this->getModel('Pmml','dbconnectModel');
    
    
  } 
  
  
  /**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{                                        
		parent::__construct( $default );
		$this->document =& JFactory::getDocument();
	}

}
?>

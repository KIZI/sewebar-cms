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

      $connectionId=$connectionsModel->insertConnection($_POST['db_type'],$_POST['db_server'],$_POST['db_username'],$_POST['db_password'],$_POST['db_database'],$_POST['db_table'],$_POST['db_primary_key'],$_POST['db_shared_connection']);      
      if (JRequest::getString('quickDMTask')=='ok'){
        $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=quickDMTask&connection_id='.$connectionId,false));
      }else{
        $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=listConnections',false));
      }      
    }else{
      $view = &$this->getView('newDatabase',$this->document->getType());
      $view->assign('quickDMTask',JRequest::getString('quickDMTask',''));
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
      $dbpassword=$connection->getPassword();
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
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task='.$task,false));
    }elseif (JRequest::getString('xx','xx')==JText::_('STORNO')){ 
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task='.$task,false));
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
    $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task='.$task,false));
  }
  
  public function newDMTask(){
    return $this->newEditDMTask();
  }
  
  public function editDMTask(){
    return $this->newEditDMTask();
  }
  
  public function quickDMTask(){
    $_POST['quickDMTask']='ok';
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
    //if ((@$connection->uid!=$userId)&&(@$adminMode!='ok')){
    if ((@$connection->uid!=$userId)){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }                                 
    
    //zjistime, jestli                                                                            
    if ((JRequest::getString('save','')=="ok")&&(@$_POST['name']!='')&&(@$_POST['connection_id']!='')){  
      //máme uložit výsledek
      $tasksModel=&$this->getModel('Tasks','dbconnectModel');
      //potrebujeme slozit XML s informacemi o sloupcich
      $columnsXml='<columns>';        
      foreach ($_POST as $key=>$columnName) {
        if (preg_match("/(.+)_useColDM_colName/",$key)){
          //$columnName=substr($key,0,strlen($key)-9);
          if (@$_POST[$columnName.'_useColDM']==1){
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
        $taskId=$tasksModel->insertBasicTask($_POST['name'],$connection->id,$columnsXml);
      }
      //redirect na vypis uloh
      if (($_POST['quickDMTask']=='ok')&&($taskId>0)){
        $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=quickDMTask_generate&task_id='.$taskId.'&generatePreprocessing='.JRequest::getVar('generatePreprocessing'),false));
      }else{
        $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=listDMTasks',false));
      }
    }else{                         
      //máme zobrazit výběr sloupců
      if ($_POST['quickDMTask']=='ok'){
        $view=&$this->getView('QuickDMTask',$this->document->getType());
      }else{
        $view=&$this->getView('newEditDMTask',$this->document->getType());
      }   
      //nastavíme uniDB model
      $unidbModel=&$this->getModel('Unidb','dbconnectModel');     
      $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->getPassword(),$connection->db_name);
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
   *  Akce pro vygenerování quick task
   */     
  public function quickDMTask_generate(){       
    $taskId=JRequest::getInt('task_id');
    $tasksModel= & $this->getModel('Tasks','dbconnectModel');
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $generatorModel=&$this->getModel('Generator','dbconnectModel');
     
    
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }           
    $user=& JFactory::getUser();
    $connection=$connectionsModel->getConnection($task->db_table);
    if (!$connection){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');
    $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->getPassword(),$connection->db_name);
    if ($dbError!=''){
      JError::raiseError(500,$dbError);
      return ;
    }
    
    
    //potřebujeme vygenerovat BKEF a FML article
    $curDateStr=date(JText::_('DATETIME_FORMAT'));
    
    
    if (!$generatorModel->processData($unidbModel->getContentXML($task,true),$connection->table,(JRequest::getString('generatePreprocessing','')=='ok'))){
      //TODO show error
      exit('BKEF OR FML GENERATION FAILED!');
      return;
    }
    //máme vygenerováno, tak jdeme ukládat
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefArticleTitle=$connection->table.' - BKEF ('.$curDateStr.')';
    $fmlArticleTitle=$connection->table.' - FML ('.$curDateStr.')';
    $bkefXML=$generatorModel->getBkefXML();
    if ($bkefXML){
      $bkefArticleId=$dataModel->newArticle($bkefArticleTitle,$bkefXML);
    }
    
    $taskParams=array();
    if (@$bkefArticleId){
      $taskParams['bkef']=$bkefArticleId;
      $fmlXML=$generatorModel->getFmlXML($bkefArticleId,$bkefArticleTitle,$task->id,$task->name);
      if ($fmlXML){
        $fmlArticleId=$dataModel->newArticle($fmlArticleTitle,$fmlXML);
      }
      if (@$fmlArticleId){
        $taskParams['fml']=$fmlArticleId;
        $preprocessed=$generatorModel->isPreprocessed();
      }else{
        $fmlArticleTitle='';
      }
    }else{
      $bkefArticleTitle='';
    }
    
    $tasksModel->updateTaskArticles($task->id,$taskParams);  
      
    //pokud došlo k namapování, do view doplníme informaci pro přesměrování, jinak view prostě jen zobrazíme
    $view=&$this->getView('QuickDMTaskGenerated',$this->document->getType());    
    if ($preprocessed){
      $view->redirectUrl=JRoute::_('index.php?option=com_dbconnect&task=generatePMML&taskId='.$task->id,false);
    }
    $view->assignRef('task',$tasksModel->getTask($taskId));
    $view->assign('bkefArticleTitle',$bkefArticleTitle);
    $view->assign('fmlArticleTitle',$fmlArticleTitle);
    $view->display();  
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
    $layoutStr=JRequest::getString('layout','');
    if ($layoutStr!=''){$layoutStr='&layout='.$layoutStr;}
    if (JRequest::getString('xx','xx')==JText::_('DELETE')){
      $tasksModel->deleteTask($taskId);
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=listDMTasks'.$layoutStr,false));
    }elseif (JRequest::getString('xx','xx')==JText::_('STORNO')){
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=listDMTasks'.$layoutStr,false));
    }else{
      $view=&$this->getView('deleteDMTask',$this->document->getType());
      $view->assign('task',$task);
      $view->display();
    }
  }  
  
  /**
   *  Funkce pro naklonování úlohy
   */     
  public function cloneDMTask(){        
    $taskId=JRequest::getInt('task_id');
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    //pokud je zadany novy nazev, tak úlohu naklonujeme, jinak zobrazíme formulář
    $name=JRequest::getString('name','');
    if (trim($name)==''){
      $name=$task->name.' (copy - '.date(JText::_('DATETIME_FORMAT')).')';
    }                
    if (@$_POST['action']==JText::_('CREATE_COPY')){ 
      //máme spustit klonování
      $tasksModel->cloneTask($taskId,$name);
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=listDMTasks',false));
    }elseif(!isset($_POST['action'])){      
      $view=&$this->getView('CloneDMTask',$this->document->getType());
      $view->assign('task',$task);
      $view->assign('taskName',$name);  
      $view->display();
    }else{
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=listDMTasks',false));
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
    $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->getPassword(),$connection->db_name);
    if ($dbError!=''){
      JError::raiseError(500,$dbError);
      return ;
    }

    //pripravime si obsah clanku - vystup aktualne nepotrebujeme...
    $unidbModel->getContentXML($task,true);
    
    $this->setRedirect(JRoute::_('index.php?option=com_mapping&task=startTaskMapping&id='.$task->id,false));
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
    
    $view=&$this->getView('Preprocessing',$this->document->getType());
    
    
    $fmlModel=&$this->getModel('Fml','dbconnectModel');
    if (!$fmlModel->setFml($fml)){      exit('Neplatné mapování! Spusťte prosím znovu krok mapování...');      //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    $view->setModel($fmlModel,false);
    
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');     
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    $view->setModel($bkefModel,false);                   
    
    $view->assign('taskId',$taskId);
    $view->assign('bkef',$bkef);
    $view->assign('fml',$fml);
   
    $view->display();
  } 
  
  /**
   *  Funkce pro vybrání typu preprocessingu (iframe)
   */      
  public function showPreprocessingHints(){  exit('TODO');
    $maName=JRequest::getString('maName');
    $formatName=JRequest::getString('formatName');
    $pmmlName=JRequest::getString('pmmlName');
    $taskId=JRequest::getInt('taskId');
    
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    
    $view=&$this->getView('ShowPreprocessingHints',$this->document->getType());
    
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    $view->setModel($bkefModel,false);
    
    
    $view->assign('maName',$maName);
    $view->assign('formatName',$formatName);
    $view->assign('pmmlName',$pmmlName);
    $view->assign('taskId',$taskId);
   
    $view->display();
  }   
  
  /**
   *  Akce pro vybrání preprocessing hintu k danému mapování
   */     
  public function selectPreprocessingHint(){  exit('TODO');
    $maName=JRequest::getString('maName');
    $formatName=JRequest::getString('formatName');
    $pmmlName=JRequest::getString('pmmlName');
    $preprocessingName=JRequest::getString('preprocessingName');
    $taskId=JRequest::getInt('taskId');
                                                           
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return ;
    }                                               
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
                                         
    if (($preprocessingName=='')||($bkefModel->preprocessingHintExists($maName,$formatName,$preprocessingName))){
      //existuje zvolený preprocessing, nebo je zadání prázdné - přidáme ho do FML
      $fmlModel=&$this->getModel('Fml','dbconnectModel');
      $fml=$dataModel->loadArticleXML($task->fml_article);
      $fmlModel->setFml($fml);
      $fmlModel->setPreprocessingHint($pmmlName,$maName,$formatName,$preprocessingName); 
      
      $dataModel->saveArticleXML($task->fml_article,$fmlModel->getFml());
      //TODO
    }                        
    //máme uloženo - přesměrujeme uživatele zpátky na mapování    
    $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&task=preprocessing&task_id='.$taskId,false));
  }
  
  
  
  
  
  public function gotoARDesigner(){
    $taskId=JRequest::getInt('taskId',JRequest::getInt('task_id'));
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return ;
    }
    $kbiSource=$task->kbi_source;
    $this->setRedirect(str_replace(array('{$server}','{$1}'), array('http://'.$_SERVER['HTTP_HOST'],$kbiSource), JText::_('IZI_MINER_URL')));
    ///exit('TODO: kontrola, jestli miner stále existuje a přechod na ARDesigner...');
    
    //TODO!!!
  }
  
  
  /**
   *  Funkce pro vygenerování PMML a vytvoření KBI zdroje
   */     
  public function generatePMML(){       
    $taskId=JRequest::getInt('taskId',JRequest::getInt('task_id'));
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);      
    if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    $fml=$dataModel->loadArticleXML($task->fml_article);  
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connection=$connectionsModel->getConnection($task->db_table);
    $fmlModel=&$this->getModel('Fml','dbconnectModel');
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
                                                     
    if ((!$connection)||(!$fml)||(!$bkef)){           exit('err');
      //TODO vypsání informace o tom, že zatím není všechno nastaveno!
      JError::raiseError(500,JText::_('NOT_PREPARED'));
      return ;
    }                                     
                                    
    $pmmlModel=&$this->getModel('Pmml','dbconnectModel');  
    $fmlModel->setFml($fml);
    $bkefModel->setBkef($bkef);   
    $pmml=$pmmlModel->generatePMML($task,$connection,$bkefModel,$fmlModel);
                                   
                                   
                                                         //exit(var_dump($pmml));
    //TODO configArr
    $defaultKbiSource=$task->kbi_source;
    $kbiSource=$this->generateKbiSource(null,$connection,$task,$pmml);
//                             exit(var_dump($kbiSource));
    if (JRequest::getString('from','')){
      //pokud spouštíme import z iframu izi-mineru, tak zobrazíme jiné view
      if ($defaultKbiSource==$kbiSource){
        $view = &$this->getView('IziReloadPMML',$this->document->getType());
        $view->display();
      }else{
        $view = &$this->getView('IziGeneratePMML',$this->document->getType());
        $session = JFactory::getSession();  
        if ($session->has('iziMinerUrl','dbconnect')){
          $url=$session->get('iziMinerUrl','','dbconnect');     
          if (strpos($url,'?')){
            $url.='&';
          }else{
            $url.='?';
          }
          $url.='id_dm='.$kbiSource;           
          $session->clear('iziMinerUrl','dbconnect');
          $view->assign('redirectUrl',$url);
        }else{
          $view->assign('redirectUrl',str_replace(array('{$server}','{$1}'), array('http://'.$_SERVER['HTTP_HOST'],$kbiSource), JText::_('IZI_MINER_URL')));
        }
        
        $view->display();
      }
    }else{
      //přesměrování na IZI miner z iframu...
      $view = &$this->getView('GeneratePMML',$this->document->getType());
      $view->assign('redirectUrl',str_replace(array('{$server}','{$1}'), array('http://'.$_SERVER['HTTP_HOST'],$kbiSource), JText::_('IZI_MINER_URL')));
      $view->display();  
    }
  } 
  
  /**
   *  Akce vracející informace o přihlášeném uživateli
   */
  public function userInfo(){
    $user=&JFactory::getUser();
    $view=@$this->getView('userInfo','raw','IziView');
    $view->assignRef('user',$user);
    $view->display();           
    /*$document = JFactory::getDocument();
    $document->setType('raw');           
    echo json_encode($returnArr);*/
  }      
  
  /**
   *  Metoda pro vytvoření KBI Source a import zadání úlohy v PMML
   */                                                                                    //TODO výběr minerUrl
  private function generateKbiSource($configArr,$connection,$task,$pmml,$minerUrl='http://connect-dev.lmcloud.vse.cz/SewebarConnectNext'){
    $kbiModel=&$this->getModel('Kbi','dbconnectModel');        
    $configArr=array('type'=>'LISPMINER','name'=>'TEST','method'=>'POST','url'=>$minerUrl);

    JLoader::import('KBIntegrator', JPATH_LIBRARIES . DS . 'kbi');     
    $kbi = KBIntegrator::create($configArr);
    //přiřazení uživatele ze session
    $session =& JFactory::getSession();
    $userData=$session->get('user',array(),'sewebar');
    if (!empty($userData)){
      $kbi->setUser($userData);
    }
    //--přiřazení uživatele ze session

    if ($task->kbi_source<=0){  
      $registerNewLispminer=true;
    }else{
      //TODO kontrola, jestli lispminer existuje
      $registerNewLispminer=false;
    }         
                                  
    /*-----------registrace LM a import----------*/                         
      //TODO kontrola, jestli server existuje
      /*musíme vytvořit nový LM server*/
      
      if ($connection->db_type=='mysql'){
        $dbType='MySQLConnection';
      }else{
        $dbType='AccessConnection';
      }          

      if ($registerNewLispminer){       
        //máme zaregistrovat nový lisp miner
        try{
          $lispminerId=$kbi->register(array(
                                        'server'=>$connection->server,
                                        'database'=>$connection->db_name,
                                        'username'=>$connection->username,
                                        'password'=>$connection->getPassword(),
                                        'type'=>$dbType
                                      ));        
                                                            
        }catch(Exception $e){
          exit('Při vytváření LM zdroje došlo k chybě. '.$e->getMessage());
        }
        //--máme zaregistrovat nový lisp miner
      }else{                   
        //miner už existuje
        $kbiSource=$kbiModel->getSource($task->kbi_source);  
        $kbiSourceParams=json_decode($kbiSource->params,true);
        $lispminerId=$kbiSourceParams['miner_id'];        
      }
                  
      if ($lispminerId){      
        //máme zaregistrovaný LM server - vytvorime KBI zdroj
        ////
        try{
          $importResult=$kbi->importDataDictionary($pmml,$lispminerId);//TODO kontrola $importResult !!
        }catch(Exception $e){
          exit('Při vytváření LM zdroje došlo k chybě. '.$e->getMessage());
        }
        
        //exit(var_dump($importResult));
        if ($importResult){
          //máme úspěšně naimportováno
          if ($task->kbi_source<=0){ 
            $kbiSource=$kbiModel->newLMSource($task->name,$minerUrl,$lispminerId,$connection->table);
            $tasksModel=&$this->getModel('Tasks','dbconnectModel');
            $tasksModel->updateTaskKbiSource($task->id,$kbiSource);
            $task->kbi_source=$kbiSource;
          }else{       //TODO pokud miner neexistoval, je potřeba aktualizovat info
            ///$kbiModel->updateLMSource_minerId($task->kbi_source,$lispminerId);
          }
        }else{
          //TODO odstraneni lispmineru, hláška pro uživatele
        }
        // 
      }else{
        //TODO nepodařilo se zaregistrovat LM, hláška pro uživatele
      }  
    /*-----------//registrace LM a import----------*/    
    return $task->kbi_source;
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

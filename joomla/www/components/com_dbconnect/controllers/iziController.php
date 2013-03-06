<?php   
jimport( 'joomla.application.component.controller' );
          
class IziController extends JController{
  var $document;
	  
  /**
   *   Akce pro upload testovacího CSV souboru
   */     
  public function uploadDemoCSV(){      
    
    $path='./components/com_dbconnect/tmp/';
    $counter=0;
    while(file_exists($path.'demo_'.$counter.'.csv')){
      $counter++;
    }
    
    if ($_REQUEST['file']=='big'){
      copy('./components/com_dbconnect/media/loansBig.csv',$path.'demo_'.$counter.'.csv');
    }else{
      copy('./components/com_dbconnect/media/loans.csv',$path.'demo_'.$counter.'.csv');
    }     
    
             
             
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    if ($fileId=$uploadsModel->insertFile('demo_'.$counter.'.csv',$path.'demo_'.$counter.'.csv')){
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=uploadCSV_step2&file='.$fileId,false);
      return ;
    } 
      
  }  
    
    
  /**
   *  Akce vracející informace o přihlášeném uživateli
   */
  public function userInfo(){
    $user=&JFactory::getUser();
    $view=@$this->getView('UserInfo','raw');
    $view->assignRef('user',$user);
    $view->display();           
    /*$document = JFactory::getDocument();
    $document->setType('raw');           
    echo json_encode($returnArr);*/
  }      
  
  /**
   *  Akce pro zobrazení začátku výběru nového datového zdroje
   */      
  public function newTask(){
    $view=$this->getView('IziNewTask',$this->document->getType());
                                        
    $tasksModel=&$this->getModel('Tasks','dbconnectModel'); 
                                          
    $order=JRequest::getVar('order','id');
    $tasks=$tasksModel->getTasks($order);
  	$view->assignRef('tasks',	$tasks);                   
    
    $user=&JFactory::getUser();
    $view->assignRef('user',$user);
    $view->display();
  }
    
  /**
   *  Akce pro zobrazení začátku výběru nového datového zdroje
   */      
  public function newDatasource(){
    $view=$this->getView('IziNewDatasource',$this->document->getType());
    $view->display();
  }
  
  /**
   *   Akce pro upload CSV souboru
   */     
  public function uploadCSV(){      
    if (isset($_FILES['url'])){     
      //test, jestli byl odeslán formulář
      $fileData=$_FILES['url'];
      $fileName=$fileData['name'];if (is_array($fileName)){$fileName=$fileName[0];}
      $fileTmpName=$fileData['tmp_name'];if (is_array($fileTmpName)){$fileTmpName=$fileTmpName[0];}
               
      $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
      if ($fileId=$uploadsModel->insertFile($fileName,$fileTmpName)){
        $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=uploadCSV_step2&file='.$fileId,false);
        return ;
      } 
    }
    $view=$this->getView('IziUploadCSV',$this->document->getType());
    $view->display();      
  }
  
  /**
   *  Funkce pro zadání základních parametrů uploadu CSV souboru
   */
  public function uploadCSV_step2(){   
    $fileId=JRequest::getInt('file',-1);
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    $fileData=$uploadsModel->getFile($fileId);
    
    if (!$fileData){
      //pokud nemáme data o nahraném souboru, tak uživatele přesměrujeme na nahrání jiného
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=uploadCSV',false);
      return ;
    }
    
    $view=$this->getView('IziUploadCSV_2',$this->document->getType());
    $view->assignRef('fileData',$fileData);
    $view->assign('table_name',$uploadsModel->cleanName($fileData->filename));
    $view->assign('delimitier',trim(JRequest::getString('delimitier',$uploadsModel->getCSVDelimitier($fileData->id))));
    $view->assign('enclosure',trim(JRequest::getString('enclosure','"')));
    $view->assign('escapeChar',trim(JRequest::getString('escapeChar','\\')));
    $view->display();
  }
  
  /**
   *  Funkce pro vykreslení náhledu na importovaná data - jen jako začátek tabulky...
   */
  public function uploadCSV_getData(){
    $fileId=JRequest::getInt('file',-1);
    
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    $view=$this->getView('IziUploadCSV_dataPreview','html');
                                                    
    $delimitier=JRequest::getString('delimitier',';');
    $enclosure=JRequest::getString('enclosure',',');
    $escapeChar=JRequest::getString('escape','\\');
    $encoding=JRequest::getString('encoding','utf8');
    
    $uploadsModel->iconvFile($fileId,$encoding);
    
    $view->csvData=$uploadsModel->analyzeCSV($fileId,$delimitier,$enclosure,$escapeChar);
    $view->csvRows=$uploadsModel->getRowsFromCsv($fileId,10,$delimitier,$enclosure,$escapeChar);
    $view->rowsCount=$uploadsModel->getRowsCount($fileId);
    $view->display();
  }
  
  /**
   *  Akce pro naimportování CSV do databáze
   */     
  public function uploadCSV_import(){             
    $fileId=JRequest::getInt('file',-1);
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    $fileData=$uploadsModel->getFile($fileId);
    
    if (!$fileData){
      //pokud nemáme data o nahraném souboru, tak uživatele přesměrujeme na nahrání jiného
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=uploadCSV',false);
      return ;
    }              
    
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel'); 
                                                      
    $delimitier=JRequest::getString('delimitier','');
    if ($delimitier==''){
      $delimitier=JRequest::getString('delimitier_text',';');
    }
    $enclosure=JRequest::getString('enclosure',',');
    $escapeChar=JRequest::getString('escape','\\');
    $encoding=JRequest::getString('encoding','utf8');
    $tableName=JRequest::getString('table_name','table'.rand(1,100));
    
    $uploadsModel->iconvFile($fileId,$encoding);         
    
    $dbusersModel=&$this->getModel('Dbusers','dbconnectModel');   
    $dbUser=$dbusersModel->getDbUser();     
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');             
    $unidbModel->setDB($dbUser->db_type,$dbUser->server,$dbUser->username,$dbUser->password,$dbUser->db_name);
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');       
                                              //TODO tady by to asi chtělo kontrolu, jestli to prošlo!
    $tableName=$uploadsModel->importToDb($unidbModel,$tableName,$fileId,$delimitier,$enclosure,$escapeChar);
                                                                        
    $connectionId=$connectionsModel->insertConnection($dbUser->db_type,$dbUser->server,$dbUser->username,$dbUser->password,$dbUser->db_name,$tableName,'id',false);
                                    
    $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newDmTask&tmpl=component&connection_id='.$connectionId,false);
  }      
  
  /**
   *  Akce pro vypsání jednotlivých připojení
   */     
  public function listConnections(){
    $view=&$this->getView('IziSelectConnection',$this->document->getType());
    $connectionsModel= &$this->getModel('Connections', 'dbconnectModel');
    $view->setModel($connectionsModel,true);
    $view->display();
  }
  
  /**
   *  Akce pro vypsání jednotlivých úloh
   */
  public function listDMTasks(){
    $view=&$this->getView('IziListDMTasks',$this->document->getType());
    $tasksModel= & $this->getModel('Tasks', 'dbconnectModel');
    $view->setModel($tasksModel,true);
    $view->display();
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
      $name=$task->name.' ('.JText::_('COPY').' - '.date(JText::_('DATETIME_FORMAT')).')';
    }                
    if (@$_POST['action']==JText::_('CREATE_COPY')){ 
      //máme spustit klonování
      $tasksModel->cloneTask($taskId,$name);
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=listDMTasks',false);
    }elseif(!isset($_POST['action'])){      
      $view=&$this->getView('IziCloneDMTask',$this->document->getType());
      $view->assign('task',$task);
      $view->assign('taskName',$name);  
      $view->display();
    }else{
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=listDMTasks',false);
    }   
  }
  
  
  /**
   *  Akce pro vyvolání vytvoření nového atributu po vyvolání akce z javascriptu
   */
  public function newAttribute(){               
    //TODO potřebujeme načíst příslušné hodnoty pro adresu, na kterou se má přejít
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');    
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $kbiId=JRequest::getInt('kbi',-1);
    if ($taskId>0){
      $task=$tasksModel->getTask($taskId);
    }elseif($kbiId>0){
      $task=$tasksModel->getTaskByKbi($kbiId);
    }                            
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
                                
    $fmlModel=&$this->getModel('Fml','dbconnectModel'); 
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefModel=&$this->getModel('Bkef','dbconnectModel'); 
                       
    //kontrola, jestli již byl daný sloupec obsažený v úloze...
    $columns=simplexml_load_string($task->columns);
    $columnsArr=array();
    $reload=false;
    if (count($columns)>0){
      foreach ($columns as $column){    
      	if ((string)$column['name']==$pmmlName){
          if ((string)$column['use']!='1'){
            $column['use']=1;
            $reload=true;
            $columnsXml=$columns->asXML();
            $tasksModel->updateBasicTask($task->id,$task->name,$task->db_table,$columnsXml,1);
          }
        }
      }
    }
    if ($reload){   
      //TODO redirect na znovuvygenerování úlohy
      //exit('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$task->id.'&newAttribute='.$pmmlName);
//      exit(JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$task->id.'&newAttribute='.$pmmlName,false));
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$task->id.'&newAttribute='.$pmmlName,false);
      return;
    }                                   
    //--kontrola, jestli již byl daný sloupec obsažený v úloze...
    
    $fml=$dataModel->loadArticleXML($task->fml_article);
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    
                           
    if (!$fmlModel->setFml($fml)){      exit('Neplatné mapování! Spusťte prosím znovu krok mapování...');      //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    
    $pmmlMapping=$fmlModel->getPmmlMapping($pmmlName);
                                
    $maName=$pmmlMapping['metaattributeName'];
    $formatName=$pmmlMapping['formatName'];
    
    //$preprocessingHints=$bkefModel->getPreprocessingHints($maName,$formatName);
    
    $view=@$this->getView('IziShowPreprocessingHints',$this->document->getType());
    //$view->assignRef('preprocessingHints',$preprocessingHints);
    $view->assignRef('format',$bkefModel->getFormat($maName,$formatName));
    $view->assign('taskId',$task->id);
    $view->assign('maName',$maName);
    $view->assign('formatName',$formatName);
    $view->assign('pmmlName',$pmmlName);    
    $view->display();
  }
  
  /**
   *  Akce pro vyvolání úpravy atributu po vyvolání akce z javascriptu
   */     
  public function editAttribute(){
    exit('NOT IMPLEMENTED');
  }
  
  /**
   *  Akce pro zobrazení konkrétního preprocessing hintu 
   */
  public function showPreprocessingHint(){       
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $kbiId=JRequest::getInt('kbi',-1);
    if ($taskId>0){
      $task=$tasksModel->getTask($taskId);
    }elseif($kbiId>0){
      $task=$tasksModel->getTaskByKbi($kbiId);
    }                     
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $maName=JRequest::getString('maName','');
    $formatName=JRequest::getString('formatName','');
    $preprocessingName=JRequest::getString('preprocessingName','');   
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');   
    $bkef=$dataModel->loadArticleXML($task->bkef_article); 
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }                                 
    $preprocessingHint=$bkefModel->getPreprocessingHint($maName,$formatName,$preprocessingName);  
    if (!$preprocessingHint){
      //neexistujici preprocessing hint
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&tmpl=component&col='.urlencode($pmmlName).'&task_id='.$taskId,false);
      return ;
    }
    //zjištění typu view, které se má použít
    $preprocessingType='';
    if (isset($preprocessingHint->EquidistantInterval)){
      $preprocessingType='Equidistant';
    }elseif(isset($preprocessingHint->IntervalEnumeration)){
      $preprocessingType='IntervalEnumeration';
    }elseif(isset($preprocessingHint->NominalEnumeration)){
      $preprocessingType='NominalEnumeration';
    }elseif(isset($preprocessingHint->EachValueOneBin)){
      $preprocessingType='EachOne';
    }
    $view=&$this->getView('IziShowPreprocessing_'.$preprocessingType,$this->document->getType());
    $view->assign('maName',$maName);
    $view->assign('formatName',$formatName);
    $view->assignRef('preprocessingHint',$preprocessingHint);
    $view->assign('pmmlName',$pmmlName);
    $view->assign('taskId',$task->id);
    $view->display();
  } 
  
  
  
  /**
   *  Akce pro vyvolání úpravy preprocessing hintu
   */
  public function editPreprocessingHint(){
    exit('TODO - výběr konkrétního preprocessing hintu (respektive jeho typu) a přesměrování na konkrétní funkci pro úpravu');
  }     
  
  /************************************************************************************************************/
  public function newPreprocessingHint_eachValueOneCategory(){
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $maName=JRequest::getString('maName','');
    $formatName=JRequest::getString('formatName','');
    
    $attributeName=trim(JRequest::getString('attributeName',''));
    if ($attributeName==''){
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      $view=&$this->getView('IziNewPreprocessing_EachOne',$this->document->getType());
      $view->assign('pmmlName',$pmmlName);
      $view->assign('maName',$maName);
      $view->assign('formatName',$formatName);
      $view->assign('taskId',$taskId);
      $view->display();
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      return;
    }
    //vyřešení vygenerování preprocessing hintu a jeho uložení
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
                         
    $fmlModel=&$this->getModel('Fml','dbconnectModel'); 
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefModel=&$this->getModel('Bkef','dbconnectModel'); 
    
    
    $fml=$dataModel->loadArticleXML($task->fml_article);
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    
                         
    if (!$fmlModel->setFml($fml)){      exit('Neplatné mapování! Spusťte prosím znovu krok mapování...');      //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    //vyřešení vygenerování preprocessing hintu a jeho uložení
    $preprocessingHint=$bkefModel->addNewPreprocessingHint_EachValueOneBin($maName,$formatName);
    if ($preprocessingHint){
      //došlo k uložení - potřebujeme doplnit FML a vygenerovat 
      $phName=(string)$preprocessingHint->Name;
      
      $fmlModel->setPreprocessingHint($pmmlName,$maName,$formatName,$phName,$attributeName); 
      $dataModel->saveArticleXML($task->fml_article,$fmlModel->getFml());
      $dataModel->saveArticleXML($task->bkef_article,$bkefModel->getBkef());
      
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$taskId,false);
      return ;
    }
    $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&tmpl=component&task_id='.$taskId.'&col='.$pmmlName,false);
  }
  
  public function editPreprocessingHint_equidistantInterval(){
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $maName=JRequest::getString('maName','');
    $formatName=JRequest::getString('formatName','');
    
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    
    $attributeName=trim(JRequest::getString('attributeName',''));
    $oldPhName=trim(JRequest::getString('oldPhName',''));
    if ($attributeName==''){
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      $view=&$this->getView('IziNewPreprocessing_Equidistant',$this->document->getType());
      $view->assign('pmmlName',$pmmlName);
      $view->assign('maName',$maName);
      $view->assign('formatName',$formatName);
      $view->assign('taskId',$taskId);
      $view->assign('oldPhName',$oldPhName);
      
      if ($oldPhName!=''){   
        $oldPreprocessingHint=$bkefModel->getPreprocessingHint($maName,$formatName,$oldPhName);
      }else{
        $oldPreprocessingHint=null;
      }               
      if ($oldPreprocessingHint){     
        $view->assign('start',(float)$oldPreprocessingHint->EquidistantInterval->Start);
        $view->assign('end',(float)$oldPreprocessingHint->EquidistantInterval->End);
        $view->assign('step',(float)$oldPreprocessingHint->EquidistantInterval->Step);
      }else{                                          
        $format=$bkefModel->getFormat($maName,$formatName);  
        $interval=@$format->Range->Interval[0]; 
        if ($interval){
          $start=(float)$interval['leftMargin'];
          $end=(float)$interval['rightMargin'];
          $view->assign('start',$start);
          $view->assign('end',$end);
          $view->assign('step',abs($end-$start)/10);
        }   
      }   
      
      
      $view->display();
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      return;
    }
    //vyřešení vygenerování preprocessing hintu a jeho uložení                         
    $fmlModel=&$this->getModel('Fml','dbconnectModel'); 
    $fml=$dataModel->loadArticleXML($task->fml_article);
                         
    if (!$fmlModel->setFml($fml)){      exit('Neplatné mapování! Spusťte prosím znovu krok mapování...');      //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    
    
    //kontrola, jestli updatujeme existující preprocessing hint
    $createdInfo=null;
    if ($oldPhName!=''){
      $oldPreprocessingHint=$bkefModel->getPreprocessingHint($maName,$formatName,$oldPhName);
      if ($oldPreprocessingHint){
        $createdInfo=array(
                       'timestamp'=>(string)$oldPreprocessingHint->Created->Timestamp,
                       'author'=>(string)$oldPreprocessingHint->Created->Author
                     );             
        //$bkefModel->deletePreprocessingHint($maName,$formatName,$oldPhName);             
      }
    }
    $start=$this->cleanNumber($_REQUEST['start']);
    $end=$this->cleanNumber($_REQUEST['end']);
    if ($end<$start){
      $x=$start;
      $start=$end;
      $end=$x;
    }
    $step=$this->cleanNumber($_REQUEST['step']);
    $paramsArr=array('start'=>$start,
                     'end'=>$end,
                     'step'=>$step);
    //vyřešení vygenerování preprocessing hintu a jeho uložení
    $phName='Equidistant ['.$start.';('.$step.');'.$end.']';
    $preprocessingHint=$bkefModel->addNewPreprocessingHint_EquidistantInterval($maName,$formatName,$phName,$paramsArr,$createdInfo);
    if ($preprocessingHint){
      //došlo k uložení - potřebujeme doplnit FML a vygenerovat 
      $phName=(string)$preprocessingHint->Name;
      
      $fmlModel->setPreprocessingHint($pmmlName,$maName,$formatName,$phName,$attributeName); 
      $dataModel->saveArticleXML($task->fml_article,$fmlModel->getFml());
      $dataModel->saveArticleXML($task->bkef_article,$bkefModel->getBkef());
      
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$taskId,false);
      return ;
    }
    $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&tmpl=component&task_id='.$taskId.'&col='.$pmmlName,false);
  }
  
  /**
   *  Akce pro připravění interval enumeration
   */     
  public function editPreprocessingHint_intervalEnumeration(){
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $maName=JRequest::getString('maName','');
    $formatName=JRequest::getString('formatName','');
    
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
    
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }

    $createdInfo=null;
    $oldPhName=trim(JRequest::getString('oldPhName',''));
    if ($oldPhName!=''){
      $oldPreprocessingHint=$bkefModel->getPreprocessingHint($maName,$formatName,$oldPhName);
      if (!isset($oldPreprocessingHint->IntervalEnumeration)){
        $oldPreprocessingHint=null;
      }
      if ($oldPreprocessingHint){
        $createdInfo=array(
                       'timestamp'=>(string)$oldPreprocessingHint->Created->Timestamp,
                       'author'=>(string)$oldPreprocessingHint->Created->Author
                     );             
        //$bkefModel->deletePreprocessingHint($maName,$formatName,$oldPhName);             
      }
    }else{
      $oldPreprocessingHint=null;
    }
    
    $attributeName=trim(JRequest::getString('attributeName',''));
    if ($attributeName==''){           
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      $view=&$this->getView('IziNewPreprocessing_IntervalEnumeration',$this->document->getType());
      $view->assign('pmmlName',$pmmlName);
      $view->assign('maName',$maName);
      $view->assign('formatName',$formatName);
      $view->assign('taskId',$taskId);
      $view->assign('format',$bkefModel->getFormat($maName,$formatName));
      $view->assign('preprocessingHint',$oldPreprocessingHint);
      $view->display();
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      return;
    }
    //vyřešení vygenerování preprocessing hintu a jeho uložení
                           
    $fmlModel=&$this->getModel('Fml','dbconnectModel'); 
    $fml=$dataModel->loadArticleXML($task->fml_article);
                         
    if (!$fmlModel->setFml($fml)){      exit('Neplatné mapování! Spusťte prosím znovu krok mapování...');      //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }                      
    //vytvoření $dataArr - struktura pro uložení preprocessing hintu
    $dataArr=array();
    $groupNamesArr=array(); 
    foreach ($_POST as $key=>$value) {     //TODO spojení všech potřebných hodnot a vytvoření struktury pole
      if (preg_match('/^group_\d+_name$/',$key)){
        //jde o název skupiny
        $dataArr[$value]=array();
        $groupNamesArr[$key]=$value;
      }
    }                       
    foreach ($_POST as $key=>$value) {  
      if(preg_match('/^group_\d+_interval_\d+$/',$key)){
        //jde o konkrétní hodnotu
        $keyArr=explode('_',$key);
        if (count($keyArr)<4){continue;}
        $groupKey=$keyArr[0].'_'.$keyArr[1];
        $groupName=$groupNamesArr[$groupKey.'_name'];
        $intervalDataArr=explode('#',$value);
        if (count($intervalDataArr)!=3){continue;}
        $dataArr[$groupName][]=array('closure'=>$intervalDataArr[0],'leftMargin'=>$intervalDataArr[1],'rightMargin'=>$intervalDataArr[2]);
      }
    }                      
    //odstranění prázdných skupin
    foreach ($dataArr as $key=>$valuesArr) {
    	if (count($valuesArr)==0){
        unset($dataArr[$key]);
      }
    }                                           
    
    //vyřešení vygenerování preprocessing hintu a jeho uložení
    $preprocessingHint=$bkefModel->addNewPreprocessingHint_IntervalEnumeration($maName,$formatName,$attributeName,$dataArr,$createdInfo);
    if ($preprocessingHint){                  
      //došlo k uložení - potřebujeme doplnit FML a vygenerovat 
      $phName=(string)$preprocessingHint->Name;
      
      $fmlModel->setPreprocessingHint($pmmlName,$maName,$formatName,$phName,$attributeName); 
      $dataModel->saveArticleXML($task->fml_article,$fmlModel->getFml());
      $dataModel->saveArticleXML($task->bkef_article,$bkefModel->getBkef());
      
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$taskId,false);
      return ;
    }
    $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&tmpl=component&task_id='.$taskId.'&col='.$pmmlName,false);
  } 
  
  /**
   *  Akce pro vygenerování nominal enumeration
   */     
  public function editPreprocessingHint_nominalEnumeration(){
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $maName=JRequest::getString('maName','');
    $formatName=JRequest::getString('formatName','');
    
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
    
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }

    $createdInfo=null;
    $oldPhName=trim(JRequest::getString('oldPhName',''));
    if ($oldPhName!=''){
      $oldPreprocessingHint=$bkefModel->getPreprocessingHint($maName,$formatName,$oldPhName);
      if (!isset($oldPreprocessingHint->NominalEnumeration)){
        $oldPreprocessingHint=null;
      }
      if ($oldPreprocessingHint){
        $createdInfo=array(
                       'timestamp'=>(string)$oldPreprocessingHint->Created->Timestamp,
                       'author'=>(string)$oldPreprocessingHint->Created->Author
                     );             
        //$bkefModel->deletePreprocessingHint($maName,$formatName,$oldPhName);             
      }
    }else{
      $oldPreprocessingHint=null;
    }
    
    $attributeName=trim(JRequest::getString('attributeName',''));
    if ($attributeName==''){      
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      $view=&$this->getView('IziNewPreprocessing_NominalEnumeration',$this->document->getType());
      $view->assign('pmmlName',$pmmlName);
      $view->assign('maName',$maName);
      $view->assign('formatName',$formatName);
      $view->assign('taskId',$taskId);
      $view->assign('format',$bkefModel->getFormat($maName,$formatName));
      $view->assign('preprocessingHint',$oldPreprocessingHint);
      $view->display();
      //nemáme zadaný název preprocessingu - zobrazíme view pro zadání 
      return;
    }
    //vyřešení vygenerování preprocessing hintu a jeho uložení
                               
    $fmlModel=&$this->getModel('Fml','dbconnectModel'); 
    $fml=$dataModel->loadArticleXML($task->fml_article);
                         
    if (!$fmlModel->setFml($fml)){      exit('Neplatné mapování! Spusťte prosím znovu krok mapování...');      //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    //vytvoření $dataArr - struktura pro uložení preprocessing hintu
    $dataArr=array();
    $groupNamesArr=array();
    foreach ($_POST as $key=>$value) {    
      if (preg_match('/^group_\d+_name$/',$key)){
        //jde o název skupiny
        $dataArr[$value]=array();
        $groupNamesArr[$key]=$value;
      }
    }
    foreach ($_POST as $key=>$value) {    
      if(preg_match('/^group_\d+_value_\d+$/',$key)){   
          //jde o konkrétní hodnotu
          $keyArr=explode('_',$key);
          if (count($keyArr)<4){continue;}   
          $groupKey=$keyArr[0].'_'.$keyArr[1].'_name';  
          $groupName=$groupNamesArr[$groupKey]; 
          $dataArr[$groupName][]=$value;
      }
    }
    //odstranění prázdných skupin
              
    foreach ($dataArr as $key=>$valuesArr) {
    	if (count($valuesArr)==0){
        unset($dataArr[$key]);
      }
    }                                            
    //vyřešení vygenerování preprocessing hintu a jeho uložení
    
    $preprocessingHint=$bkefModel->addNewPreprocessingHint_NominalEnumeration($maName,$formatName,$attributeName,$dataArr,$createdInfo);
    if ($preprocessingHint){    
      //došlo k uložení - potřebujeme doplnit FML a vygenerovat 
      $phName=(string)$preprocessingHint->Name;
      
      $fmlModel->setPreprocessingHint($pmmlName,$maName,$formatName,$phName,$attributeName);
      
      $dataModel->saveArticleXML($task->bkef_article,$bkefModel->getBkef());
      $dataModel->saveArticleXML($task->fml_article,$fmlModel->getFml());
      
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$taskId,false);
      return ;
    }                                     
    $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&tmpl=component&task_id='.$taskId.'&col='.$pmmlName,false);
  }
  /************************************************************************************************************/
  
  /**
   *  Metoda pro vytvoření KBI Source a import zadání úlohy v PMML
   */                                                                                    //TODO výběr minerUrl
  private function generateKbiSource($configArr,$connection,$task,$pmml,$minerUrl='http://connect-dev.lmcloud.vse.cz/SewebarConnect'){   
    $kbiModel=&$this->getModel('Kbi','dbconnectModel');        
    $configArr=array('type'=>'LISPMINER','name'=>'TEST','method'=>'POST','url'=>$minerUrl);                                      
    JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');     
    $kbi = KBIntegrator::create($configArr);
                   
    if ($task->kbi_source<=0){  
      $registerNewLispminer=true;
    }else{
      //TODO kontrola, jestli lispminer existuje
      $registerNewLispminer=false;
    }
                             
    if ($registerNewLispminer){
      //TODO kontrola, jestli server existuje
      /*musíme vytvořit nový LM server*/
      
      if ($connection->db_type=='mysql'){
        $dbType='MySQLConnection';
      }else{
        $dbType='AccessConnection';
      }          
      
      //$kbiSource=$kbi->register(array('server'=>'localhost','database'=>'barbora','type'=>'mysqlconnection','username'=>'lisp','password'=>'lisp'));
      $lispminerId=$kbi->register(array(
                                    'server'=>$connection->server,
                                    'database'=>$connection->db_name,
                                    'username'=>$connection->username,
                                    'password'=>$connection->password,
                                    'type'=>$dbType
                                  ));                               
      if ($lispminerId){
        //máme zaregistrovaný LM server - vytvorime KBI zdroj
        $importResult=$kbi->importDataDictionary($pmml,$lispminerId);//TODO kontrola $importResult !!
        //exit(var_dump($importResult));
        if ($importResult){
          //máme úspěšně naimportováno
          if ($task->kbi_source<=0){
            $kbiSource=$kbiModel->newLMSource($task->name,$minerUrl,$lispminerId,$connection->table);
            $tasksModel=&$this->getModel('Tasks','dbconnectModel');
            $tasksModel->updateTaskKbiSource($task->id,$kbiSource);
            $task->kbi_source=$kbiSource;
          }else{
            $kbiModel->updateLMSource_minerId($task->kbi_source,$lispminerId);
          }
        }else{
          //TODO odstraneni lispmineru, hláška pro uživatele
        }
        // 
      }else{
        //TODO nepodařilo se zaregistrovat LM, hláška pro uživatele
      }  
    }    
    return $task->kbi_source;
  }
  
  
  /**
	 * Custom Constructor
	 */
	public function __construct( $default = array())
	{                                        
		parent::__construct( $default );
		$this->document =& JFactory::getDocument();
	}

  /**Akce pro vytvoření/úpravu úlohy********************************************/
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
    //if ((@$connection->uid!=$userId)&&($adminMode!='ok')){
    if (@$connection->uid!=$userId){
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
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
        $tasksModel->updateBasicTask($task->id,$_POST['name'],$connection->id,$columnsXml,true);
      }else{  
        $taskId=$tasksModel->insertBasicTask($_POST['name'],$connection->id,$columnsXml,true);
      }
      
      //redirect na vypis uloh
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$taskId.'&generatePreprocessing='.JRequest::getString('generatePreprocessing',''),false);
    }else{                    
      //máme zobrazit výběr sloupců
      $view=&$this->getView('IziQuickDMTask',$this->document->getType());
      
      //nastavíme uniDB model
      $unidbModel=&$this->getModel('Unidb','dbconnectModel');    
      $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->password,$connection->db_name);
      if ($dbError!=''){        
        JError::raiseError(500,$dbError);
        return ;                                
      }                                          
      //priradime connection do view
      $view->assignRef('connection',$connection); 
      if (@$task){
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
    $generatorModel=&$this->getModel('Generator','dbconnectModel'); //TODO chyba v modelu generator
                           
               
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return;
    }         
    $user=& JFactory::getUser();
    $connection=$connectionsModel->getConnection($task->db_table);
    if (!$connection){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }              
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');
    $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->password,$connection->db_name);
    if ($dbError!=''){
      JError::raiseError(500,$dbError);
      return ;
    }
    
          
    //potřebujeme vygenerovat BKEF a FML article
    $curDateStr=date(JText::_('DATETIME_FORMAT'));
                    
    if (!$generatorModel->processData(
           $unidbModel->getContentXML($task,true),
           $connection->table,
           (JRequest::getString('generatePreprocessing','')=='ok'),
           $task->fml_article,
           $task->bkef_article,
           $this->getModel('Data','dbconnectModel'),
           $this->getModel('Fml','dbconnectModel'),
           $this->getModel('Bkef','dbconnectModel') 
         )
        ){           
      //TODO show error
      //exit('BKEF OR FML GENERATION FAILED!');
      /////return;
    }                     
    //máme vygenerováno, tak jdeme ukládat
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkefArticleTitle=$connection->table.' - BKEF ('.$curDateStr.')';
    $fmlArticleTitle=$connection->table.' - FML ('.$curDateStr.')';
    $bkefXML=$generatorModel->getBkefXML();
        
    if ($bkefXML){
      if ($task->bkef_article>0){
        //update
        $bkefArticleId=$task->bkef_article;
        $dataModel->saveArticle($bkefArticleId,$bkefXML);
      }else{   
        //new article
        $bkefArticleId=$dataModel->newArticle($bkefArticleTitle,$bkefXML);
      }
    }
        
    $taskParams=array();
    if (@$bkefArticleId){
      $taskParams['bkef']=$bkefArticleId;
      $fmlXML=$generatorModel->getFmlXML($bkefArticleId,$bkefArticleTitle,$task->id,$task->name);
      if ($fmlXML){
        if ($task->fml_article>0){
          //update
          $fmlArticleId=$task->fml_article;
          $dataModel->saveArticle($fmlArticleId,$fmlXML);
        }else{
          $fmlArticleId=$dataModel->newArticle($fmlArticleTitle,$fmlXML);
        }
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
    
    if(@$_GET['newAttribute']!=''){
      //přesměrování v případě dovygenerování nového atributu
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.JRequest::getString('newAttribute','').'&kbi='.$task->kbi_source.'&tmpl=component',false);
    }else{
      $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=generatePMML&taskId='.$task->id.'&from=izi&tmpl=component',false);
    }    
  }     

  /**
   *  Akce pro vybrání preprocessing hintu k danému mapování a spuštění předzpracování
   */     
  public function selectPreprocessingHint(){//TODO !!!
    $maName=JRequest::getString('maName');
    $formatName=JRequest::getString('formatName');
    $pmmlName=JRequest::getString('pmmlName',JRequest::getString('col',''));
    $preprocessingName=JRequest::getString('preprocessingName');
    $taskId=JRequest::getInt('taskId');
                                                          
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }                                               
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $bkef=$dataModel->loadArticleXML($task->bkef_article);
    
    $bkefModel=&$this->getModel('Bkef','dbconnectModel');
    if (!$bkefModel->setBkef($bkef)){      exit('Soubor BKEF připojený k úloze není platný.');    //TODO
      //TODO vyřešit chybu - nejde o mapování PMML na BKEF!!!
    }
    
    $attributeName=JRequest::getString('attributeName','');
    if ($attributeName!=''){
      if ($bkefModel->preprocessingHintExists($maName,$formatName,$preprocessingName)){   
        //existuje zvolený preprocessing, nebo je zadání prázdné - přidáme ho do FML
        $fmlModel=&$this->getModel('Fml','dbconnectModel');
        $fml=$dataModel->loadArticleXML($task->fml_article);
        $fmlModel->setFml($fml);
        $fmlModel->setPreprocessingHint($pmmlName,$maName,$formatName,$preprocessingName,$attributeName); //TODO doplnit attribute name
                                        
        $dataModel->saveArticleXML($task->fml_article,$fmlModel->getFml());
        //TODO - přesměrování na částečné vygenerování PMML a doimport
        $this->_redirect=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=quickDMTask_generate&task_id='.$task->id,false);
        return;
      } 
      //nejspíš nějaká chyba - přesměrujeme uživatele zpátky na výběr preprocessing hintu     
      exit('ERROR - preprocessing hint not found');
      //$this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=showPreprocessingHints&taskId='.$taskId.'&maName='.urlencode($maName).'&formatName='.urlencode($formatName).'&pmmlName='.urlencode($pmmlName) ,false);
    }else{
      //je potřeba zobrazit view pro zadání názvu atributu
      $view=&$this->getView('IziNewAttributeName',$this->document->getType());
      $view->assign('maName',$maName);
      $view->assign('formatName',$formatName);
      $view->assign('pmmlName',$pmmlName);
      $view->assign('preprocessingName',$preprocessingName);
      $view->assign('taskId',$taskId);
      $view->display();
    }
  }           

  /**
   *  Funkce pro vyčištění formátu čísla zadaného uživatelem
   */     
  private function cleanNumber($number){
    return floatval(str_replace(array(' ',','),array('','.'),$number));
  }
  
  
  /**
   *  Akce pro zobrazení hodnot z konkrétního sloupce DB tabulky
   */     
  public function previewColumn(){     
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');    
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $kbiId=JRequest::getInt('kbi',-1);
    $columnName=JRequest::getString('col','');
    if ($taskId>0){
      $task=$tasksModel->getTask($taskId);
    }elseif($kbiId>0){
      $task=$tasksModel->getTaskByKbi($kbiId);
    }                            
    if (!$task){//TODO zobrazení chyby
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
      return ;
    }
    
    $connectionsModel= &$this->getModel('Connections', 'dbconnectModel');
    $connection=$connectionsModel->getConnection($task->db_table);
    
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');    
    $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->password,$connection->db_name);
    if ($dbError!=''){        
      JError::raiseError(500,$dbError);
      return ;                                
    }
    
    $order=JRequest::getVar('order','value');
    
    $values=$unidbModel->getColumnValuesPreview($connection->table,$columnName,$order);
    
    $view=&$this->getView('IziPreviewColumn',$this->document->getType());
    $view->assign('columnName',$columnName);
    $view->assign('kbiId',$kbiId);
    $view->assignRef('values',$values);
    $view->assign('order',$order);
    $view->assign('graphStyle',JRequest::getVar('graph'));
    $view->display();
  }
  
  /**
   *  Akce pro zobrazení hodnot vybraného atributu
   */
  public function previewAttribute(){
    //TODO
    $attributeName=JRequest::getVar('attribute','');
    $kbiId=JRequest::getInt('kbi','');
    
           
    try {
      require_once (JPATH_COMPONENT.DS.'../com_kbi/models/transformator.php');
      $config = array(                        //TODO - kde se použije ID kbi zdroje???
            			'source' => JRequest::getVar('kbi', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
            			'query' => JRequest::getVar('query', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
            			'xslt' => JRequest::getVar('xslt', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
            			'parameters' => JRequest::getVar('parameters', NULL, 'default', 'none', JREQUEST_ALLOWRAW)
            		);                          
			$model = new KbiModelTransformator($config);   
			$dataDescription=$model->getDataDescription(array('template'=>'LMDataSource.Matrix.ARD.DBConnect.Template.PMML'));
      $data=simplexml_load_string($dataDescription);
                 
      /*máme načtený XML dokument => získáme z něj příslušnou část (hodnoty zvoleného atributu)*/
      $dictionary=null;
      if (count($data->Dictionary)>0){
        foreach ($data->Dictionary as $dict){
        	if ($dict['sourceDictType']=='TransformationDictionary'){
            $dictionary=$dict;
            break;
          }
        }
      }
      $field=null;
      if (($dictionary)&&(count($dictionary->Field)>0)){
        foreach ($dictionary->Field as $fie){
        	if ((string)$fie->Name==$attributeName){
            $field=$fie;
            break;
          }
        }
      }
      //pokud máme konkrétní field, tak načteme seznam hodnot
      if ($field&&(count($field->Category)>0)){
        $categoriesArr=array();
        $order=1;
        foreach ($field->Category as $category){
        	$categoriesArr[]=array('order'=>$order,'name'=>(string)$category,'frequency'=>(integer)$category['frequency']);
          $order++;
        }
      }
      
      if (is_array($categoriesArr)){
        //výběr způsobu řazení a seřazení položek
        $order=JRequest::getVar('order','order/asc');
        $orderArr=explode('/',$order);
        if (!in_array($orderArr[0],array('order','name','frequency'))){
          $orderArr[0]='order';
        }
        if (@$orderArr[1]!='desc'){
          $orderArr[1]='asc';
          $order=$orderArr[0];
        }else{
          $order=$orderArr[0].'/desc';
        }
        
        usort($categoriesArr,self::buildDatadictionarySorter($orderArr[0],$orderArr[1]));
      }
      
		} catch (Exception $e) {
			var_dump($e);
      return;
		}
    
    $view=&$this->getView('IziPreviewAttribute',$this->document->getType());
    $view->assign('field',$field);
    $view->assign('kbiId',$kbiId);
    $view->assignRef('categoriesArr',$categoriesArr);
    $view->assign('order',$order);
    $view->assign('graphStyle',JRequest::getVar('graph'));
    $view->display();
  }     
  
  
  /**
   *  Funkce vracející implementaci řadící funkce pro usort, která je závislá na klíči vybraném pro řazení a směr řazení
   */     
  static function buildDatadictionarySorter($key,$order='asc'){
    return function ($a, $b) use ($key,$order) {
        $return =strnatcmp($a[$key], $b[$key]);
        if ($order=='desc'){
          $return*=-1;
        }
        return $return;
    };
  }
  
  
  /**
   *  Akce pro vyvolání úpravy preprocessing hintu
   */
  public function newReportArticle(){
    exit('not yet implemented');
  } 

  /**
   *  Akce pro vygenerování a zobrazení view s popisem chyby - aby nebylo nutné zobrazovat standartní hlášku joomly
   */     
  private function showErrorView($title,$text){      
    $view=&$this->getView('IziError',$this->document->getType());
    $view->assign('title',$title);
    $view->assign('text',$text);
    $view->display();
  }
  
  /**
   *  Akce pro zobrazení detailů připojení k databázi 
   */     
  public function showConnection(){             
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $connection=&$connectionsModel->getConnection(JRequest::getInt('connection_id',-1));
    if (!$connection){
      $this->showErrorView(JText::_('CONNECTION_NOT_FOUND'),JText::_('CONNECTION_NOT_FOUND_TEXT'));
    }
    $view=&$this->getView('IziShowConnection',$this->document->getType());
    $view->assign('connection',$connection);
    $view->display();
  }
  
  
  /**
   *  Akce pro zobrazení detailů připojení k databázi 
   */     
  public function showTask(){    
    $tasksModel= & $this->getModel('Tasks', 'dbconnectModel');
    $task=&$tasksModel->getTask(JRequest::getInt('task_id',-1));
    if (!$task){
      $this->showErrorView(JText::_('TASK_NOT_FOUND'),JText::_('TASK_NOT_FOUND_TEXT'));
    }
    $view=&$this->getView('IziShowTask',$this->document->getType());
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    $connection=$connectionsModel->getConnection($task->db_table);
    $view->assign('connection',$connectionsModel->getConnection($task->db_table));
    
    $view->assign('task',$task);
    $view->display();
  }  
  
  /**
   *  Akce pro zadání nového připojení k databázi
   */
  public function newDatabase(){
    $connectionsModel= & $this->getModel('Connections', 'dbconnectModel');
    //zjistime, jestli mame ukladat data, nebo jen zobrazit formular
    if ((JRequest::getString('save','')=='connection')&&(JRequest::getString('step','')=='3')){
      //uložíme záznam    

      $connectionId=$connectionsModel->insertConnection($_POST['db_type'],$_POST['db_server'],$_POST['db_username'],$_POST['db_password'],$_POST['db_database'],$_POST['db_table'],$_POST['db_primary_key'],$_POST['db_shared_connection']);      
      //TODO - je potřeba dodělat kontrolu, jestli se má pokračovat s připojením...!!!
      if (JRequest::getString('quickDMTask')=='ok'){
        $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=quickDMTask&connection_id='.$connectionId,false);
      }else{
        $this->_redirect=JRoute::_('index.php?option=com_dbconnect&task=listConnections',false);
      }      
    }else{
      $view = &$this->getView('IziNewDatabase',$this->document->getType());
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
}
?>

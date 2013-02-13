<?php
jimport( 'joomla.application.component.controller' );

/**
 *  Controller pro zpřístupnění funkcí aplikace prostřednictvím GET/POST požadavků 
 */  
class ApiController extends JController{
  var $document;
  private $loggedIn;


  /**
   *  Akce pro jednoduchý import dat - parametry přebírá z $_POST
   */     
  public function quickImport(){
    try{
    ////////////////
      if (@$_POST['task_id']>0){
        //máme taskId, jde jenom o import PMML - výsledek necháme rovnou vypsat
          $this->importPMML(false);
          return;
        //--máme taskId, jde jenom o import PMML    
      }
      $result=array();
      
      if (!(@$_POST['connection_id'])){
        $connectionResult=$this->newConnection(true);
        if ($connectionResult['status']=='ok'){
          $_POST['connection_id']=$connectionResult['connection_id'];
          $result['connection_id']=$connectionResult['connection_id'];
        }
      }
      
      if (!(@$_POST['task_id'])){
        $taskResult=$this->newTask(true);
        if ($taskResult['status']=='ok'){
          $_POST['task_id']=$taskResult['task_id'];
          $result['task_id']=$taskResult['task_id'];
        }
      }
      
      
      if(@$_POST['task_id']){
        //import PMML
        $importResult=$this->importPMML(true);
        $result['status']=$importResult['status'];
        if (@$importResult['kbi_source']>0){
          $result['kbi_source']=$importResult['kbi_source'];
          $result['miner_id']=@$importResult['miner_id'];
        }
        //--máme connectionId => je potřeba zaregistrovat task,vytvořit KBI source a provést import
      }
       
    ////////////////  
    }catch (Exception $e){
      echo json_encode(array('status'=>'error','message'=>$e->getMessage()));
    }
    
  }

  /**
   *  Akce pro vytvoření KBI zdroje a naimportování zaslaného PMML
   */     
  public function importPMML($returnResult=false){
    $taskId=@$_POST['task_id'];
    $pmmlData=trim(@$_POST['pmml']);
    $minerUrl=trim(@$_POST['miner_url']);
    
    //přihlášení uživatele
    $this->loginUser();
    
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTask($taskId);
    if (!$task){
      $result=array('status'=>'error','message'=>'Task for pmml import does not exist!');
      if ($returnResult){
        return $result;
      }else{
        echo json_encode($result);
        return;
      }
    }
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connection=$connectionsModel->getConnection($task->db_table);
    
    try{
      if ($minerUrl){
        $kbiResult=$this->generateKbiSource(null,$connection,$task,$pmmlData,$minerUrl);
      }else{
        $kbiResult=$this->generateKbiSource(null,$connection,$task,$pmmlData);
      }
    }catch(Exception $e){
      $result=array('status'=>'error'/*,'kbi_source'=>@$kbiResult['kbi_source'],'miner_id'=>@$kbiResult['miner_id']*/,'message'=>$e->getMessage());
    }
    
    if (!$result){
      $result=array('status'=>'ok','kbi_source'=>@$kbiResult['kbi_source'],'miner_id'=>@$kbiResult['miner_id']);
    }
    if ($returnResult){
      return $result;
    }else{
      echo json_encode($result);
    }
  }
  
  /**
   *  Akce pro zaregistrování nového tasku na základě zadaného připojení k databázi
   */     
  public function newTask($returnResult=false){
    $connectionId=@$_POST['connection_id'];
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connection=$connectionsModel->getConnection($connectionId);       
    //přihlášení uživatele
    $this->loginUser();
    $user =& JFactory::getUser();
    
    if (!((@$connection->uid==$user->get('id'))||(@$connection->shared))){
      $result=array('status'=>'error','message'=>'Connection access denied!');
      if ($returnResult){
        return $result;  
      }else{
        echo json_encode($result);
        return;
      }
    }
    
    $taskName=@$_POST['task_name'];
    if ($taskName==''){
      $taskName='APIimport '.date('Y-m-d H:i:s');
    }

    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    if ($taskId=$tasksModel->insertBasicTask($taskName,$connectionId,'<columns></columns>',false)){
      $result=array('status'=>'ok','task_id'=>$taskId);
      $_POST['task_id']=$taskId;
    }else{
      $result=array('status'=>'error','message'=>'Task creation failed!');
    }
    if ($returnResult){
      return $result;
    }else{
      echo json_encode($result);
      return;
    }
  }
  

  /**
   *  Akce pro zaregistrování nového připojení k databázi prostřednictvím dat zaslaných metodou POST
   */     
  public function newConnection($returnResult=false){
    $dbType=JRequest::getString('db_type','mysql');
    $dbServer=@$_POST['db_server'];
    $dbUsername=@$_POST['db_username'];
    $dbPassword=@$_POST['db_password'];
    $dbName=@$_POST['db_name'];
    $dbTable=@$_POST['db_table'];
    $dbPrimaryKey=@$_POST['db_primary_key'];
    $dbShared=(JRequest::getInt('shared',0)==1);
    
    if (($dbServer=='')||($dbName=='')||($dbTable=='')){
      $output=array('status'=>'error','message'=>'It is necessary to send db name, db server and table name!');
      if($returnResult){
        return $output;
      }else{
        echo json_encode($output);
        return; 
      }
    }
    
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');    
    $dbError=$unidbModel->setDB($dbType,$dbServer,$dbUsername,$dbPassword,$dbName);
    if ($dbError!=''){        
      $output=array('status'=>'error','message'=>'DB connection failed!');
      if($returnResult){
        return $output;
      }else{
        echo json_encode($output);
        return; 
      }                          
    }
    
    $rowsCount=$unidbModel->getTableRowsCount($dbTable);
    if (!$rowsCount){
      $output=array('status'=>'error','message'=>'Defined table has no rows or does not exist.');
      if($returnResult){
        return $output;
      }else{
        echo json_encode($output);
        return; 
      }
    }
    
    //přihlášení uživatele
    $this->loginUser();
    //uložení připojení
    $connectionsModel=&$this->getModel('Connections','dbconnectModel');
    $connectionId=$connectionsModel->insertConnection($dbType,$dbServer,$dbUsername,$dbPassword,$dbName,$dbTable,$dbPrimaryKey,$dbShared);
    if ($connectionId>0){
      $output=array('status'=>'ok','connection_id'=>$connectionId);
      $_POST['connection_id']=$connectionId;
    }else{
      $output=array('status'=>'error','message'=>'Connection saving failed.');
    }
    if($returnResult){
      return $output;
    }else{
      echo json_encode($output);
      return; 
    }
  }
  
  /**
   *  Akce pro přihlášení uživatele prostřednictvím zaslaných dat v POSTu
   */     
  public function loginUser(){
    if ($this->loggedIn){return;}
    $username=@$_POST['username'];
    $password=@$_POST['password'];
    if ($username!=''){
      $application = JFactory::getApplication();
      $application->login(array('username'=>$username,'password'=>$password),array('silent'=>true));
    }
    $this->loggedIn=true;
  }
  
  /**
   *  Metoda pro vytvoření KBI Source a import zadání úlohy v PMML
   */                                                                                    //TODO výběr minerUrl
  private function generateKbiSource($configArr,$connection,$task,$pmml='',$minerUrl='http://connect.lmcloud.vse.cz'){   
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
                                        'password'=>$connection->password,
                                        'type'=>$dbType
                                      ));                   
        }catch(Exception $e){
          throw $e;
        }
        //--máme zaregistrovat nový lisp miner
      }else{                   
        //miner už existuje
        $kbiSource=$kbiModel->getSource($task->kbi_source);  
        $kbiSourceParams=json_decode($kbiSource->params,true);
        $lispminerId=$kbiSourceParams['miner_id'];        
      }
      
      //TODO - pokud nemáme zadaný PMML soubor, musíme vytvořit aspoň zadání primárního klíče
                  
      if ($lispminerId){      
        //máme zaregistrovaný LM server - vytvorime KBI zdroj
        ////
        try{
          $importResult=$kbi->importDataDictionary($pmml,$lispminerId);//TODO kontrola $importResult !!
        }catch(Exception $e){
          throw $e;
        }
        
        if ($importResult){
          //máme úspěšně naimportováno
          if ($task->kbi_source<=0){ 
            $kbiSource=$kbiModel->newLMSource($task->name,$minerUrl,$lispminerId,$connection->table);
            $tasksModel=&$this->getModel('Tasks','dbconnectModel');
            $tasksModel->updateTaskKbiSource($task->id,$kbiSource);
            $task->kbi_source=$kbiSource;
          }/*else{       //TODO pokud miner neexistoval, je potřeba aktualizovat info
            ///$kbiModel->updateLMSource_minerId($task->kbi_source,$lispminerId);
          }  */
        }
        // 
      }  
    /*-----------//registrace LM a import----------*/    
    return array('kbi_source'=>$task->kbi_source,'miner_id'=>$lispminerId);
  }


  /**
   *  Konstruktor
   */     
  public function __construct( $default = array()){                                        
		parent::__construct( $default );
		$this->document=&JFactory::getDocument();
    $this->loggedIn=false;
	}

}
?>

<?php
jimport( 'joomla.application.component.controller' );

/**
 *  Controller pro zpřístupnění funkcí aplikace prostřednictvím GET/POST požadavků 
 */  
class DataController extends JController{
  var $document;
  const DEFAULT_IZI_EXPORT_TEMPLATE='4ftMiner.Task.Template.PMML';
  const PMML_SECTION_ID=0;

  /**
   *  Akce pro stažení PMML dat a jejich uložení v podobě článku
   */     
  public function savePMMLArticle(){      
    $kbiId=JRequest::getInt('kbi',-1);
    $lmtaskId=JRequest::getVar('lmtask','');
    $articleId=JRequest::getInt('articleId',-1);
    $template=JRequest::getVar('template',self::DEFAULT_IZI_EXPORT_TEMPLATE);
    //TODO rules - získání jejich seznamu a uložení ke článku
                                        
    try {          /*
      require_once (JPATH_COMPONENT.DS.'../com_kbi/models/transformator.php');
      $config = array(
            			'source' => JRequest::getVar('kbi', null, 'default', 'none', JREQUEST_ALLOWRAW),
            			'query' => JRequest::getVar('query', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
            			'xslt' => JRequest::getVar('xslt', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
            			'parameters' => JRequest::getVar('parameters', NULL, 'default', 'none', JREQUEST_ALLOWRAW)
            		);                          
			$model = new KbiModelTransformator($config);
      $source=$model->getSource(); */
      $source=$this->getKbiSource($kbiId);         
      
      $options=array('export'=>$lmtaskId,'template'=>$template);
      $result=$source->queryPost(null,$options);      
      if((!strpos($result,'<response status="failure">'))&&(strpos($result,'<PMML'))){
        //máme vyexportovaný PMML soubor => uložíme ho do článku 
        /*uložení článku*/
        $userId=JRequest::getInt('user',-1);
        if (!($userId>=0)){
          $user =& JFactory::getUser();
          $userId=$user->get('id');    
        }
        $sectionId=JRequest::getInt('sectionId',self::PMML_SECTION_ID);
        $title=JRequest::getString('title','');
        if ($title==''){
          $title='PMML '.date('r');
        }                                                   
        $dataModel=&$this->getModel('Data','dbconnectModel');
        $articleId=$dataModel->saveArticle(0,$title,$result,$sectionId,$userId);
        if ($articleId){
          $tasksModel=&$this->getModel('Tasks','dbconnectModel');
          $task=$tasksModel->getTaskByKbi($kbiId);
          if ($task){
            $dataModel->saveTaskArticle($task->id,$articleId,'pmml');
          }
          $this->outputJSON(array('result'=>'ok','article'=>$articleId));
          return;
        }
        /*--uložení článku*/
        
      }else{
        $xml=simplexml_load_string($result);
        if (isset($result->message)){
          $errorMessage=(string)$result->message;
        }else{
          $errorMessage=JText::_('PMML_EXPORT_FORMAT_ERROR');
        }
      }
    }catch (Exception $e) {
		  $errorMessage=$e->getMessage();	
	  }
    $this->outputJSON(array('result'=>'error','message'=>$errorMessage));
  }
  
  
  
  /**
   *  Akce pro získání seznamu článků s uživatelskými zprávami 
   */
  public function listKBIArticles(){        
    $kbiId=JRequest::getInt('kbi',-1);
    if ($kbiId<0){//TODO kontrola, jestli má uživatel přístup k danému KBI zdroji
      $this->outputJSON(array('result'=>'error','message'=>JText::_('KBI_NOT_DEFINED'),'kbi'=>$kbiId));
      return;
    }
                        
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTaskByKbi($kbiId);
    if (!$task){
      $this->outputJSON(array('result'=>'error','message'=>'DMTASK_NOT_ACCESSIBLE','kbi'=>$kbiId));
      return;
    }         
    //máme načtenou úlohu, tak se pokusíme načíst jednotlivé články
    $dataModel=&$this->getModel('Data','dbconnectModel');     
    $articles=$dataModel->getArticlesList($task->id,'report'); 
    $articlesArr=array();
    if (count($articles)>0){  
      foreach ($articles as $article){
      	$articlesArr[$article->id]=$article->title;
      }
    }
    $this->outputJSON(array('result'=>'ok','kbi'=>$kbiId,'articles'=>$articlesArr));
  }      

  /**
   *  Akce pro zobrazení konkrétního článku v Joomle - na základě zadaného IDčka
   */
  public function showArticle(){
    $articleId=JRequest::getInt('id',-1);
    if ($articleId>0){
      $this->setRedirect(JRoute::_('index.php?view=article&id='.$articleId));
    }else{
      $this->setRedirect(JRoute::_('index.php'));
    }
  }      

  /**
   *  Akce pro vytvoření nového článku
   */
  public function createArticle(){
    $userId=JRequest::getInt('user',-1);
    if (!($userId>=0)){
      $user =& JFactory::getUser();
      $userId=$user->get('id');    
    }
    $sectionId=JRequest::getInt('sectionId',0);
    $title=JRequest::getString('title','');
    if ($title==''){
      $title=date('r');
    }
    
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $articleId=$dataModel->saveArticle(0,$title,@$_POST['data'],$sectionId,$userId);
    if ($articleId){
      
      $kbiId=JRequest::getInt('kbi',0);
      if ($kbiId>0){
        $type=JRequest::getVar('type','report');
        $tasksModel=&$this->getModel('Tasks','dbconnectModel');
        $task=$tasksModel->getTaskByKbi($kbiId);
        if ($task){
          $dataModel->saveTaskArticle($task->id,$articleId,$type);
        }
      }
      
      if (JRequest::getVar('return','')=='json'){
        $this->outputJSON(array('result'=>'ok','article_id'=>$articleId));
      }else{
        //článek existuje
        $this->setRedirect(JRoute::_('index.php?view=article&id='.$articleId.'&task=edit'),JText::_('ARTICLE_CREATED'));
      }
    }else{
      if (JRequest::getVar('return','')=='json'){
        $this->outputJSON(array('result'=>'error','message'=>'Error while saving of article'));
      }else{
        $this->setRedirect(JRoute::_('index.php'));
      }
    } 
  }      

  /**
   *  Akce pro jednoduchý import dat - parametry přebírá z $_POST
   */     
  public function saveMinerData(){      
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $userId=JRequest::getInt('user',-1);
    if (!($userId>=0)){
      $user =& JFactory::getUser();
      $userId=$user->get('id');    
    }
    $type=JRequest::getVar('type','');
    if (!in_array($type,$dataModel->getMinerDataTypes())){
      $type='';
    }
    
    $kbiId=JRequest::getInt('kbi',-1);
    if ($kbiId<0){//TODO kontrola, jestli má uživatel přístup k danému KBI zdroji
      $this->outputJSON(array('result'=>'error','message'=>'KBI not defined!','kbi'=>$kbiId,'user'=>$userId,'type'=>$type));
    }
    
    $data=@$_POST['data'];
    
    if ($dataModel->saveMinerData($kbiId,$userId,$type,$data)){
      $this->outputJSON(array('result'=>'ok','kbi'=>$kbiId,'user'=>$userId,'type'=>$type));
    }else{
      $this->outputJSON(array('result'=>'error','message'=>'Error while saving.','kbi'=>$kbiId,'user'=>$userId,'type'=>$type));
    }                 
  }

  /**
   *  Akce pro načtení dat
   */
  public function loadMinerData(){        
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $userId=JRequest::getInt('user',-1);
    
    if (!($userId>=0)){
      $user =& JFactory::getUser();
      $userId=$user->get('id');    
    }
    $type=JRequest::getVar('type','');
    if (!in_array($type,$dataModel->getMinerDataTypes())){
      $type='';
    }
                                            
    $kbiId=JRequest::getInt('kbi',-1);  
    if ($kbiId<0){//TODO kontrola, jestli má uživatel přístup k danému KBI zdroji
      $this->outputJSON(array('result'=>'error','message'=>'KBI not defined!','kbi'=>$kbiId,'user'=>$userId,'type'=>$type));
    }                                 
                                           
    if ($data=$dataModel->loadMinerData($kbiId,$userId,$type)){
      if (JRequest::getVar('decode','')=='json'){
        $data=json_decode($data);
        $this->outputJSON(array('result'=>'ok','kbi'=>$kbiId,'user'=>$userId,'type'=>$type,'data'=>$data));
      }
    }else{
      $this->outputJSON(array('result'=>'error','message'=>'Data not found.','kbi'=>$kbiId,'user'=>$userId,'type'=>$type));
    }                                     
  }      
  
  /**
   *  Akce vracející JSON přehled atributů, které existují ve vybraném LM
   */        
  public function getExistingAttributes(){   
    $kbiId=JRequest::getInt('kbi',-1);  
    try{  
      $kbiModel=$this->getKbiModel($kbiId);   
      $xmlStr=$kbiModel->getDataDescription(array('template'=>'LMDataSource.Matrix.ARD.Attributes.Template.XML'));
      $xml=simplexml_load_string($xmlStr);
      $attributesArr=array();
      if (($xml)&&(count($xml->Attribute)>0)){
        foreach ($xml->Attribute as $attribute){
          $attributesArr[]=(string)$attribute;	
        }
        $this->outputJSON(array('result'=>'ok','attributes'=>$attributesArr));
        return;
      }
    }catch (Exception $e) {       
		  $errorMessage=$e->getMessage();	
	  }
    $this->outputJSON(array('result'=>'error','message'=>$errorMessage));
  }
  
  
  /**
   *  Funkce pro vypsání výstupu ve formátu JSON
   */     
  private function outputJSON($data){
    echo json_encode($data);
  }


  /**
   *  Konstruktor
   */     
  public function __construct( $default = array()){                                        
		parent::__construct( $default );
		$this->document =& JFactory::getDocument();
	}


  /**
   *  Funkce pro získání instance KBI modelu s konkrétním zdrojem
   */     
  private function getKbiModel($kbiId){
    require_once (JPATH_COMPONENT.DS.'../com_kbi/models/transformator.php');
    $config = array(
          			'source' => $kbiId,
          			'query' => JRequest::getVar('query', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
          			'xslt' => JRequest::getVar('xslt', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
          			'parameters' => JRequest::getVar('parameters', NULL, 'default', 'none', JREQUEST_ALLOWRAW)
          		);                          
		return new KbiModelTransformator($config);
  }
  
  /**
   *  Funkce pro získání instance konkrétního KBI zdroje
   */
  private function getKbiSource($kbiId){
    $model=$this->getKbiModel($kbiId);
    return $model->getSource();
  }     
}
?>

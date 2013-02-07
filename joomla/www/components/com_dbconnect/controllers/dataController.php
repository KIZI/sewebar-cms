<?php
jimport( 'joomla.application.component.controller' );

/**
 *  Controller pro zpřístupnění funkcí aplikace prostřednictvím GET/POST požadavků 
 */  
class DataController extends JController{
  var $document;

  /**
   *  Akce pro stažení PMML dat a jejich uložení v podobě článku
   */     
  public function savePMMLArticle(){
    $this->outputJSON(array('result'=>'error','message'=>'Not finished!'));
    //TODO - dodělat stažení z KBI a uložení
  }
  
  
  
  /**
   *  Akce pro získání seznamu článků s uživatelskými zprávami 
   */
  public function listKBIArticles(){
    $kbiId=JRequest::getInt('kbi',-1);
    if ($kbiId<0){//TODO kontrola, jestli má uživatel přístup k danému KBI zdroji
      $this->outputJSON(array('result'=>'error','message'=>'KBI not defined!','kbi'=>$kbiId));
      return;
    }
    
    $this->outputJSON(array('result'=>'ok','kbi'=>$kbiId,'articles'=>array()));
    //TODO !!!
  }      

  /**
   *  Akce pro zobrazení konkrétního článku v Joomle - na základě zadaného IDčka
   */
  public function showArticle(){
    $articleId=JRequest::getInt('id',-1);
    if ($articleId>0){
      $this->setRedirect(JRoute::_('index.php?view=article&id='.$articleId));
    }else{
      $this->setRedirect(JRoute::_('index.php');
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
        //TODO je potřeba doplnit vazbu na KBI zdroj
        
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

}
?>

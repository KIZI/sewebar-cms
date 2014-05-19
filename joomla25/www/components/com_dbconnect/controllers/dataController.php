<?php
jimport( 'joomla.application.component.controller' );

/**
 *  Controller pro zpřístupnění funkcí aplikace prostřednictvím GET/POST požadavků 
 */  
class DataController extends JController{
  var $document;

  /**
   * Akce pro smazání článku
   */
  public function deleteArticle(){
    $articleId=JRequest::getInt('articleId',0);
    /** @var $dataModel dbconnectModelData */
    $dataModel=&$this->getModel('Data','dbconnectModel');
    if ($dataModel->deleteArticle($articleId)){
      $dataModel->deleteTaskArticle($articleId);
      $this->outputJSON(array('result'=>'ok'));
    }else{
      $this->outputJSON(array('result'=>'error'));
    }
  }

  /**
   *  Akce pro stažení PMML dat a jejich uložení v podobě článku
   */     
  public function savePMMLArticle(){
    /** @var $dataModel dbconnectModelConfig */
    $configModel=&$this->getModel('Config','dbconnectModel');
    $pmmlArticlesCategory=$configModel->loadConfig('new_pmml_category');
    $lispminerExportTemplate=$configModel->loadConfig('lispminer_export_template');

    $kbiId=JRequest::getInt('kbi',-1);
    $lmtaskId=JRequest::getVar('lmtask','');
    $articleId=JRequest::getInt('articleId',JRequest::getInt('article',0));
    $template=JRequest::getVar('template',$lispminerExportTemplate);
    $rules=JRequest::getString('rules',JRequest::getString('rulesIds',""));

    /*pripraveni XML kodu pro zaznamenani vybranych asociacnich pravidel*/
    $selectedRulesXml="";
    if (!empty($rules)){
      if ($rulesEncoded=json_decode($rules)){
        $rules=$rulesEncoded;
      }
      if (!is_array($rules)&&(strpos($rules,','))){
        $rules=explode(',',$rules);
      }
      if (is_int($rules)){
        $rules=array(0=>$rules);
      }
      if (is_array($rules)&&(count($rules)>0)){
        foreach ($rules as $ruleId){
          $selectedRulesXml.='<AssociationRule id="'.$ruleId.'" />';
        }
      }
    }

    if ($selectedRulesXml!=""){
      $selectedRulesXml='<Extension name="selectedAssociationRules">'.$selectedRulesXml.'</Extension>';
    }
    /*pripraveni XML kodu pro zaznamenani vybranych asociacnich pravidel*/
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
      //přiřazení uživatele ze session
      /*$session =& JFactory::getSession();
      $userData=$session->get('user',array(),'sewebar');
      if (!empty($userData)){
        $source->setUser($userData);
      }*/
      //--přiřazení uživatele ze session
      $options=array('export'=>$lmtaskId,'template'=>$template);
      $result=$source->queryPost(null,$options);

      //exit(var_dump($result));      
      if((!strpos($result,'<response status="failure">'))&&(strpos($result,'<PMML'))){
        //máme vyexportovaný PMML soubor => uložíme ho do článku
        /*pripojeni informaci o vybranych pravidlech*/
        $result=str_replace('</guha:AssociationModel>',$selectedRulesXml.'</guha:AssociationModel>',$result);
        /*--pripojeni informaci o vybranych pravidlech*/

        /*uložení článku*/
        $userId=JRequest::getInt('user',-1);
        if (!($userId>=0)){
          $user =& JFactory::getUser();
          $userId=$user->get('id');    
        }
        /** @var $dataModel dbconnectModelConfig */
        $configModel=&$this->getModel('Config','dbconnectModel');
        if ($pmmlArticlesCategory>0){
          $reportArticlesCategory=$pmmlArticlesCategory;
        }else{
          $reportArticlesCategory=$configModel->loadConfig('new_report_category');
        }

        $title=JRequest::getString('title',JRequest::getString('taskName',''));
        if ($title==''){
          $title='PMML '.date('r');
        }

        /** @var $dataModel dbconnectModelData */
        $dataModel=&$this->getModel('Data','dbconnectModel');
        $articleId=$dataModel->saveArticle($articleId,$title,$result,$reportArticlesCategory,$userId,'delete');
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
   * Funkce pro označení vybraných asociačních pravidel v rámci PMML (na základě seznamu jejich IDček)
   * @param $domDocument
   * @param $idsArr
   */
  private function markSelectedRules(&$domDocument,$idsArr){
    if (!is_array($idsArr)){
      $idsArr=array(0=>$idsArr);
    }
    if (count($idsArr)>0){
      $xpath = new DOMXpath($domDocument);
      foreach($idsArr as $id){
        //označíme jednotlivá pravidla
        /** @var $elements DomNode[] */
        $xpath->registerNamespace('guha','http://keg.vse.cz/ns/GUHA0.1rev1');
        $elements = $xpath->query("/guha:AssociationModel/AssociationRule[@id=".$id."]");//TODO možná sem přidat přesnou cestu
        $elements = $xpath->query("//AssociationRule[@id=".$id."]"); //další varianta ||@id=".$id." or @id=".$id."||

        if (!empty($elements)){
          foreach ($elements as $element) {
            //projdeme potomky a zkusíme najít FourFtTable
            $nodeInsertBefore=null;
            if ($element->hasChildNodes){
              /** @var $childNodes DomNode[] */
              $childNodes=$element->childNodes;
              foreach ($childNodes as $childNode){
                if ($childNode->name=='FourFtTable'){
                  $nodeInsertBefore=$childNode;
                  break;
                }
              }
            }

            $annotationNode=$domDocument->createElement('Annotation');
            $annotationNode->appendChild($domDocument->createElement('RuleAnnotation','interesting'));

            if (!is_null($nodeInsertBefore)){
              $nodeInsertBefore->insertBefore($annotationNode);
            }else{
              $element->appendChild($annotationNode);
            }
          }
        }
      }
    }
  }

  /**
   *  Akce pro export pravidel pro BR server
   */     
  public function exportBR(){
    /** @var $dataModel dbconnectModelConfig */
    $configModel=&$this->getModel('Config','dbconnectModel');
    //$pmmlArticlesCategory=$configModel->loadConfig('new_pmml_category');
    $lispminerExportTemplate=$configModel->loadConfig('lispminer_export_template');

    $kbiId=JRequest::getInt('kbi',-1);
    $lmtaskId=JRequest::getVar('lmtask','');
    $articleId=JRequest::getInt('articleId',-1);
    $template=JRequest::getVar('template',$lispminerExportTemplate);

    if (JRequest::getVar('show','')!='ok'){
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&task=exportBR&show=ok&tmpl=component&kbi='.$kbiId.'&lmtask='.$lmtaskId.'&rules='.JRequest::getString('rules'),false));
    }

    try{
      $source=$this->getKbiSource($kbiId);         
      
      $options=array('export'=>$lmtaskId,'template'=>$template);
      $result=$source->queryPost(null,$options);
      //exit(var_dump($result));

      if((!strpos($result,'<response status="failure">'))&&(strpos($result,'<PMML'))){
        //máme k dispozicii PMML dokument - doplníme označení pravidel a následně spustíme transformace

        $pmmlXml=new DOMDocument();
        $pmmlXml->loadXML($result);

        //zpracování IDček a jejich označení v PMMLku
        $selectedRulesIds=JRequest::getString('rules',JRequest::getString('rulesIds'));
        if (!($rulesIdsArr=json_decode($selectedRulesIds))){
          $rulesIdsArr=explode(',',$selectedRulesIds);
        }
        if (empty($rulesIdsArr)){
          JError::raiseError(500,'No selected rules!');
        }
        $this->markSelectedRules($pmmlXml,$rulesIdsArr);

        $pmml2arXslt=new DOMDocument();
        $pmml2arXslt->load(JPATH_SITE.'/media/com_dbconnect/xml/pmml2ar_demo.xslt');

        $proc = new XSLTProcessor();
        $proc->importStyleSheet($pmml2arXslt);
        $arXml=$proc->transformToDoc($pmmlXml);

        

        $ar2drlXslt=new DOMDocument();
        $ar2drlXslt->load(JPATH_SITE.'/media/com_dbconnect/xml/ar2drl_demo.xslt');

        $proc2 = new XSLTProcessor();
        $proc2->importStylesheet($ar2drlXslt);

        $drlRulesXml=$proc2->transformToDoc($arXml);

        $drlRulesXml=simplexml_import_dom($drlRulesXml);
        //--máme k dispozicii PMML dokument - doplníme označení pravidel a následně spustíme transformace

        //zobrazíme view s přehledem
        $view=&$this->getView('DataExportBR','html');
        $view->assignRef('drlXml',$drlRulesXml);
        $view->display();
      }
    }catch (Exception $e){
      JError::raiseError(500, 'EXPORT FAILED, PLEASE TRY IT AGAIN LATER...');
    }
  }
  
  
  /**
   *  Akce pro získání seznamu článků s uživatelskými zprávami 
   */
  public function listKBIArticles(){
    //var_dump($_REQUEST['kbi']);
    $kbiId=JRequest::getInt('kbi',-1);
    if ($kbiId<0){//TODO kontrola, jestli má uživatel přístup k danému KBI zdroji
      $this->outputJSON(array('result'=>'error','message'=>JText::_('KBI_NOT_DEFINED'),'kbi'=>$kbiId));
      return;
    }

    /** @var $tasksModel dbconnectModelTasks */
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
    if (count($articles)>0){                                                /**/
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
    $articleId=JRequest::getInt('id',JRequest::getInt('article',-1));
    if ($articleId>0){
      //$this->setRedirect(JRoute::_('index.php?view=article&id='.$articleId));
      $this->setRedirect(('index.php?view=article&id='.$articleId));
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
    $sectionId=JRequest::getInt('sectionId',2);//TODO id výchozí kategorie
    $title=JRequest::getString('title','');
    if ($title==''){
      $title=date('r');
    }

    /** @var $dataModel dbconnectModelData */
    $dataModel=&$this->getModel('Data','dbconnectModel');
    $articleId=$dataModel->newArticle($title,@$_POST['data'],$sectionId,$userId);//TODO zkontrolovat
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
      }else{
        echo $data;
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
      if (($xml instanceof SimpleXMLElement)&&($xml->getName()=='Attributes')&&(($xml->count()==0)||(count($xml->Attribute)>0))){
        $attributesArr=array();
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
   * Akce pro zobrazení jednoduché infostránky
   */
  public function showInfo(){
    //zobrazíme view s přehledem
    $view=&$this->getView('ShowInfo','html');
    $view->assign('message',JText::_(JRequest::getString('message')));
    $view->display();
  }
  
  /**
   *  Akce vracející JSON přehled atributů, které existují ve vybraném LM
   */        
  public function getExistingTasks(){       
    $user =& JFactory::getUser();
    $userId=$user->id;
    if (($user->guest)&&(JRequest::getVar('ignoreAnonymous','')=='ok')){
      $this->outputJSON(array('result'=>'ok','tasks'=>array()));
      return;
    }
                                                    
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $tasks=$tasksModel->getTasks();
    $namesArr=array();
    if ($tasks&&(count($tasks)>0)){
      foreach ($tasks as $task){
      	$namesArr[]=$task->name;
      }
    }
     
    $this->outputJSON(array('result'=>'ok','tasks'=>$namesArr));
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
          			'query' => JRequest::getVar('query', null, 'default', 'none', JREQUEST_ALLOWRAW),
          			'xslt' => JRequest::getVar('xslt', null, 'default', 'none', JREQUEST_ALLOWRAW),
          			'parameters' => JRequest::getVar('parameters', null, 'default', 'none', JREQUEST_ALLOWRAW)
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

  public function modelTester(){
    //zobrazíme view s přehledem
    $view=&$this->getView('ModelTester','html');
    //$view->assignRef('drlXml',$drlRulesXml);
    $view->display();
  }

}
?>

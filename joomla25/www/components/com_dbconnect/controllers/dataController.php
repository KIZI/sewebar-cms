<?php
jimport( 'joomla.application.component.controller' );

/**
 *  Controller pro zpřístupnění funkcí aplikace prostřednictvím GET/POST požadavků 
 */  
class DataController extends JController{
  var $document;
  const RULES_XML_TEMPLATE='4ftMiner.Task.AssociationRules.Template.XML';//TODO
  const ATTRIBUTES_XML_TEMPLATE='LMDataSource.Matrix.ARD.DBConnectExtended.Template.PMML';//TODO
  const MODELTESTER_URL="http://br-dev.lmcloud.vse.cz:8080/DroolsModelTester_web/rest/association-rules/test-files";
  const MODELTESTER_XML_DIR='./components/com_dbconnect/tmp/rulesxml';
  const BRBASE_URL='http://brserver.golemsoftware.cz/www';

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
    $errorMessage='';
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
      $rules=$this->decodeRules($rules);
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
   * @return LispMiner
   */
  private function getKbiSource($kbiId){
    $model=$this->getKbiModel($kbiId);
    return $model->getSource();
  }

  public function modelTester(){
    //zobrazíme view s přehledem
    $kbi=JRequest::getString('kbi');
    $lmtask=JRequest::getString('lmtask');
    $rules=JRequest::getString('rules');

    //TODO check REQUEST items (kbi,lmtask,rules)
    $view=&$this->getView('DataModelTester','html');

    $testFile=JRequest::getString('file','');
    if ($testFile){

      $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
      $testFile=dbconnectModelUploads::DATA_DIR.'/'.$testFile;
      $view->assign('testFile',$testFile);

      if ($lmtask=='BRBASE'){
        $rulesExportUrl=JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseExportRulesXml&format=raw&kbi='.$kbi,false);
        //$view->assign('ruleRemoveUrl',JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseRemoveRule&tmpl=component&kbi='.$kbi.'&return=modelTester&file='.JRequest::getString('file','').'&rule={ruleId}'));//TODO
      }else{
        $rulesExportUrl=JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTesterExportRulesXml&tmpl=component&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules,false);
      }

      $view->assign('rulesExportUrl',$rulesExportUrl);
      $view->assign('testUrl',JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTesterRequest&format=raw&rulesXml='.self::MODELTESTER_XML_DIR.'/'.$kbi.'-'.$lmtask.'&dataCsv='.$testFile,false));
    }

    $view->assign('kbi',$kbi);
    $view->assign('lmtask',$lmtask);
    $view->assign('rules',$rules);


    //$view->assignRef('drlXml',$drlRulesXml);
    $view->display();
  }

  public function modelTesterExportRulesXml(){
    $kbiId=JRequest::getInt('kbi',-1);
    $lmtaskId=JRequest::getVar('lmtask','');
    $template=JRequest::getVar('template',self::RULES_XML_TEMPLATE);
    $rules=JRequest::getString('rules',JRequest::getString('rulesIds',""));

    try{
      $source=$this->getKbiSource($kbiId);
      $options=array('export'=>$lmtaskId,'template'=>$template);
      $result=$source->queryPost(null,$options);

      if (!strpos($result,'<AssociationRules')){
        throw new Exception('Export failed!');
      }
      $rules=$this->decodeRules($rules);
      if (is_array($rules)&&count($rules)){
        $result=$this->cleanRulesXml($result,$rules);
      }
      file_put_contents(self::MODELTESTER_XML_DIR.'/'.$kbiId.'-'.$lmtaskId,$result);
    }catch (Exception $e){
      exit(var_dump($e));
      //TODO show error
    }
  }

  private function cleanRulesXml($xmlString,$rulesArr){
    $outputXml='';
    if (strpos($xmlString,'encoding="windows-1250"')){
      $xmlString=str_replace('encoding="windows-1250"','encoding="utf-8"',$xmlString);
      $xmlString=iconv('cp1250','utf8',$xmlString);
    }
    $pos=mb_strpos($xmlString,'<AssociationRules',0,'utf8');
    $pos=mb_strpos($xmlString,'>',$pos,'utf8');
    $outputXml=mb_substr($xmlString,0,$pos+1,'utf8');
    $xmlString=mb_substr($xmlString,$pos+1,null,'utf8');

    $pos=0;
    while (($pos=mb_strpos($xmlString,'<AssociationRule',$pos,'utf8'))!==false){
      $endPos=mb_strpos($xmlString,'>',$pos,'utf8');
      $startTag=mb_substr($xmlString,$pos,$endPos-$pos,'utf8').'/>';

      $xml=simplexml_load_string($startTag);
      $id=(string)$xml['id'];
      $endPos=mb_strpos($xmlString,'</AssociationRule',$endPos,'utf8');
      $endPos=mb_strpos($xmlString,'>',$endPos,'utf8');
      if (in_array($id,$rulesArr)){
        //copy tag
        unset($xml);
        $outputXml.=mb_substr($xmlString,$pos,$endPos-$pos+1,'utf8');
      }
      $xmlString=mb_substr($xmlString,$endPos+1,null,'utf8');
      $pos=0;
    }
    $outputXml.=$xmlString;
    return $outputXml;
  }

  public function modelTesterRequest(){
    $rulesXml=JRequest::getString('rulesXml','');
    $dataCsv=JRequest::getString('dataCsv','');

    if (substr($rulesXml,0,2)=='./'){
      $rulesXml=JURI::base().substr($rulesXml,2);
    }
    if (substr($dataCsv,0,2)=='./'){
      $dataCsv=JURI::base().substr($dataCsv,2);
    }

    try{
      $output=array();
      //exit(self::MODELTESTER_URL.'?complexResults=ok&rulesXml='.$rulesXml.'&dataCsv='.$dataCsv);
      $content=@file_get_contents(self::MODELTESTER_URL.'?complexResults=ok&rulesXml='.$rulesXml.'&dataCsv='.$dataCsv);
      $xml=simplexml_load_string($content);
      $output['truePositive']=(string)$xml->truePositive;
      $output['falsePositive']=(string)$xml->falsePositive;
      $output['rowsCount']=(string)$xml->rowsCount;

      if (isset($xml->rulesMatches)&&count($xml->rulesMatches->rule)){
        $rulesResultsArr=array();
        foreach ($xml->rulesMatches->rule as $rule){
          $id=(string)$rule['id'];
          $rulesResultsArr[$id]=array('truePositive'=>(string)$rule['truePositive'],'falsePositive'=>(string)$rule['falsePositive']);
        }
        unset($xml);
        $xml=simplexml_load_file($rulesXml);
        $output['rules']=array();
        foreach ($xml->AssociationRule as $associationRule){
          $id=(string)$associationRule['id'];
          $truePositive=0;
          if (isset($rulesResultsArr[$id])){
            $truePositive=$rulesResultsArr[$id]['truePositive'];
          }
          $falsePositive=0;
          if (isset($rulesResultsArr[$id])){
            $falsePositive=$rulesResultsArr[$id]['falsePositive'];
          }
          $output['rules'][]=array('id'=>(string)$associationRule['id'],'text'=>(string)$associationRule->Text,'truePositive'=>$truePositive,'falsePositive'=>$falsePositive);
        }

      }
      echo json_encode($output);
    }catch (Exception $e){
      //TODO return error
    }
  }

  //region export CSV from DB
  public function modelTesterExportConnectionCSV(){
    $kbi=JRequest::getString('kbi');
    $lmtask=JRequest::getString('lmtask');
    $rules=JRequest::getString('rules');

    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $taskId=JRequest::getInt('task_id',JRequest::getInt('taskId',-1));
    $kbiId=JRequest::getInt('kbi',-1);
    $columnName=JRequest::getString('col','');
    $task=null;
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

    /** @var dbconnectModelUnidb $unidbModel */
    $unidbModel=&$this->getModel('Unidb','dbconnectModel');
    $dbError=$unidbModel->setDB($connection->db_type,$connection->server,$connection->username,$connection->getPassword(),$connection->db_name);
    if ($dbError!=''){
      JError::raiseError(500,$dbError);
      return ;
    }

    //output CSV file
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    $testFileName=$kbiId.'_trainData';
    $testFile=dbconnectModelUploads::DATA_DIR.'/'.$testFileName;

    if (!$unidbModel->exportCsv($connection->table,$testFile)){
      $this->showErrorView(JText::_('DB_EXPORT_FAILED'),JText::_('DB_EXPORT_FAILED'));
      return;
    }

    $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTester&tmpl=component&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules.'&file='.$testFileName,false));
  }
  //endregion

  #region modelTesterUploadCSV
  /**
   *   Akce pro upload CSV souboru
   */
  public function modelTesterUploadCSV(){
    $kbi=JRequest::getString('kbi');
    $lmtask=JRequest::getString('lmtask');
    $rules=JRequest::getString('rules');

    if (isset($_FILES['url'])){
      //test, jestli byl odeslán formulář
      $fileData=$_FILES['url'];
      $fileName=$fileData['name'];if (is_array($fileName)){$fileName=$fileName[0];}
      $fileTmpName=$fileData['tmp_name'];if (is_array($fileTmpName)){$fileTmpName=$fileTmpName[0];}

      $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
      if ($fileId=$uploadsModel->insertFile($fileName,$fileTmpName)){
        $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&tmpl=component&task=modelTesterUploadCSV_step2&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules.'&file='.$fileId,false));
        return ;
      }
    }
    $view=$this->getView('DataModelTesterUploadCSV',$this->document->getType());
    $view->assign('kbi',JRequest::getString('kbi'));
    $view->assign('lmtask',JRequest::getString('lmtask'));
    $view->assign('rules',JRequest::getInt('rules'));
    $view->display();
  }

  /**
   *  Funkce pro zadání základních parametrů uploadu CSV souboru
   */
  public function modelTesterUploadCSV_step2(){
    $kbi=JRequest::getString('kbi');
    $lmtask=JRequest::getString('lmtask');
    $rules=JRequest::getString('rules');

    $fileId=JRequest::getInt('file',-1);
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    $fileData=$uploadsModel->getFile($fileId);

    if (!$fileData){
      //pokud nemáme data o nahraném souboru, tak uživatele přesměrujeme na nahrání jiného
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&tmpl=component&task=modelTesterUploadCSV&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules,false));
      return ;
    }

    /** @var dataViewDataModelTesterUploadCSV_2 $view */
    $view=$this->getView('DataModelTesterUploadCSV_2',$this->document->getType());
    $view->assignRef('fileData',$fileData);
    $view->assign('table_name',$uploadsModel->cleanName($fileData->filename));
    $view->assign('delimitier',trim(JRequest::getString('delimitier',$uploadsModel->getCSVDelimitier($fileData->id))));
    $view->assign('enclosure',trim(JRequest::getString('enclosure','"')));
    $view->assign('escapeChar',trim(JRequest::getString('escapeChar','\\')));
    $view->assign('kbi',JRequest::getString('kbi'));
    $view->assign('lmtask',JRequest::getString('lmtask'));
    $view->assign('rules',JRequest::getInt('rules'));
    $view->display();
  }

  /**
   *  Akce pro naimportování CSV do databáze
   */
  public function modelTesterUploadCSV_import(){
    $kbi=JRequest::getString('kbi');
    $lmtask=JRequest::getString('lmtask');
    $rules=JRequest::getString('rules');
    $fileId=JRequest::getInt('file',-1);
    /** @var $uploadsModel dbconnectModelUploads */
    $uploadsModel=&$this->getModel('Uploads','dbconnectModel');
    $fileData=$uploadsModel->getFile($fileId);

    if (!$fileData){
      //pokud nemáme data o nahraném souboru, tak uživatele přesměrujeme na nahrání jiného
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&tmpl=component&task=modelTesterUploadCSV&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules,false));
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
    $uploadsModel->iconvFile($fileId,$encoding);

    if ($uploadsModel->prepareCsvFile(dbconnectModelUploads::DATA_DIR.'/'.$kbi.'_testData',$fileId,$delimitier,$enclosure,$escapeChar)){
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTester&tmpl=component&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules.'&file='.$kbi.'_testData',false));
      return;
    }
    $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&tmpl=component&task=modelTesterUploadCSV&kbi='.$kbi.'&lmtask='.$lmtask.'&rules='.$rules,false));
  }
  #endregion modelTesterUploadCSV

  private function decodeRules($rules){
    if ($rulesEncoded=json_decode($rules)){
      $rules=$rulesEncoded;
    }
    if (!is_array($rules)&&(strpos($rules,','))){
      $rules=explode(',',$rules);
    }
    if (is_int($rules)){
      $rules=array(0=>$rules);
    }
    return $rules;
  }

  /**
   * Akce vracející počet pravidel v BR base pro konkrétní úlohu
   */
  public function brBaseRulesCount(){
    //TODO není finální verze
    $kbiId=JRequest::getInt('kbi',-1);
    $rulesExportUrl=self::BRBASE_URL.'/association-rules/association-rules-count?baseId=http://easyminer.eu/kb/KnowledgeBase/kb'.$kbiId.'&kbi='.$kbiId;
    echo file_get_contents($rulesExportUrl);
  }

  public function brBaseShow(){
    $kbiId=JRequest::getInt('kbi',-1);
    /** @var dbconnectModelTasks $tasksModel */
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');

    /** @var dataViewBRBaseShow $view */
    $view=&$this->getView('BRBaseShow','html');
    $view->assign('kbiId',$kbiId);//TODO není finální verze...
    $rulesetContent=file_get_contents(self::BRBASE_URL.'/rule-set/get?uri=http://easyminer.eu/kb/RuleSet/'.$kbiId.'&baseId=http://easyminer.eu/kb/KnowledgeBase/kb'.$kbiId);
    $view->assign('deleteRuleUrl',self::BRBASE_URL.'/rule/delete?uri={:ruleUri}&ruleset=http://easyminer.eu/kb/RuleSet/{:kbiId}&baseId=http://easyminer.eu/kb/KnowledgeBase/kb{:kbiId}');
    $view->assign('rulesXml',simplexml_load_string($rulesetContent));
    $view->display();

  }

  public function brBaseRemoveRule(){
    $kbiId=JRequest::getInt('kbi',-1);
    /** @var dbconnectModelTasks $tasksModel */
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTaskByKbi($kbiId);
    $ruleId=JRequest::getInt('rule',-1);//TODO není finální verze...
    if ($task && $ruleId){
      file_get_contents(self::BRBASE_URL.'/rule/delete?uri=http://easyminer.eu/kb/Rule/'.$ruleId.'&ruleset=http://easyminer.eu/kb/RuleSet/'.$kbiId.'&baseId=http://easyminer.eu/kb/KnowledgeBase/kb'.$kbiId);
    }
    if (JRequest::getVar('return','')=='modelTester'){
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTester&tmpl=component&kbi='.$kbiId.'&lmtask=BRBASE&file='.JRequest::getString('file'),false));
    }else{
      $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseShow&tmpl=component&kbi='.$kbiId,false));
    }

  }

  public function brBaseRemoveAllRules(){
    $kbiId=JRequest::getInt('kbi',-1);
    /** @var dbconnectModelTasks $tasksModel */
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTaskByKbi($kbiId);
    if ($task){
      /** @var dbconnectModelBRBase $brbaseModel */
      $brbaseModel=&$this->getModel('BRBase','dbconnectModel');
      $brbaseModel->removeAllRules($task->id);
    }
    $this->setRedirect(JRoute::_('index.php?option=com_dbconnect&controller=data&task=brBaseShow&tmpl=component&kbi='.$kbiId,false));
  }

  public function brBaseExportRulesXml(){
    $kbiId=JRequest::getInt('kbi',-1);
    /*
    $tasksModel=&$this->getModel('Tasks','dbconnectModel');
    $task=$tasksModel->getTaskByKbi($kbiId);
    $ruleId=JRequest::getInt('rule',-1);
    $brbaseModel=&$this->getModel('BRBase','dbconnectModel');
    $result=$brbaseModel->getRulesXml($task->id);*/

    $rulesExportUrl=self::BRBASE_URL.'/association-rules/export-association-rules?baseId=http://easyminer.eu/kb/KnowledgeBase/kb'.$kbiId.'&kbi='.$kbiId;
    file_put_contents(self::MODELTESTER_XML_DIR.'/'.$kbiId.'-'.'BRBASE',file_get_contents($rulesExportUrl));
  }

  public function brBaseAddRules(){
    $kbiId=JRequest::getInt('kbi',-1);
    $lmtaskId=JRequest::getVar('lmtask','');
    $template=JRequest::getVar('template',self::RULES_XML_TEMPLATE);
    $rules=JRequest::getString('rules',JRequest::getString('rulesIds',""));

    try{
      /** @var LispMiner $source */
      $source=$this->getKbiSource($kbiId);
      $resultRules=$source->queryPost(null,array('export'=>$lmtaskId,'template'=>$template));

      #region získání atributů
      require_once (JPATH_COMPONENT.DS.'../com_kbi/models/transformator.php');
      $config = array(                        //TODO - kde se použije ID kbi zdroje???
        'source' => JRequest::getVar('kbi', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
        'query' => JRequest::getVar('query', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
        'xslt' => JRequest::getVar('xslt', NULL, 'default', 'none', JREQUEST_ALLOWRAW),
        'parameters' => JRequest::getVar('parameters', NULL, 'default', 'none', JREQUEST_ALLOWRAW)
      );
      $model = new KbiModelTransformator($config);
      $resultAttributes=$model->getDataDescription(array('template'=>self::ATTRIBUTES_XML_TEMPLATE));
      #endregion získání atributů

      if (!strpos($resultRules,'<AssociationRules')){
        throw new Exception('Export failed!');
      }
      $rules=$this->decodeRules($rules);
      if (is_array($rules)&&count($rules)){
        $resultRules=$this->cleanRulesXml($resultRules,$rules);
      }

      //TODO doladit - aktuálně jen provizorní řešení...
      #regin odeslání dat do EasyMinerCenter
      $urlAttributes = self::BRBASE_URL.'/association-rules/import-data-description?baseId=http://easyminer.eu/kb/KnowledgeBase/kb'.$kbiId.'&kbi='.$kbiId;
      $data = array('data' => $resultAttributes);
      $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
        ),
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($urlAttributes, false, $context);
      $urlRules = self::BRBASE_URL.'/association-rules/import-association-rules?baseId=http://easyminer.eu/kb/KnowledgeBase/kb'.$kbiId.'&kbi='.$kbiId;

      $data = array('data' => $resultRules);
      $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
        ),
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($urlRules, false, $context);

      #endregion odeslání dat do EasyMinerCenter
      /*
      / ** @var dbconnectModelTasks $tasksModel * /
      $tasksModel=&$this->getModel('Tasks','dbconnectModel');
      $task=$tasksModel->getTaskByKbi($kbiId);
      / ** @var dbconnectModelBRBase $brbaseModel * /
      $brbaseModel=&$this->getModel('BRBase','dbconnectModel');
      $brbaseModel->addRules($resultRules,$task->id);
      */
    }catch (Exception $e){
      var_dump($e);
      exit();
      //TODO show error
    }
  }

}
?>

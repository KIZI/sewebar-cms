<?php  
jimport('joomla.application.component.controller');
                  
/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */                                     
class MappingController extends JController{

  /**
   *  Funkce pro výběr článků a spuštění mapování při mapování za účelem DM úlohy
   *  @requires com_dbconnect   
   */     
  public function startTaskMapping(){   
    require_once (JPATH_COMPONENT.DS.'models'.DS.'data.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'selArticlesTaskMapping.html.php');
		                                                          
    require_once (JPATH_COMPONENT.DS.'models'.DS.'config.php');
		require_once (JPATH_COMPONENT.DS.'models'.DS.'tasks.php');
		
	  $tasksModel=new dbconnectModelTasks();
	  $task=$tasksModel->getTask(JRequest::getInt('id',-1));
	  if (!$task){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }      
    
    $view = new MappingViewSelArticlesTaskMapping();
    $view->setModel(new DataModel(),true);
    $view->assignRef('configModel',new ConfigModel());
    $view->assign('task',$task);
	  $view->display();
  }

  /**
   *  Akce pro výběr článků s daty
   */     
  function selArticles(){            
    require_once (JPATH_COMPONENT.DS.'models'.DS.'data.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'selArticles.html.php');
		require_once (JPATH_COMPONENT.DS.'models'.DS.'config.php');
	  $view = new MappingViewSelArticles();
    $view->setModel(new DataModel(),true);
    $view->assignRef('configModel',new ConfigModel());
	  $view->display();
  }
  
  /**
   *  Funkce pro yobrazení iframe view s články - výběr článků
   */     
  function articlesiframe(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'data.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'articlesIframe.html.php');
		
	  $view = new MappingViewArticlesIframe();
    $view->setModel(new DataModel(),true );
	  $view->display();
  }
  /******************************************************************************************************************/
  function similarity(){            
    require_once (JPATH_COMPONENT.DS.'models'.DS.'data.php');
    require_once (JPATH_COMPONENT.DS.'library'.DS.'XmlConnector.php');
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $dataModel=new DataModel();
    
    //načteme články z DB
    $art2=$dataModel->loadArticle(JRequest::getVar('art2',-1),true);
    
    //overime, jestli jde o mapovani konkretni ulohy a podle toho nacteme prvni soubor
    $taskId=JRequest::getVar('taskId',0);
    if ($taskId>0){
      //jde o úlohu
      require_once (JPATH_COMPONENT.DS.'models'.DS.'tasks.php');
      $tasksModel=new dbconnectModelTasks();
      $task=$tasksModel->getTask($taskId);
      $art1content=$tasksModel->getTableContent($taskId);
    }else{
      //nacteme clanek z DB
      $art1=$dataModel->loadArticle(JRequest::getVar('art1',-1),true);
      $art1content=$art1->text;
    }
    
    //zkontrolujeme,jestli jde o podporovane typy souboru a pripravime transformaci
    $error=false;                 
    $art1sourceType='';           
    $art1dataXML=XmlConnector::loadInputXML($art1content,$art1sourceType); 
    if ($art1dataXML){                   
      $workData=new WorkDataModel();                                                                    
      $workData->reset();
      $workData->save('assignClass',JRequest::getVar('assignClass','ManualAssignClass'));    
      $workData->save('valuesAssignClass',JRequest::getVar('valuesAssignClass','ManualValuesAssignClass'));   
      if ($taskId){
        //jde o ulohu
        $workData->save('art1',array('taskId'=>$task->id,'title'=>$task->name,'sourceType'=>(string)$art1sourceType));
      }else{
        //jde o PMML soubor
        $workData->save('art1',array('id'=>$art1->id,'title'=>$art1->title,'sourceType'=>(string)$art1sourceType));  
      }
      $art2sourceType='';
      $art2dataXML=XmlConnector::loadInputXML($art2->text,$art2sourceType);
      $workData->save('art2',array('id'=>$art2->id,'title'=>$art2->title,'sourceType'=>(string)$art2sourceType));
      if (!($art2dataXML)){
        $error=true;
        $this->showError(JText::_('FILE2_FORMAT_ERROR'),'index.php?option=com_mapping&amp;task=selArticles&amp;task=selArticles&art1='.$art1->id);
      }
      
      //máme zadaná FML data - uložíme ID do workdata
      $fmlId=JRequest::getInt('artFml',0);
      if ($fmlId>0){
        $fmlArt=$dataModel->loadArticle($fmlId,true);
        if ($fmlArt){
          $workData->save('fml',array('id'=>$fmlArt->id,'title'=>$fmlArt->title));
        }
      }
    }else{
      $error=true;
      $this->showError(JText::_('FILE1_FORMAT_ERROR'),'index.php?option=com_mapping&amp;task=selArticles&amp;task=selArticles&art2='.$art2->id);
    }     
    if ($error){
      //vyskytla se nejaka chyba
      if (isset($workData)){
        $workData->unsetVar('art1');
        $workData->unsetVar('art2');
        $workData->unsetVar('fml');
      }
    }else{
      //spustime vypocet podobnosti
      require_once(JPATH_COMPONENT.DS.'library'.DS.'MatchingClass.php');
      $matchingClass=new MatchingClass;
      $matchingClass->loadXML($art1dataXML,$art2dataXML);    
      $xmlData=$matchingClass->getMatchingXML(); 
      //pripravime data pro useredit
      require_once (JPATH_COMPONENT.DS.'library'.DS.'XmlConnector.php');
      XMLConnector::loadMatchingXML($xmlData);
      //pokud mame FML, vyresime  předdefinovaná mapování
      if ($fmlArt){
        //máme definovaný mapovací soubor
        $this->autoAddUserMerges(simplexml_load_string($fmlArt->text),$workData);
      }
      //TODO//exit(var_dump($_SESSION));
      //presmerujeme stranku na uzivatelske upravy
      $this->setRedirect('index.php?option=com_mapping&task=useredit');  
    }
  }
  /******************************************************************************************************************/
  /**
   *  Funkce pro nastavení uživatelských dat...
   */ 
  private function autoAddValuesUserMerge($fml,$workData=null){ 
    if (!$workData){
      $workData=new WorkDataModel();
    }
    /*zjistime,jestli mame k dispozici nejaka uzivatelska mapovani*/
    require_once (JPATH_COMPONENT.DS.'library'.DS.'XmlConnector.php');   
    $userFieldsInfo=XMLConnector::loadFMLUserFields($fml,true);
    $userFields=$userFieldsInfo['mappingArr'];
    
    if (count($userFields)>0){
      //načteme třídu pro mapování...
      $userDataArr=$workData->load("userDataArr");
      $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$userDataArr);
      $legendArr=$workData->load('legendArr');
      
      $valuesMapArr=$workData->load("valuesMapArr");
      $valuesAssignClass=$this->newValuesAssignClass($workData->load('valuesAssignClass'),$valuesMapArr,$workData->load("finalArr"));
      
      $finalArr=$workData->load('finalArr');
      //projdeme všechna data z finalArr
      foreach ($finalArr as $key=>$arr) {
        //potřebujeme zjistit názvy mapování
        $name1=$legendArr[$key];
        $key2=$arr['name'];
        $name2=$legendArr[$key2];
        	
        //projdeme vsechna mapovani v userFields (tj. z FDML)
        foreach ($userFields as $userField) {
        	if (($name1==$userField[0])&&($name2==$userField[1])){
            //jde o realne mapovani - mrkneme na hodnoty
            $valuesPairsArr=XMLConnector::getValuesPairs($fml,$userField['fmId'],$userFieldsInfo['dict1id'],$userFieldsInfo['dict2id']);
            if (count($valuesPairsArr)>0){
              //projdeme všechny nalezené páry hodnot, které jsou namapované...
              foreach ($valuesPairsArr as $valuesArr) {
                $valueAkey=array_search($valuesArr[0],$valuesMapArr);
                $valueBkey=array_search($valuesArr[1],$valuesMapArr);
                if (($valueAkey!==false)&&($valueAkey!==false))
              	$valuesAssignClass->addValuesMap($key,$valueAkey,$valueBkey);
              }
            }
          }
        }  
      }
       
      $workData->save('finalArr',$valuesAssignClass->getFinalArr());
      $workData->save("dataArr",$assign->dataArr);
      $workData->save("userDataArr",$assign->finalArr);
    }
  }
  
  /**
   *  Funkce pro nastavení uživatelských dat...
   */ 
  private function autoAddUserMerges($fml,$workData=null){
    if (!$workData){
      $workData=new WorkDataModel();
    }
    /*zjistime,jestli mame k dispozici nejaka uzivatelska mapovani*/
    require_once (JPATH_COMPONENT.DS.'library'.DS.'XmlConnector.php');
    $userFields=XMLConnector::loadFMLUserFields($fml);
    if (count($userFields)>0){
      //načteme třídu pro mapování...
      $userDataArr=$workData->load("userDataArr");
      $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$userDataArr);
      $legendArr=$workData->load('legendArr');
      
      //potrebujeme predpripravit pole s klici z legendArr
      $fieldNames1Arr=array();
      $fieldNames2Arr=array();
      foreach ($legendArr as $key=>$name) {
      	if ($key[0]=='a'){
          //jde o sloupec 1
          $fieldNames1Arr[$name]=$key;
        }else{
          //jde o sloupec 2
          $fieldNames2Arr[$name]=$key;
        } 
      }
    
      //projdeme všechna uživatelská namapování
      foreach ($userFields as $userField) {
        //nejprve zjistíme, jaké máme identifikátory jednotlivých polí...
        $name1=$userField[0];
        $name2=$userField[1];
        
        $key1=@$fieldNames1Arr[$name1];
        $key2=@$fieldNames2Arr[$name2];
        if (($key1!='')&&($key2!='')){
          $assign->addUserMerge($key1,$key2);
        }
      }
      
      $workData->save("dataArr",$assign->dataArr);
      $workData->save("userDataArr",$assign->finalArr);
    }
  }  
  
  
  
  
  
  /******************************************************************************************************************/
  /* Uživatelská úprava mapování hodnot */ 
  /**
   *  Funkce pro přesypání dat z assignArr do finalArr
   */        
  public function startMapValues(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();       
    $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$workData->load("userDataArr"));     
    $assignArr=$assign->getAssignArr();   
    /*projdeme assignArr a prejmenujeme na value na match*/
    if (count($assignArr)>0){  
      foreach ($assignArr as $key=>$arr) {
      	if (isset($assignArr[$key]['value'])){
          $assignArr[$key]['match']=$assignArr[$key]['value'];
          unset($assignArr[$key]['value']);
        }
      }
    }
    /**/    
    $workData->save("finalArr",$assignArr);                                                
    $this->setRedirect("index.php?option=com_mapping&task=mapValues");
  } 
  
  /**
   *  Funkce pro přidání hodnot z hodnotového pole do globálního pole
   *  vrací pole klíčů z mapovacího pole   
   */     
  private function makeValuesMapArr(&$valuesMapArr,&$invertMapArr,$valuesArr){
    $returnArr=array();                //TODO mrknout, jestli by to nešlo ještě zefektivnit...
    if (count($valuesArr)>0){
      foreach ($valuesArr as $value){
        if (isset($invertMapArr[$value])){
          $key=$invertMapArr[$value];
        }else{
          $valuesMapArr[]=$value;    
          $key=array_search($value, $valuesMapArr);
          $invertMapArr[$value]=$key;
        }
        $returnArr[]=$key;
      } 
    }
    return $returnArr;
  }
  
  /**
   *  Funkce pro prvotní mapování totožných hodnot
   */  
  public function mapValues(){                 
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();               
    /*spojení totožných hodnot*/
    $finalArr=$workData->load("finalArr");
    $workData->unsetVar("dataArr"); 
    $valuesArr=$workData->load("valuesArr");    
    $invertMapArr=array();
    $valuesMapArr=array();                         
    //projdeme pole s hodnotami a rozdělíme je do finalArr
    if (count($valuesArr)>0){ 
      foreach ($valuesArr as $key=>$arr) {
      	if (ereg("^a", $key)){
          //jde o klic s hodnotami pro A
          $finalArr[$key]['valuesA']=$this->makeValuesMapArr($valuesMapArr,$invertMapArr,$arr);
          unset($valuesArr[$key]);
          $keyBname=$finalArr[$key]['name'];
          if (isset($valuesArr[$keyBname])){
            $finalArr[$key]['valuesB']=$this->makeValuesMapArr($valuesMapArr,$invertMapArr,$valuesArr[$keyBname]);
          }
        }else{
          //jde o klic s hodnotami pro B
          //musime projit cele finalArr a najit vhodne umisteni...
          foreach ($finalArr as $keyFinal=>$arrFinal) {
          	if ($arrFinal['name']==$key){
              $finalArr[$keyFinal]['valuesB']=$this->makeValuesMapArr($valuesMapArr,$invertMapArr,$arr);
            }
          }
        }
      }                                       
      $workData->unsetVar("valuesArr");
      $workData->save("valuesMapArr",$valuesMapArr);                                             
      $workData->save("finalArr",$finalArr);
      unset($invertMapArr);
    }                                                              
    $valuesAssignClass=$this->newValuesAssignClass($workData->load('valuesAssignClass'),$valuesMapArr,$finalArr);
    $valuesAssignClass->initMapping();
    $finalArr=$valuesAssignClass->getFinalArr();
    $workData->save("finalArr",$finalArr);
    
    //pokud budeme mít nějaký mapovací soubor, tak musíme zpracovat jeho mapování....
    $fmlW=$workData->load('fml');
    if (isset($fmlW['id'])){
      require_once (JPATH_COMPONENT.DS.'models'.DS.'data.php');
      $dataModel=new DataModel();
    	$fmlArt=$dataModel->loadArticle($fmlW['id'],true);
      if ($fmlArt->text)
        $fml=@simplexml_load_string($fmlArt->text);
    }
    //pokud máme načtené FML, tak ho budeme zpracovávat
    if ($fml){
      //potřebujeme projít celé finalArr a zpracovat mapování, která jsou stejná, jako ta, která už byla v FDML
      $this->autoAddValuesUserMerge($fml,$workData);
    }
                   
    $this->setRedirect('index.php?option=com_mapping&task=usereditValues'); 
  } 
  
  public function usereditValues(){      
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');      
    switch(@$_GET["action"]){
      case "join":$this->userJoinValues($_GET["fieldA"],$_GET["keyA"],$_GET["keyB"]);break;
      case "unjoin":$this->userUnjoinValues($_GET["fieldA"],$_GET["keyA"],$_GET["keyB"]);break;
      case "unconfirm":$this->userUnconfirmValues($_GET["fieldA"],$_GET["keyA"],$_GET["keyB"]);break;
      case "json":$this->getJSONValuesOutput();break;
      case "jsonLegend": $this->getJSONValuesLegendOutput();break;//TODO
      default:
        //zobrazime view          
        require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'usereditValues.html.php');
        $view=new MappingViewUserEditValues();
        $view->display(); 
        //
      break;
    }
  } 
  
  /**
   *  Funkce vracející JSON output pro VALUES
   */       
  private function getJSONValuesOutput(){        
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();    
    $output=array(
      'finalArr'=>$workData->load("finalArr")
    );
    echo json_encode($output);
  }  
  
  /**
   *  Funkce pro přidání uživatelského mapování hodnot
   */     
  private function userJoinValues($keyA,$valueA,$valueB){       
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();
    $valuesAssignClass=$this->newValuesAssignClass($workData->load('valuesAssignClass'),$workData->load("valuesMapArr"),$workData->load("finalArr"));
    $valuesAssignClass->addValuesMap($keyA,$valueA,$valueB);
    $finalArr=$valuesAssignClass->getFinalArr();
    $workData->save("finalArr",$finalArr);
    $this->getJSONValuesOutput();                            
  }  

  /**
   *  Funkce pro odebrání mapování hodnot
   */     
  private function userUnjoinValues($keyA,$valueA,$valueB){       
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();
    $valuesAssignClass=$this->newValuesAssignClass($workData->load('valuesAssignClass'),$workData->load("valuesMapArr"),$workData->load("finalArr"));  
    $valuesAssignClass->removeValuesMap($keyA,$valueA,$valueB);
    $finalArr=$valuesAssignClass->getFinalArr();
    $workData->save("finalArr",$finalArr);
    $this->getJSONValuesOutput();                            
  }

  /**
   *  Funkce pro odebrání mapování hodnot
   */     
  private function userUnconfirmValues($keyA,$valueA,$valueB){       
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();
    $finalArr=$workData->load("finalArr"); 
    $valuesAssignClass=$this->newValuesAssignClass($workData->load('valuesAssignClass'),$workData->load("valuesMapArr"),$workData->load("finalArr"));   
    $valuesAssignClass->unconfirmValuesMap($keyA,$valueA,$valueB);
    $finalArr=$valuesAssignClass->getFinalArr();
    $workData->save("finalArr",$finalArr);
    $this->getJSONValuesOutput();                            
  }    

  
  /******************************************************************************************************************/
  /*uživatelské úpravy párování*/
  /**
   *  Funkce pro obsluhu "párovacích událostí"   
   */     
  public function useredit(){                       
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');         
    switch (@$_GET["action"]) {
      case "addMerge": $this->addUserMerge($_GET["keyA"],$_GET["keyB"]);break;
      case "removeMerge": $this->removeUserMerge($_GET["keyA"],$_GET["keyB"]);break;
      case "addIgnore": $this->addUserIgnore($_GET["keyA"]);break;
      case "removeIgnore": $this->removeUserIgnore($_GET["keyA"]);break;
      case "json": $this->getJSONOutput();break;
      case "jsonLegend": $this->getJSONLegendOutput();break;
      default:
        //zobrazime view          
        require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'useredit.html.php');
        $view=new MappingViewUserEdit();
        $view->display(); 
        //
      break;
    }
  } 
  /**
   *  Funkce pro nastavení uživatelských dat...
   */ 
  private function addUserMerge($key,$key2){
    $workData=new WorkDataModel();
    /*spojení klíče se subklíčem*/
    $userDataArr=$workData->load("userDataArr");
    $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$userDataArr);     
    /*kontrola, jestli je přiřazené nějaké uživatelské propojení*/
    if ((isset($userDataArr[$key]))){
      $key2remove=$userDataArr[$key]['name'];
      $assign->removeUserMerge($key,$key2remove,$workData->load("defaultDataArr"));
    }
    /*přidání uživatelského propojení*/
    $assign->addUserMerge($key,$key2);
    $workData->save("dataArr",$assign->dataArr);
    $workData->save("userDataArr",$assign->finalArr);
    $this->getJSONOutput($assign->getAssignArr());
  }
  
  /**
   *  Funkce pro nastavení uživatelských dat...
   */ 
  private function removeUserMerge($key,$key2){
    $workData=new WorkDataModel();
    /*zrušení spojení klíče se subklíčem*/
    $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$workData->load("userDataArr"));     
    $assign->removeUserMerge($key,$key2,$workData->load("defaultDataArr"),$workData->load("userIgnoreArr"));
    $workData->save("dataArr",$assign->dataArr);
    $workData->save("userDataArr",$assign->finalArr);
    $this->getJSONOutput($assign->getAssignArr());
  }
  
  
  /**
   *  Funkce pro nastavení uživatelských dat...
   */ 
  private function addUserIgnore($key){
    $workData=new WorkDataModel();
    /*přidání klíče do ignore*/
    $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$workData->load("userDataArr"));     
    $workData->save("userIgnoreArr",$assign->addUserIgnore($key,$workData->load("userIgnoreArr")));
    $workData->save("dataArr",$assign->dataArr);
    $workData->save("userDataArr",$assign->finalArr);
    $this->getJSONOutput($assign->getAssignArr());
  }
  
  
  /**
   *  Funkce pro nastavení uživatelských dat...
   */ 
  private function removeUserIgnore($key){
    $workData=new WorkDataModel();
    /*odebrání klíče z ignore*/
    $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$workData->load("userDataArr"));     
    $workData->save("userIgnoreArr",$assign->removeUserIgnore($key,$workData->load("userIgnoreArr"),$workData->load("defaultDataArr")));
    $workData->save("dataArr",$assign->dataArr);
    $workData->save("userDataArr",$assign->finalArr);
    $this->getJSONOutput($assign->getAssignArr());
  }
        
  /**
   *  Funkce vracející JSON output
   */       
  private function getJSONOutput($assignArr=null){     
    $workData=new WorkDataModel();
    if ($assignArr==null){                     
      $assign=$this->newAssignClass($workData->load("assignClass"),$workData->load("dataArr"),$workData->load("userDataArr"));     
      $assignArr=$assign->getAssignArr();      
    }                               
    $output=array(
      'userArr'=>$workData->load("userDataArr"),
      'assignArr'=>$assignArr,
      'ignoreArr'=>$workData->load("userIgnoreArr"),
      'keysArr'=>$workData->load("keysArr"),
      'keys2Arr'=>$workData->load("keys2Arr")
    );                                   
    echo json_encode($output);           
  }
        
  /**
   *  Funkce vracející legendu k JSON output
   */       
  private function getJSONLegendOutput(){
    $workData=new WorkDataModel();    
    echo json_encode(array("legendArr"=>$workData->load('legendArr'),'defaultDataArr'=>$workData->load("defaultDataArr")));
  } 
        
  /**
   *  Funkce vracející legendu k JSON output
   */       
  private function getJSONValuesLegendOutput(){   
    $workData=new WorkDataModel();    
    echo json_encode(array("legendArr"=>$workData->load('legendArr'),'valuesMapArr'=>$workData->load("valuesMapArr")));
  }      
  
  /******************************************************************************************************************/
  /**
   *  Akce pro dokončení úlohy mapování
   *  (naučení správných párů, nabídka na vygenerování FML)   
   */     
  public function finalizeMapping(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    require_once (JPATH_COMPONENT.DS.'library'.DS.'ExpirienceClass.php');
    $expirienceClass=new ExpirienceClass(true);
    $workData=new WorkDataModel();
    $expirienceClass->saveExpiriences($workData->load("finalArr"),$workData->load("userDataArr"),$workData->load("legendArr"));
    //presmerujeme dal...       
    $this->setRedirect('index.php?option=com_mapping&task=finalizedMapping');
    //
  }  
  
  /******************************************************************************************************************/
  
  
  /******************************************************************************************************************/
  /**
   *  Akce pro dokončení úlohy mapování
   *  (naučení správných párů, nabídka na vygenerování FML)   
   */     
  public function finalizedMapping(){       
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    require_once (JPATH_COMPONENT.DS.'models'.DS.'data.php');
    $workData=new WorkDataModel();
    $art1=$workData->load('art1');
    $dataModel=new DataModel();
    
    if (@$_POST['fmlArticleId']>0){
      //máme ukládat do existujícího článku
      if ($dataModel->isArticleWritable($_POST['fmlArticleId'])){
        $dataModel->saveArticle($_POST['fmlArticleId'],$this->generateFML());
        $fmlArticleId=$_POST['fmlArticleId'];
        $saved=true;
      }
    }elseif(($_POST['title']!='')&&(isset($_POST['category']))){
      $fmlArticleId=$dataModel->newArticle($_POST['title'],$this->generateFML());
      if (($fmlArticleId!==false)&&($fmlArticleId>0)){
        $saved=true;
      }
      
    }
    
    if ($saved){
      //data byla uložena
      require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'finalizedMapping_info.html.php');
      $view=new MappingViewFinalizedMapping_info(); 
      if ($art1['taskId']){
        //aktualizujeme data úlohy
        require_once (JPATH_COMPONENT.DS.'models'.DS.'tasks.php');
  	    $tasksModel=new dbconnectModelTasks();
        $art2=$workData->load('art2');
        $tasksModel->updateTaskArticles($art1['taskId'],array('fml'=>$fmlArticleId,'bkef'=>$art2['id']));
        $view->assign('redirectUrl','index.php?option=com_dbconnect&task=listDMTasks');
      }
         
      $view->display();
    }else{
      require_once (JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'finalizedMapping.html.php');
      $view=new MappingViewFinalizedMapping();    
      //ověříme, jestli jde o mapování z konkrétní úlohy
      if ($art1['taskId']){
        //jde o zpracovani ulohy - musime aktualizovat informace o uloze v DB
        require_once (JPATH_COMPONENT.DS.'models'.DS.'tasks.php');
    	  $tasksModel=new dbconnectModelTasks();
    	  $task=$tasksModel->getTask($art1['taskId']);
        
        $view->assignRef('task',$task);         
                                       
        if ($fmlArticle=$dataModel->loadArticle($task->fml_article)){   
          //máme existující článek s mapováním
          $view->assignRef('taskFmlArticle',$fmlArticle);
        }
      }   
      
      //zobrazime view          
      $view->display();
    }
  }
  
  
  
  public function downloadFML(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();                           
    $document =& JFactory::getDocument();
    $document->setMimeEncoding('text/xml');               
    echo $this->generateFML();
  }
  
  /******************************************************************************************************************/
  /**
   *  Akce pro vygenerování FML a jeho vypsání na výstup
   */     
  private function generateFML(){     
    require_once (JPATH_COMPONENT.DS.'models'.DS.'workdata.php');
    $workData=new WorkDataModel();
    require_once(JPATH_COMPONENT.DS.'library'.DS.'XmlConnector.php');
    return XMLConnector::generateFML($workData->load("finalArr"),$workData->load("legendArr"),$workData->load("valuesMapArr"),$workData->load("userDataArr"),$workData->load("art1"),$workData->load("art2"));
  }  
  /******************************************************************************************************************/
  /**
   *  Funkce pro zobrazení stránky s chybou...
   *  @param $text - textový popis chyby
   *  @param $link - odkaz pro pokračování      
   */     
  private function showError($text,$link){
    require_once(JPATH_COMPONENT.DS.'views'.DS.'main'.DS.'error.html.php');
    $view=new MappingViewError();
    $view->text=$text;
    $view->link=$link;
    $view->display();
  }
  /******************************************************************************************************************/
  /**
   *  Funkce pro dynamické načtení assignClass
   */     
  private function newAssignClass($assignClassName,$dataArr,$finalArr){
    require_once(JPATH_COMPONENT.DS.'library'.DS.$assignClassName.'.php');
    return new $assignClassName($dataArr,$finalArr);
  } 
  /**
   *  Funkce pro dynamické načtení valuesAssignClass
   */     
  private function newValuesAssignClass($assignClassName,$valuesMapArr,$finalArr){  //TODO parametry
    require_once(JPATH_COMPONENT.DS.'library'.DS.$assignClassName.'.php');
    return new $assignClassName($valuesMapArr,$finalArr);
  } 
  /******************************************************************************************************************/
  /**
   *  Funkce pro zobrazení nastavení komponenty
   */     
  public function config(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'config.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'config'.DS.'config.html.php');
	  $configModel=new ConfigModel();
    $view = new MappingViewConfig();
    $view->assignRef('configModel',$configModel);
    /*pokud máme odeslané nastavení, tak ho uložíme...*/
    if (JRequest::getString('submitConfig',"")=="ok"){
      foreach ($_POST as $key=>$value) {
      	if(ereg("^config#",$key)){
          /*jde o naše nastavení*/
          $keynameArr=split("#",$key);
          switch ($keynameArr){
            case "matchRate":$valueX=JRequest::getFloat($key,1);break;
            case "assignClass":$valueX=JRequest::getInt($key,1);break;
            default:$valueX=$value;break;
          }        
          $configModel->saveConfigValue($keynameArr[1],$keynameArr[2],$valueX);
        }
      }
      $view->assign('message',JText::_('SETTINGS_SAVED'));
    }
    /**/
	  $view->display();
  } 


}
?>

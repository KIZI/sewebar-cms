<?php

jimport( 'joomla.application.component.controller' );
 
/**
 *  Controller pro práci uživatele - vytváření článků
 */  
class articlesController extends JController{
  var $document;
  
  public function articleslist(){      
    $configModel=$this->getModel('Config','sewebarModel');
    $view=&$this->getView('Articleslist',$this->document->getType());
    $view->setModel($this->getModel('Articles','sewebarModel'),true);
    $view->setModel($this->getModel('Admin','sewebarModel'));
    $view->assign('parentUserGroupId',$configModel->getConfig("PARENT_USERGROUP_ID"));
    $view->assign('usergroupId',JRequest::getInt('usergroup',0));   
    $view->display();
  }
  
  /**
   *  Funkce pro zobrazení stránky se všemi články, které mohu editovat
   */     
  public function myarticles(){         
    $view=&$this->getView('Myarticles',$this->document->getType());   
    $view->setModel($this->getModel('Articles','sewebarModel'),true);   
    $view->display();                                                
  } 
  
  /**
   *  Funkce pro smazání článku
   */     
  public function deleteArticle(){
    $articleId=JRequest::getInt('id',-1);
    $articlesModel=$this->getModel('Articles','sewebarModel');
    $article=$articlesModel->getArticle($articleId);
    if (!$article){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }else{
      //můžeme zobrazit dotaz, nebo článek označit jako smazaný...
      $view=&$this->getView('DeleteArticle',$this->document->getType());
      $view->assignRef('article',$article);
      
      if (@$_POST['confirm']=='delete'){
        $result=$articlesModel->deleteArticle($articleId);
        if ($result){
          $view->assign('confirm','delete');
        }else{
          $view->assign('confirm','storno');
        }
      }
      
      $view->display();
    }
  }
  
  /**
   *  Funkce pro smazání článku
   */     
  public function renameArticle(){
    $articleId=JRequest::getInt('id',-1);
    $articlesModel=$this->getModel('Articles','sewebarModel');
    $article=$articlesModel->getArticle($articleId);
    if (!$article){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }else{
      //můžeme zobrazit dotaz, nebo článek označit jako smazaný...
      $view=&$this->getView('RenameArticle',$this->document->getType());
      $view->assignRef('article',$article);
      $title=JRequest::getString('title','');
      
      if ((@$_POST['confirm']=='rename')&&($title!='')){
        $result=$articlesModel->renameArticle($articleId,$title);
        if ($result){
          $view->assign('confirm','rename');
        }else{
          $view->assign('confirm','storno');
        }
      }
      
      $view->display();
    }
  }  
  
  /**
   *  Funkce pro založení nového článku
   */     
  public function newArticle(){
    $categoryId=JRequest::getInt('catid',-1);
    if ($categoryId==-1){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    $title=JRequest::getString('title','');
    $view=&$this->getView('NewArticle',$this->document->getType());
    $view->assign('categoryId',$categoryId);
    if ($title!=''){
      //vytvorime novy clanek
      $articlesModel=$this->getModel('Articles','sewebarModel');
      if ($articlesModel->newArticle($title,$categoryId)){
        $view->assign('confirm','created');
      }else{
        $view->assign('confirm','storno');
      }
    }
    $view->display();
  } 
  
  /**
   *  Akce pro upload PMML souborů
   */     
  public function uploadPmmlFiles(){
    if (isset($_SESSION['filesUpload'])){
      unset($_SESSION['filesUpload']);
      
    }
    $view=&$this->getView('UploadPmmlFiles',$this->document->getType());
    $view->assign('categoryId',JRequest::getInt('catid',0));
    $view->display();
  }
  
  /**
   *  Akce pro upload PMML souborů - samotné nahrání
   */     
  public function uploadPmmlFiles2(){
    $categoryId=JRequest::getInt('catid',-1);
    if ($categoryId==-1){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    
    
    if ((isset($_FILES['url']))&&(count($_FILES['url']['tmp_name'])>0)){
      foreach ($_FILES['url']['tmp_name'] as $key=>$fileTmpName) {
      	$fileContent=file_get_contents($fileTmpName);
        $fileName=$_FILES['url']['name'][$key];
        //TODO kontrola obsahu, jestli je ho možné importovat
        $articlesModel=$this->getModel('Articles','sewebarModel');
                        
        $fileContent=iconv('cp1250','UTF-8',$fileContent);
        $articlesModel->newArticle($fileName,$categoryId,false,$fileContent);
      }
    }
    $view=&$this->getView('UploadPmmlFiles2',$this->document->getType());
    $view->display();
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

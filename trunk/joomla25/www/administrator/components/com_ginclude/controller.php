<?php
/**
* @package gInclude
* @author Stanislav Vojíř - xvojs03
* @copyright Stanislav Vojíř, 2009
*
*/

/* ověření, jestli je skript spouštěn v rámci instance joomly a ne samostatně */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */                                     
class GincludeController extends JController
{
  
  /**
   *  Funkce pro akci "smartPage" sloužící pro chytré přesměrování stránky...
   */     
  function smartPage(){
    if ($_SESSION['ginclude']['article']!=-1){
      header('Location: index.php?option=com_ginclude&task=insert&tmpl=component&article='.$_SESSION['ginclude']['article'].'&part='.$_SESSION['ginclude']['part']);  
    }else {
      header('Location: index.php?option=com_ginclude&task=articles&tmpl=component');  
    }
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function getArticle(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		
	  $model=new GincludeModel();  
      echo '<div class="gincludeDiv">';   
      $part=$_REQUEST['part'];
      
      if ($part==""){$part=-1;}   
      echo $model->finalizeGetArticleContent($model->getArticleContent(JRequest::getInt('article',-1),$part),JRequest::getInt('article',-1),$part);
      echo '</div>';
      exit();
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function articles(){
    $_SESSION['ginclude']['article']='-1';
    $_SESSION['ginclude']['part']='-1';
    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'articles.html.php');
		
	  $view = new GincludeViewArticles();
    $view->setModel(new GincludeModel(),true );
	  $view->display();
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function insert(){
    $_SESSION['ginclude']['article']=JRequest::getInt('article',-1);
    if (@$_REQUEST['part']!=''){
      $_SESSION['ginclude']['part']=$_REQUEST['part'];
    }else{
      $_SESSION['gInclude']['part']=-1;
    }
                                 
    require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
    $model=new GincludeModel();
    
    $dbResult=$model->getArticleDB(JRequest::getInt('article',-1));
    if (count($dbResult)==1){
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'insert.html.php');
      $view = new GincludeViewInsert();
      $view->article=$dbResult[0];
      $view->setModel($model,true );
	    $view->display();
    }else {
      $this->articles();
    }
  }    
  
  /**
   *  Funkce pro akci "reload"
   */     
  function reload(){
    $article=JRequest::getInt('article',-1);
    if ($article!=-1){
      /*už jsme vybrali článek, tak zobrazíme to, na co odkazuje"*/
      require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		  require_once (JPATH_COMPONENT.DS.'views'.DS.'reload'.DS.'article.html.php');
	    $view = new GincludeViewArticle();
      $view->setModel(new GincludeModel(),true);
      $view->articleId=$article;
	    $view->display();
    }else {                     
      require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		  require_once (JPATH_COMPONENT.DS.'views'.DS.'reload'.DS.'articles.html.php');
	    $view = new GincludeViewSelectArticle();
      $view->setModel(new GincludeModel(),true);
	    $view->display();
    }
  }
  
  /**
   *  Funkce pro akci "reload"
   */     
  function reloadChangeArticle(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'reload'.DS.'changeArticle.html.php');
	  $view = new GincludeViewChangeArticle();
    $view->setModel(new GincludeModel(),true);
	  $view->display();
  }
  
  /**
   *  Funkce pro akci "reload"
   */     
  function reloadParts(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'reload'.DS.'parts.html.php');
	  $view = new GincludeViewParts();
    $view->setModel(new GincludeModel(),true);
    $view->articleId=JRequest::getInt('article',-1);
	  $view->display();
  }  
  
  /**
   *  Funkce pro aktualizaci článku a jeho uložení
   */     
  function reloadPartsSave(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'ginclude.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'reload'.DS.'message.html.php');
		$model=new GincludeModel();
	  $model->saveParts();

    $view = new GincludeViewMessage();
    $view->title=JText::_('COMPLETED');
    $view->text=JText::_('RELOAD_COMPLETED_MSG');
	  $view->display();
  }
  
  
  function display()
    {
        parent::display();
    }


}
?>
<?php
/**
* @package gInclude
* @author Stanislav Vojíř - xvojs03
* @copyright Stanislav Vojíř, 2011
*
*/

/*TODO: 
    - new BKEF - výběr kategorie
*/

/* ověření, jestli je skript spouštěn v rámci instance joomly a ne samostatně */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
                   
function checknumber($value){
  if (ereg("[0-9]+,[0-9]+", $value)) {
  	return str_replace(",", ".", $value);
  }elseif(ereg("[0-9]+,[0-9]+\\.[0-9]+", $value)) {
  	return str_replace(",", "", $value);
  }elseif((strtoupper($value)=='+INF')||(strtoupper($value)=='INF')){
    return '+INF';
  }elseif(strtoupper($value)=='-INF'){
    return '-INF';
  } return $value;
}

jimport('joomla.application.component.controller');

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */                                     
class BkefController extends JController
{
  /**
   *  Funkce pro spuštění modálního režimu fungování editoru
   */      
  function showModal(){
    $articleId=JRequest::getInt('article',-1);
    $cloneArticle=JRequest::getInt('cloneArticle',1);
    if ($cloneArticle){
      //máme vytvořit klon původního článku a teprve ten upravovat...
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
      $bkefModel=new BkefModel();
      $newArticleId=$bkefModel->cloneArticleDB($articleId);
    }else{
      $newArticleId=$articleId;
    }
    $_SESSION['BKEF']['originalArticle']=$articleId;
    $_SESSION['BKEF']['article']=$newArticleId;
    $_SESSION['BKEF']['showmodal']=1;
    $_SESSION['BKEF']['showstorno']=($articleId!=$newArticleId);
    $_SESSION['BKEF']['modalurl']=$_GET["modalurl"];
    //máme uloženo -> přesměrujeme činnost do činnosti editoru
    $this->_redirect='index.php?option=com_bkef&task=selArticle';
  } 
  
  private function getUserName(){
    $user=& JFactory::getUser();
    return $user->name;
  }
  
  /**
   *  Funkce pro ukončení modálního režimu činnosti - přesměruje činnost na původní komponentu...
   */     
  function endModal(){  ////TODO dokoncit
    if (JRequest::getInt('storno',0)==1){
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
      $bkefModel=new BkefModel();
      $bkefModel->deleteArticle($_SESSION['BKEF']['article']);
    }
  }  
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function getArticle(){  /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'getArticle.html.php');
	  $view = new BkefViewGetArticle();
    $view->setModel(new BkefModel(),true );
	  $view->display();
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function articles(){  /*DONE*/
    $_SESSION['BKEF']['article']='-1';
    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'articles.html.php');
		
	  $view = new BkefViewArticles();
    $view->setModel(new BkefModel(),true );
	  $view->display();
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function insert(){   /*DONE*/
    $_SESSION['BKEF']['article']=JRequest::getInt('article',-1);
    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
    
    $dbResult=$model->getArticleDB(JRequest::getInt('article',-1));
    if (count($dbResult)==1){
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'insert.html.php');
      $view = new BkefViewInsert();
      $view->article=$dbResult[0];
      $view->setModel($model,true );
	    $view->display();
    }else {
      $this->articles();
    }
  }    
  
  /**
   *  Funkce pro akci "selArticle"
   */     
  function selArticle(){  /*DONE*/
    $article=JRequest::getInt('article',-1);
    if ((!$article)&&(isset($_SESSION["BKEF"]['article']))){
      $article=$_SESSION["BKEF"]["article"];
    }
    if ($article!=-1){
      /*už jsme vybrali článek, tak zobrazíme to, na co odkazuje"*/
      $_SESSION["BKEF"]["article"]=$article;
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		  require_once (JPATH_COMPONENT.DS.'views'.DS.'edit'.DS.'article.html.php');
	    $view = new BkefViewArticle();
      $view->setModel(new BkefModel(),true);
      $view->article=$article;
      $view->articleTitle=$view->getModel()->loadTitle($article);
      $view->xml=$view->getModel()->load($article);  
	    $view->display();
    }else {                     
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		  require_once (JPATH_COMPONENT.DS.'views'.DS.'edit'.DS.'articles.html.php');
	    $view = new BkefViewSelectArticle();
      $view->setModel(new BkefModel(),true);
	    $view->display();
    }
  }
  
/**************************************************************************************************************************/
/**ZÁKLADNÍ PRÁCE S METAATTRIBUTY******************************************************************************************/
/**************************************************************************************************************************/
  /**
   * Funkce pro editaci metaatrbutu
   */     
  function newMetaAttribute(){/*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='new'){
      $xml=$model->load(JRequest::getInt('article'));
      if (!isset($xml->MetaAttributes[0])){
        $xml->addChild('MetaAttributes');
      }
      $ma=$xml->MetaAttributes[0]->addChild('MetaAttribute');
      /*projdeme metaatributy a najdeme vsechna jmena a id*/
      $name=(string)JRequest::getString('name');
      $id=0;
      if (count($xml->MetaAttributes[0]->MetaAttribute)>0){
        $namesArr=array();
        foreach ($xml->MetaAttributes[0]->MetaAttribute as $metaAttribute) {
        	$namesArr[]=(string)$metaAttribute->Name[0];
        	$mId=intval($metaAttribute['id']);
        	if ($mId>$id){
            $id=$mId;
          }
        }
        $id++;
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }  
      }else {
        $id=1;
      }
      /**/
      $ma['id']=$id;
      $ma['level']='0';
      
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $ma->addChild('Name',$name);
      
      $created=$ma->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$this->getUserName());
      $lastModified=$ma->addChild('LastModified');
      $lastModified->addChild('Timestamp',date('c'));
      $lastModified->addChild('Author',$this->getUserName());
      
      $ma->addChild('Annotations');
      $annotation=$ma->Annotations[0]->addChild('Annotation');
      $annotation->Created->Timestamp=date('c');
      $annotation->Created->Author=JRequest::getString('annotationAuthor');
      $annotation->LastModified->Timestamp=date('c');
      $annotation->LastModified->Author=JRequest::getString('annotationAuthor');
      $ma->Annotations[0]->Annotation->Text=JRequest::getString('annotation');
      
      $ma->Variability=JRequest::getString('variability');
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=selArticle&article='.JRequest::getInt('article'); 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editMetaAttribute.html.php');
      $view=new BkefViewEditMetaAttribute();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('NEW_BASIC_META');
      $view->potvrzeni='new';
      $view->maId=-1;
      $view->article=JRequest::getInt('article');
      $view->display();
    } 
  } 
  
  /**
   * Funkce pro editaci metaatrbutu
   */     
  function newGroupMetaAttribute(){/*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='new'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $ma=$xml->MetaAttributes[0]->addChild('MetaAttribute');
      /*projdeme metaatributy a najdeme vsechna jmena a id*/
      $name=(string)JRequest::getString('name');
      $id=0;
      if (count($xml->MetaAttributes[0]->MetaAttribute)>0){
        $namesArr=array();
        foreach ($xml->MetaAttributes[0]->MetaAttribute as $metaAttribute) {
        	$namesArr[]=(string)$metaAttribute->Name[0];
        	$mId=intval($metaAttribute['id']);
        	if ($mId>$id){
            $id=$mId;
          }
        }
        $id++;
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }  
      }else {
        $id=1;
      }
      /**/
      $ma['id']=$id;
      $ma['level']='1';
      
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      
      $ma->addChild('Name',$name);
      
      $created=$ma->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$this->getUserName());
      $lastModified=$ma->addChild('LastModified');
      $lastModified->addChild('Timestamp',date('c'));
      $lastModified->addChild('Author',$this->getUserName());
      
      $ma->addChild('Annotations');
      $annotation=$ma->Annotations[0]->addChild('Annotation');
      $annotation->Created->Timestamp=date('c');
      $annotation->Created->Author=JRequest::getString('annotationAuthor');
      $annotation->LastModified->Timestamp=date('c');
      $annotation->LastModified->Author=JRequest::getString('annotationAuthor');
      $ma->Annotations[0]->Annotation->Text=JRequest::getString('annotation');
      
      $ma->Variability=JRequest::getString('variability');
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=selArticle&article='.JRequest::getInt('article'); 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editGroupMetaAttribute.html.php');
      $view=new BkefViewEditMetaAttribute();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('NEW_GROUP_META');
      $view->potvrzeni='new';
      $view->maId=-1;
      $view->article=JRequest::getInt('article');
      $view->display();
    } 
  }    
  
  /**
   * Funkce pro smazání metaatributu
   */     
  function delMetaAttribute(){    /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $dom=dom_import_simplexml($xml->MetaAttributes[0]->MetaAttribute[$maId]);
      $dom->parentNode->removeChild($dom);
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=selArticle&article='.JRequest::getInt('article'); 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delMetaAttribute.html.php');
      $view=new BkefViewDelMetaAttribute();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->articleTitle=$model->loadTitle(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->display();
    }
  }
  
  /**
   *  Smazání ChildMetaAttribute ze skupinového metaattributu...
   */     
  function delChildMeta(){ /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $childId=intval(JRequest::getInt('childId',-1));                                                                            
      $dom=dom_import_simplexml($xml->MetaAttributes[0]->MetaAttribute[$maId]->ChildMetaAttribute[$childId]);
      $dom->parentNode->removeChild($dom);
      //datum posledni aktualizace
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=groupMetaAttribute&article='.JRequest::getInt('article').'&maId='.$maId; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delChildMeta.html.php');
      $view=new BkefViewDelChildMeta();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->childId=JRequest::getInt('childId');
      $view->display();
    }
  }

  /**
   * Funkce pro editaci metaatrbutu
   */     
  function editMetaAttribute(){ /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $metaAttribute->Variability=JRequest::getString('variability');
      /*projdeme metaatributy a najdeme vsechna jmena*/
      $name=trim((string)JRequest::getString('name'));
      $namesArr=array();
      $maIdX=0;
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $metaAttribute) {
      	if ($maIdX!=$maId){
          $namesArr[]=(string)$metaAttribute->Name;
        }
      	$maIdX++;
      }
      $oldName=(string)$xml->MetaAttributes[0]->MetaAttribute[$maId]->Name;
      if ($name!=$oldName){
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }
      } 
      /**/

      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Name[0]=$name;
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=metaAttribute&article='.JRequest::getInt('article').'&maId='.$maId.'#basicInfo'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editMetaAttribute.html.php');
      $view=new BkefViewEditMetaAttribute();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('EDIT_BASIC_META');
      $view->potvrzeni='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->display();
    } 
  }
  
  /**
   * Funkce pro editaci metaatrbutu
   */     
  function editGroupMetaAttribute(){ /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->Variability=JRequest::getString('variability');
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      /*projdeme metaatributy a najdeme vsechna jmena*/
      $name=(string)JRequest::getString('name');
      $namesArr=array();
      $maIdX=0;
      foreach ($xml->MetaAttributes[0]->MetaAttribute as $metaAttribute) {
      	if ($maIdX!=$maId){
          $namesArr[]=(string)$metaAttribute['name'];
        }
      	$maIdX++;
      }
      while (in_array($name,$namesArr)){
        $name=$name.'X';
      }  
      /**/
      $xml->MetaAttributes[0]->MetaAttribute[$maId]['name']=$name;
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=groupMetaAttribute&article='.JRequest::getInt('article').'&maId='.$maId.'#basicInfo'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editGroupMetaAttribute.html.php');
      $view=new BkefViewEditMetaAttribute();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('EDIT_GROUP_META');
      $view->potvrzeni='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->display();
    } 
  }  
    /**
   *  Přidání base.metaatributu do skupinového metaatributu
   */     
  function addChildMeta(){/*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='new'){
      //skutecne vytvorime
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));                             
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$this->getUserName();
      $metaAttribute->addChild('ChildMetaAttribute')->addAttribute('id',intval(JRequest::getInt('childId')));
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$this->getUserName();
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=groupMetaAttribute&article='.JRequest::getInt('article').'&maId='.$maId; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'addChildMeta.html.php');
      $view=new BkefViewAddChildMeta();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=-1;
      $view->display();
    } 
  }
  
  /**
   * Zobrazení jednoho metaatributu
   */     
  function metaAttribute(){/*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    require_once (JPATH_COMPONENT.DS.'views'.DS.'edit'.DS.'metaAttribute.html.php');
    $model=new BkefModel();
    $view=new BkefViewMetaAttribute();
    $view->maId=JRequest::getInt('maId');
    $view->article=JRequest::getInt('article');
    $view->articleTitle=$model->loadTitle($view->article);
    $view->xml=$model->load($view->article);
    $view->display();
  }  
  
  /**
   * Zobrazení jednoho metaatributu
   */     
  function groupMetaAttribute(){/*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    require_once (JPATH_COMPONENT.DS.'views'.DS.'edit'.DS.'groupMetaAttribute.html.php');
    $model=new BkefModel();
    $view=new BkefViewMetaAttribute();

    $view->maId=JRequest::getInt('maId');
    $view->article=JRequest::getInt('article');
    $view->articleTitle=$model->loadTitle($view->article);
    $view->xml=$model->load($view->article);
    $view->display();
  }  
 
/**************************************************************************************************************************/
/**PRÁCE S FORMÁTY*********************************************************************************************************/
/**************************************************************************************************************************/
  /**
   * Zobrazení jednoho formátu
   */     
  function format(){/*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    require_once (JPATH_COMPONENT.DS.'views'.DS.'edit'.DS.'format.html.php');
    $model=new BkefModel();
    $view=new BkefViewFormat();
    $view->maId=JRequest::getInt('maId');
    $view->fId=JRequest::getInt('fId');
    $view->article=JRequest::getInt('article');
    $view->articleTitle=$model->loadTitle($view->article);
    $view->xml=$model->load($view->article);
    $view->display();
  }
  
  /**
   * Funkce pro editaci formátu
   */     
  function newFormat(){/*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='new'){
      //skutecne ukladame
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
                                   
      //$fId=intval(JRequest::getInt('fId',-1)); ///nepotrebne        
      if (!isset($metaAttribute->Formats[0])){
        $metaAttribute->addChild('Formats');
      }            
      
      /*projdeme formaty a najdeme vsechna jmena*/
      $name=(string)JRequest::getString('name');
      if (count($metaAttribute->Formats[0]->Format)>0){
        $namesArr=array();
        foreach ($metaAttribute->Formats[0]->Format as $format) {
        	$namesArr[]=(string)$format->Name[0];
        }
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }  
      }
      /**/
      $format=$metaAttribute->Formats[0]->addChild('Format');
      
      $format->addChild('Name',$name);
      /*info o vytvoreni a anotace*/
      $author=$this->getUserName();
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$this->getUserName();
      //aktualizace lastmodified u metaatributu
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //informace o datu/čase poslední změny
      $created=$format->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$author);
      $lastModified=$format->addChild('LastModified');
      $lastModified->addChild('Timestamp',date('c'));
      $lastModified->addChild('Author',$author);
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //vytvořeni anotace
      $format->addChild('Annotations');
      $annotation=$format->Annotations[0]->addChild('Annotation');
      $annotation->Created->Timestamp=date('c');
      $annotation->Created->Author=JRequest::getString('annotationAuthor');
      $annotation->LastModified->Timestamp=date('c');
      $annotation->LastModified->Author=JRequest::getString('annotationAuthor');
      $annotation->Text=JRequest::getString('annotation');
      /*--info o vytvoreni a anotace*/
      //ulozeni informace o dataType a valueType
      $format->addChild('DataType',JRequest::getString('dataType'));
      $format->addChild('ValueType',JRequest::getString('valueType'));
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=metaAttribute&article='.JRequest::getInt('article').'&maId='.$maId; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editFormat.html.php');
      $view=new BkefViewEditFormat();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->articleTitle=$model->loadTitle(JRequest::getInt('article'));
      $view->h1=JText::_("NEW_FORMAT");
      $view->potvrzeni='new';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=-1;
      $view->showAnnotation=true;
      $view->display();
    } 
  } 
  
  
  /**
   * Funkce pro editaci formátu
   */     
  function editFormat(){  /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      /*projdeme formaty a najdeme vsechna jmena*/
      $name=(string)JRequest::getString('name');
      if (count($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format)>0){
        $namesArr=array();
        $fIdX=0;
        foreach ($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format as $format) {
        	if ($fId!=$fIdX){
            $namesArr[]=(string)$format->Name[0];
          }
        	$fIdX++;
        }
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }  
      }
      /**/
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      $format->Name=$name;
      $format->DataType=JRequest::getString('dataType');
      $format->ValueType=JRequest::getString('valueType');
      //informace o datu/čase poslední změny
      $author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->Header[0]->LastModified[0]->Author=$author;
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$author;
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editFormat.html.php');
      $view=new BkefViewEditFormat();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->articleTitle=$model->loadTitle(JRequest::getInt('article'));
      $view->h1=JText::_('FORMAT_EDITATION');  
      $view->potvrzeni='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId',-1);
      $view->display();
    } 
  } 

  /**
   * Funkce pro smazání formátu
   */     
  function delFormat(){  /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      //smazeme
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]);
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=metaAttribute&article='.JRequest::getInt('article').'&maId='.$maId; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delFormat.html.php');
      $view=new BkefViewDelFormat();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->display();
    } 
  }
  
  /**
   * Funkce pro úpravu Range
   */     
  function editRange(){             /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if (($_POST['potvrzeni']=='interval')||($_POST['potvrzeni']=='enumeration')||($_POST['potvrzeni']=='regex')){
      //skutecne pracujeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      //ulozime cas zmeny
      ///BKEF
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      ///metaattribute
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      ///format
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$this->getUserName();
      
      //najdeme cilene elementy
      if (!isset($format->Collation)){
        $collation=$format->addChild('Collation');
      }else{
        $collation=$format->Collation[0];
      }
      if (!isset($format->Range)){
        $range=$format->addChild('Range');
      }else{
        $range=$format->Range[0];
      }
      //smazeme puvodni hodnoty
      $range['type']='';
      if (count($range->Interval)>0){
        for ($rId=0;$rId<count($range->Interval);$i++) {
        	unset($range->Interval[$rId]);
        }
      }
      if (count($range->Value)>0){
        for ($rId=0;$rId<count($range->Value);$i++) {
        	unset($range->Value[$rId]);
        }
      }
      if (count($range->Regex)>0){
        for ($rId=0;$rId<count($range->Regex);$i++) {
        	unset($range->Regex[$rId]);
        }
      }
      //ulozime hodnoty do Range
      if ($_POST['potvrzeni']=='interval'){
        /*------------------------------------------------------*/
        //ulozime interval
        $range['type']='Interval';
        $interval=$range->addChild('Interval');
        $closure='';
        if (JRequest::getString('leftBoundType')=="closed"){
          $closure.='closed';
        }else{
          $closure.='open';
        }
        if (JRequest::getString('rightBoundType')=="closed"){
          $closure.='Closed';
        }else{
          $closure.='Open';
        }
        $interval['closure']=$closure;
        $interval['leftMargin']=checknumber(JRequest::getString('leftBoundValue'));
        $interval['rightMargin']=checknumber(JRequest::getString('rightBoundValue'));
        /*------------------------------------------------------*/
        /*zkontrolujeme, jestli to neni v rozporu s collation*/
        if (isset($format->Collation[0]))
          if ($format->Collation[0]['type']=='Enumeration'){
            unset($format->Collation[0]);
          }
        /*------------------------------------------------------*/
      }elseif($_POST['potvrzeni']=='regex'){
        $range['type']='Regex';
        $range->addChild('Regex',JRequest::getString('regex'));
      }else{
        /*------------------------------------------------------*/
        //ulozime vycet prvku
        $enumArr=split("\n",JRequest::getString('enumeration'));
        $rangeArr=array();
        $range['type']='Enumeration';
        if (count($enumArr)>0){
          foreach ($enumArr as $value) {
            if (trim($value)!=''){
              $range->addChild('Value',trim($value));
              $rangeArr[]=trim($value);
            }
          }
        }
        /*------------------------------------------------------*/
      }
      
      //ulozime Collation
      $collation['type']=JRequest::getString('collation_type');
      $collation['sense']=JRequest::getString('collation_sense');
      
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#basicSettings'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editRange.html.php');
      $view=new BkefViewEditRange();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('ALLOWED_RANGE_EDITATION');
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId',-1);
      $view->display();
    }   
  }


/**************************************************************************************************************************/
  

  
  
  

  
  /**
   * Funkce pro smazání value z ValueDescription
   */     
  function delVdValue(){    ////TODO - kde se to pouziva?
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      $vdValueId=intval(JRequest::getInt('vdValueId',-1));
      
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      $children=$valueDescription->children();
      unset($children[$vdValueId]);
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions'; 
  }

 
  /**
   * Funkce pro nastaveni equidistant
   */     
  function equidistant(){    /*DONE_X*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //pridame hodnotu
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->EquidistantInterval[0])){
        $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->addChild('EquidistantInterval');
      }
      $equidistant=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->EquidistantInterval[0];
      if (!isset($equidistant->Start)){
        $equidistant->addChild('Start');
      }
      if (!isset($equidistant->End)){
        $equidistant->addChild('End');
      }
      if (!isset($equidistant->Step)){
        $equidistant->addChild('Step');
      }
      //zjistime parametry, pokud jsou prohozene start/end, tak to opravime
      $leftBound=checknumber(JRequest::getString('leftBoundValue'));
      $leftBoundType=JRequest::getString('leftBoundType');
      $rightBound=checknumber(JRequest::getString('rightBoundValue'));
      $rightBoundType=JRequest::getString('rightBoundType');
      if ($leftBound>$rightBound){
        $xBound=$leftBound;
        $xBoundType=$leftBoundType;
        $leftBound=$rightBound;
        $leftBoundType=$rightBoundType;
        $rightBound=$xBound;
        $rightBoundType=$xBoundType;
      }
      $step=checknumber(JRequest::getString('step'));
      $closure=$leftBoundType.ucfirst($rightBoundType);
      //ulozime data
      $equidistant->Start[0]=$leftBound;
      $equidistant->End[0]=$rightBound;
      $equidistant->Step[0]=$step;
      $equidistant['closure']=$closure;
      //aktualizujeme timestampy
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->LastModified[0]->Timestamp=date('c');
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'equidistant.html.php');
      $view=new BkefViewEquidistant();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->bId=JRequest::getInt('bId');
      $view->display();
    } 
       
  }  
    
   /**
   * Funkce pro smazání value description
   */     
  function delValueDescription(){  /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      //smazeme
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId]);
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delValueDescription.html.php');
      $view=new BkefViewDelValueDescription();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->vdId=JRequest::getInt('vdId');
      $view->display();
    } 
  }
   /**
   * Funkce pro úpravu anotace value description
     
  function editValueDescription(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      $valueDescription->Annotation[0]->Text=JRequest::getString('annotation');
      $valueDescription->Annotation[0]->Author=JRequest::getString('annotationAuthor');
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editValueDescription.html.php');
      $view=new BkefViewEditValueDescription();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->vdId=JRequest::getInt('vdId');
      $view->display();
    } 
  }
   */


  /**
   * Funkce pro přidání Bin do IntervalEnumeration
   */     
  function intervalEnumerationAddBin(){   /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='add'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));

      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->IntervalEnumeration[0])){
        $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->addChild('IntervalEnumeration');
      }
      $intervalEnumeration=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->IntervalEnumeration[0];
      $node=$intervalEnumeration->addChild('IntervalBin');
      $node->addChild('Name',JRequest::getString('name'));

      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'intervalEnumerationEditBin.html.php');
      $view=new BkefViewIntervalEnumerationEditBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->potvrzeni="add";
      $view->phId=JRequest::getInt('phId');
      $view->binId=-1;
      $view->display();
    } 
  }
  
  /**
   * Funkce pro úpravu názvu IntervalEnumerationBin
   */     
  function intervalEnumerationEditBin(){   ////TODO
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));

      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      $intervalEnumerationBin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->IntervalEnumeration[0]->IntervalBin[$binId];
      $intervalEnumerationBin->Name[0]=JRequest::getString('name');

      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'intervalEnumerationEditBin.html.php');
      $view=new BkefViewIntervalEnumerationEditBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->potvrzeni="edit";
      $view->phId=JRequest::getInt('phId');
      $view->binId=JRequest::getInt('binId');
      $view->display();
    } 
  }

  /**
   * Funkce pro přidání anotace k formátu
   */     
  function intervalEnumerationDeleteBin(){ 
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      
      //odstranime vybrany BIN
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->IntervalEnumeration[0]->IntervalBin[$binId]);
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'intervalEnumerationDeleteBin.html.php');
      $view=new BkefViewIntervalEnumerationDeleteBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->binId=JRequest::getInt('binId');
      $view->display();
    } 
  }
  
  /**
   * Funkce pro přidání intervalu do IntervalEnumeration Bin
   */     
  function intervalEnumerationAddInterval(){ ////TODO   
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //pridame hodnotu
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      
      $intervalBin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->IntervalEnumeration[0]->IntervalBin[$binId];
      $interval=$intervalBin->addChild('Interval');
      
      $leftBound=checknumber(JRequest::getString('leftBoundValue'));
      $leftBoundType=JRequest::getString('leftBoundType');
      $rightBound=checknumber(JRequest::getString('rightBoundValue'));
      $rightBoundType=JRequest::getString('rightBoundType');
      if ($leftBound>$rightBound){
        $xBound=$rightBound;
        $xBoundType=$rightBoundType;
        $rightBound=$leftBound;
        $rightBoundType=$leftBoundType;
        $leftBound=$xBound;
        $leftBoundType=$xBoundType;
      }
      
      $closure=$leftBoundType.ucfirst($rightBoundType);
      
      $interval->addAttribute('closure',$closure);
      $interval->addAttribute('leftMargin',$leftBound);
      $interval->addAttribute('rightMargin',$rightBound);
      
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'intervalEnumerationAddInterval.html.php');
      $view=new BkefViewIntervalEnumerationAddInterval();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->binId=JRequest::getInt('binId');
      $view->display();
    } 
       
  }
  
     /**
   * Funkce pro smazání value 
   */     
  function intervalEnumerationDeleteInterval(){  /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      $intId=intval(JRequest::getInt('intId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->IntervalEnumeration[0]->IntervalBin[$binId]->Interval[$intId]);
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
  }

   /**
   * Funkce pro úpravu anotace value description
   */     
  function addValueDescription(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne ukladame
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdFeature=JRequest::getString('vdFeature');
      $annotationText=JRequest::getString('annotationText');
      $annotationAuthor=JRequest::getString('annotationAuthor');
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0])){
        $vdNode=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->addChild('ValueDescriptions');
      }
      //pridame novou valueDescription
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->addChild('ValueDescription');
      //ulozime anotaci
      $valueDescription->addChild('Annotations');
      $annotation=$valueDescription->Annotations[0]->addChild('Annotation');
      $annotation->addChild('Created');
      $annotation->Created[0]->addChild('Timestamp',date('c'));
      $annotation->Created[0]->addChild('Author',$annotationAuthor);
      $annotation->addChild('LastModified');
      $annotation->LastModified[0]->addChild('Timestamp',date('c'));
      $annotation->LastModified[0]->addChild('Author',$annotationAuthor);
      $annotation->addChild('Text',$annotationText);
      //dalsi elementy
      $valueDescription->addChild('Scope');
      $valueDescription->addChild('Features');
      $valueDescription->Features[0]->addChild('Feature',(string)$vdFeature);
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$this->getUserName();
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $metaAttribute->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'addValueDescription.html.php');
      $view=new BkefViewAddValueDescription();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->display();
    }                                  
  }
  
  /**
   * Funkce pro smazání jednoho PreprocessingHint
   */     
  function delPreprocessingHint(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]);
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delPreprocessingHint.html.php');
      $view=new BkefViewDelPreprocessingHint();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->display();
    }
  }
 
  /**
   * Funkce pro přidání PreprocessingHint
   */     
  function addPreprocessingHint(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne ukladame
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0])){
        $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->addChild('PreprocessingHints');
      }
      $discretizationHint=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->addChild('DiscretizationHint');
      $discretizationHint->addChild('Name',JRequest::getString('name'));
      //aktualizace data/casu posledni zmeny
      $created=$discretizationHint->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$this->getUserName());
      $modified=$discretizationHint->addChild('LastModified');
      $modified->addChild('Timestamp',date('c'));
      $modified->addChild('Author',$this->getUserName());
      $discretizationHint->addChild('Annotations');
      
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      //typ
      $discretizationHintType=(string)JRequest::getString('type');
      $discretizationHintCreated=$discretizationHint->addChild($discretizationHintType);
      if ($discretizationHingType=='EquidistantInterval'){
        $discretizationHintCreated->addChild('Start');
        $discretizationHintCreated->addChild('End');
        $discretizationHintCreated->addChild('Step');
      }
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'addPreprocessingHint.html.php');
      $view=new BkefViewAddPreprocessingHint();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->display();
    }
  }  

  /**
   * Funkce pro pre PreprocessingHint
   */     
  function renamePreprocessingHint(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne ukladame
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=JRequest::getInt('phId');
      //aktualizace nazvu
      $discretizationHint=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->Name=JRequest::getString('name');
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->LastModified[0]->Timestamp=date('c');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      $dicretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$this->getUserName();
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'renamePreprocessingHint.html.php');
      $view=new BkefViewRenamePreprocessingHint();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->display();
    }
  }    
  
   /**
   * Funkce pro přidání hodnoty do value description
   */     
  function addValueDescriptionValue(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
    if (($_POST['potvrzeni']=='value')||($_POST['potvrzeni']=='regex')||($_POST['potvrzeni']=='interval')){
      //skutecne ukladame
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $valueDescription=$metaAttribute->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      if (!isset($valueDescription->Scope)){
        $scope=$valueDescription->addChild('Scope');
      }else{
        $scope=$valueDescription->Scope[0];
      }
      //ulozime dle zvolene varianty
      if ($_POST['potvrzeni']=='value'){
        //smazeme nadbytecne hodnoty
        while(isset($scope->Interval[0])){unset($scope->Interval[0]);}
        while(isset($scope->Regex[0])){unset($scope->Regex[0]);}
        //pridame hodnotu
        $scope->addChild('Value',checknumber(JRequest::getString('value'))); 
      }elseif($_POST['potvrzeni']=='regex'){
        //smazeme nadbytecne hodnoty
        while(isset($scope->Interval[0])){unset($scope->Interval[0]);}
        while(isset($scope->Value[0])){unset($scope->Value[0]);}
        while(isset($scope->Regex[0])){unset($scope->Regex[0]);}
        //pridame hodnotu
        $scope->addChild('Value',checknumber(JRequest::getString('value')));
      }elseif ($_POST['potvrzeni']=='interval'){
        //smazeme nadbytecne hodnoty
        while(isset($scope->Value[0])){unset($scope->Value[0]);}
        while(isset($scope->Interval[0])){unset($scope->Interval[0]);}
        while(isset($scope->Regex[0])){unset($scope->Regex[0]);}         //TODO doresit moznost vice intervalu
        //pridame hodnotu
        $interval=$valueDescription->addChild('Interval');
        if (JRequest::getString('leftBoundType')=='closed'){
          $intClosure='closed';
        }else{
          $intClosure='open';
        }
        if (JRequest::getString('rightBoundType')=='closed'){
          $intClosure.='Closed';
        }else{
          $intClosure.='Open';
        }
        $interval->addAttribute('closure',$intClosure);
        $interval->addAttribute('leftMargin',checknumber(JRequest::getString('leftBoundValue')));
        $interval->addAttribute('rightMargin',checknumber(JRequest::getString('rightBoundValue')));
      }  
      //aktualizace data/casu posledni zmeny
      $xml->Header[0]->LastModified[0]->Author=$this->getUserName();
      $xml->Header[0]->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$this->getUserName();
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->Formats[0]->Format[$fId]->LastModified[0]->Author=$this->getUserName();
      $metaAttribute->Formats[0]->Format[$fId]->LastModified[0]->Timestamp=date('c');
      //save
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions';
      //--skutecne ukladame
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'addValueDescriptionValue.html.php');
      $view=new BkefViewAddValueDescriptionValue();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->vdId=JRequest::getInt('vdId');
      $view->potvrzeni=JRequest::getString('type');
      $view->display();
    } 
  }    
  

/**
   * Funkce pro přidání anotace k metaattributu
   */     
  function addMetaAttributeAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      //$anId=intval(JRequest::getInt('anId',-1));///nepotrebne
      
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      if (!isset($metaAttribute->Annotations[0])){
        $annotations=$metaAttribute->addChild('Annotations');
      }else {
        $annotations=$metaAttribute->Annotations[0];
      }
      //uprava anotace
      $annotation=$annotations->addChild('Annotation');
      $annotation->addChild('Text',JRequest::getString('annotationText',''));
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      $created=$annotation->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$author);
      $lastModified=$annotation->addChild('LastModified');
      $lastModified->addChild('Timestamp',date('c'));
      $lastModified->addChild('Author',$author);
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=metaAttribute&article='.JRequest::getInt('article').'&maId='.$maId.'#annotations'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editMetaAttributeAnnotation.html.php');
      $view=new BkefViewEditMetaAttributeAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('NEW_ANNOTATION'); 
      $view->akce='add';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->anId=-1;
      $view->display();
    } 
  }
  
  /**
   * Funkce pro přidání anotace k formátu
   */     
  function addFormatAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      //$anId=intval(JRequest::getInt('anId',-1));///nepotrebne
      
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      if (!isset($format->Annotations[0])){
        $annotations=$format->addChild('Annotations');
      }else {
        $annotations=$format->Annotations[0];
      }
      //uprava anotace
      $annotation=$annotations->addChild('Annotation');
      $annotation->addChild('Text',JRequest::getString('annotationText',''));
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      $created=$annotation->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$author);
      $lastModified=$annotation->addChild('LastModified');
      $lastModified->addChild('Timestamp',date('c'));
      $lastModified->addChild('Author',$author);
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editFormatAnnotation.html.php');
      $view=new BkefViewEditFormatAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('NEW_ANNOTATION'); 
      $view->akce='add';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->anId=-1;
      $view->display();
    } 
  }
  
  /**
   * Funkce pro přidání anotace k preprocessing hintu
   */     
  function addPreprocessingHintAnnotation(){ 
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      
      //$anId=intval(JRequest::getInt('anId',-1));///nepotrebne
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      //samotna anotace
      if (!isset($discretizationHint->Annotations[0])){
        $annotations=$discretizationHint->addChild('Annotations');
      }else {
        $annotations=$discretizationHint->Annotations[0];
      }
      //uprava anotace
      $annotation=$annotations->addChild('Annotation');
      $annotation->addChild('Text',JRequest::getString('annotationText',''));
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      $created=$annotation->addChild('Created');
      $created->addChild('Timestamp',date('c'));
      $created->addChild('Author',$author);
      $lastModified=$annotation->addChild('LastModified');
      $lastModified->addChild('Timestamp',date('c'));
      $lastModified->addChild('Author',$author);
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editPreprocessingHintAnnotation.html.php');
      $view=new BkefViewEditPreprocessingHintAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('NEW_ANNOTATION'); 
      $view->akce='add';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->anId=-1;
      $view->display();
    } 
  }    
  
  /**
   * Funkce pro editaci anotace u formátu
   */     
  function editMetaAttributeAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      //uprava anotace
      $annotation=$format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotations[0]->Annotation[$anId];
      $annotation->Text=JRequest::getString('annotationText','');
      $annotation->LastModified[0]->Timestamp=date('c');
      $annotation->LastModified[0]->Author=$author;
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$this->getUserName();
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {                                                                        
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editMetaAttributeAnnotation.html.php');
      $view=new BkefViewEditMetaAttributeAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));    
      $view->h1=JText::_('EDIT_ANNOTATION');
      $view->akce='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->anId=JRequest::getInt('anId');       
      $view->display();
    } 
  }

   /**
   * Funkce pro editaci anotace u formátu
   */     
  function editFormatAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      //uprava anotace
      $annotation=$format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Annotations[0]->Annotation[$anId];
      $annotation->Text=JRequest::getString('annotationText','');
      $annotation->LastModified[0]->Timestamp=date('c');
      $annotation->LastModified[0]->Author=$author;
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$this->getUserName();
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {                                                                        
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editFormatAnnotation.html.php');
      $view=new BkefViewEditFormatAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));    
      $view->h1=JText::_('EDIT_ANNOTATION');
      $view->akce='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->anId=JRequest::getInt('anId');       
      $view->display();
    } 
  } 

   /**
   * Funkce pro editaci anotace u formátu
   */     
  function editPreprocessingHintAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      $author=JRequest::getString('annotationAuthor',$this->getUserName());
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$this->getUserName();
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      //uprava anotace
      $annotation=$discretizationHint->Annotations[0]->Annotation[$anId];
      $annotation->Text=JRequest::getString('annotationText','');
      $annotation->LastModified[0]->Timestamp=date('c');
      $annotation->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editPreprocessingHintAnnotation.html.php');
      $view=new BkefViewEditPreprocessingHintAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('EDIT_ANNOTATION');
      $view->akce='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->anId=JRequest::getInt('anId');
      $view->display();
    } 
  } 
  
  /**
   * Funkce pro smazání anotace u formátu
   */     
  function delMetaAttributeAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      //smazani
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotations[0]->Annotation[$anId]);
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=metaAttribute&article='.JRequest::getInt('article').'&maId='.$maId.'#annotations'; 
    }else {                                            
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delMetaAttributeAnnotation.html.php');
      $view=new BkefViewDelMetaAttributeAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->anId=JRequest::getInt('anId');
      $view->display();
    } 
  } 
  
   /**
   * Funkce pro smazání anotace u formátu
   */     
  function delFormatAnnotation(){ /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      //smazani
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Annotations[0]->Annotation[$anId]);
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {                                            
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delFormatAnnotation.html.php');
      $view=new BkefViewDelFormatAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->anId=JRequest::getInt('anId');
      $view->display();
    } 
  } 

   /**
   * Funkce pro smazání anotace u formátu
   */     
  function delPreprocessingHintAnnotation(){ 
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      $author=$this->getUserName();
      //aktualizace BKEF lastmodified
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;       
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization (preprocessing) hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      //smazani
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->Annotations[0]->Annotation[$anId]);
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delPreprocessingHintAnnotation.html.php');
      $view=new BkefViewDelPreprocessingHintAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->anId=JRequest::getInt('anId');
      $view->display();
    } 
  }      


  
    

  function display(){
    parent::display();
  }
  
  /**
   * Funkce pro vytvoření nového BKEF článku
   */     
  function newArticle(){/*DONE*/
    $db = & JFactory::getDBO();
    if ($_POST['articleName']!=''){
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
      $model=new BkefModel();
      $articleName=$_POST['articleName'];
      $articleContent=trim($_POST['articleContent']);
      $sectionString=JRequest::getString('articleSection','-1');
      if ($sectionString!=-1){
        //vyhodnotime sekci a kategorii
        $sectionArr=split("_",$sectionString);
        $articleSection=$sectionArr[0];
        $articleCategory=$sectionArr[1];
      }else{
        //neurceno
        $articleSection=-1;
        $articleCategory=-1;
      }
      $articleState=$article=JRequest::getInt('articleState',1);
      if ($articleContent==''){
        $articleContent='<'.'?xml version="1.0" encoding="UTF-8"?'.'>
                         <'.'?xml-stylesheet type="text/xsl" href="bkef-styl.xsl"?'.'>
                         <BKEFData xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://keg.vse.cz/bkef_data http://sewebar.vse.cz/schemas/BKEF1_1_Data.xsd" xmlns="http://keg.vse.cz/bkef_data">
                           <Header>
                             <Application name="BKEF Editor" version="1.1"/>
                             <Title>'.htmlentities($articleName).'</Title>
                             <Created>
                               <Timestamp>'.date('c').'</Timestamp>
                               <Author>'.$this->getUserName().'</Author>
                             </Created>
                             <LastModified>
                               <Timestamp>'.date('c').'</Timestamp>
                               <Author>'.$this->getUserName().'</Author>
                             </LastModified>
                           </Header>
                           <MetaAttributes></MetaAttributes>
                         </BKEFData>';
      }  
      $model->newArticle($articleName,$articleContent,$articleState,$articleSection,$articleCategory);
      $this->_redirect='index.php?option=com_bkef&task=selArticle';
    }else {
      require_once (JPATH_COMPONENT.DS.'views'.DS.'edit'.DS.'newArticle.html.php');
      $view=new BkefViewNewArticle();   
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
      $view->setModel(new BkefModel(),true );        
      $view->display();
    }
  }
  

  /**
   * Funkce pro přidání hodnoty do nominal enumeration
   */     
  function nominalEnumerationAddValue(){ /*DONE*/      
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='add'){      
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      $value=JRequest::getString('value','');
      
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $nominalEnumerationBin=$discretizationHint->NominalEnumeration[0]->NominalBin[$binId];
      
      //kontrola,jestli dana hodnota zatim neni v zadanych hodnotach v PH
      $phValues=array();
      foreach ($discretizationHint->NominalEnumeration[0]->NominalBin as $nominalBinX) {
      	if (count($nominalBinX->Value)>0){
          foreach ($nominalBinX->Value as $valueX) {
          	$phValues[]=(string)$valueX;
          }
        }       
      }                      
      if (!in_array($value,$phValues)){
        //mame ukladat...
        $nominalEnumerationBin->addChild('Value',$value);
        //aktualizace BKEF lastmodified
        $author=$this->getUserName();
        $xmlLastModified=$xml->Header[0]->LastModified[0];
        $xmlLastModified->Timestamp=date('c');
        $xmlLastModified->Author=$author;
        //aktualizace lastmodified u metaatributu
        $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
        $metaAttribute->LastModified[0]->Timestamp=date('c');
        $metaAttribute->LastModified[0]->Author=$author;
        //aktualizace lastmodified u formatu
        $format=$metaAttribute->Formats[0]->Format[$fId];
        $format->LastModified[0]->Timestamp=date('c');
        $format->LastModified[0]->Author=$author;
        //aktualizace lastmodified u discretization hintu
        $discretizationHint->LastModified[0]->Timestamp=date('c');
        $discretizationHint->LastModified[0]->Author=$author;
      }
      
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'nominalEnumerationEditValue.html.php');
      $view=new BkefViewNominalEnumerationEditValue();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('ADD_VALUE'); 
      $view->potvrzeni='add';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->binId=JRequest::getInt('binId');
      $view->display();
    } 
  }  
  
  /**
   * Funkce pro přidání anotace k formátu
   */     
  function nominalEnumerationDeleteBin(){ 
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      
      //odstranime vybrany BIN
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->NominalEnumeration[0]->NominalBin[$binId]);
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'nominalEnumerationDeleteBin.html.php');
      $view=new BkefViewNominalEnumerationDeleteBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->binId=JRequest::getInt('binId');
      $view->display();
    } 
  }
  
  /**
   * Funkce pro přidání anotace k formátu
   */     
  function nominalEnumerationAddBin(){      
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){    
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      $name=JRequest::getString('name','NEW');
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      if (!isset($discretizationHint->NominalEnumeration)){
        $nominalEnumeration=$discretizationHint->addChild('NominalEnumeration');
      }else{
        $nominalEnumeration=$discretizationHint->NominalEnumeration[0];
      }
      //vyreseni unikatnosti jmena
      if (count(@$nominalEnumeration->NominalBin)>0){
        $namesArr=array();
        foreach ($nominalEnumeration->NominalBin as $nominalBinX) {
        	$namesArr[]=(string)$nominalBinX->Name[0];
        }
        while (in_array($name,$namesArr)) {
        	$name.='X';
        }
      }
      $nominalBin=$nominalEnumeration->addChild('NominalBin');
      $nominalBin->addChild('Name',$name);
      
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'nominalEnumerationEditBin.html.php');
      $view=new BkefViewNominalEnumerationEditBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->action='add';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->binId=-1;
      $view->display();
    } 
  }
  
  /**
   * Funkce pro přidání anotace k formátu
   */     
  function nominalEnumerationEditBin(){      
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){    
      //skutecne mame ukladat
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      $name=JRequest::getString('name','NEW');
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      if (!isset($discretizationHint->NominalEnumeration)){
        $nominalEnumeration=$discretizationHint->addChild('NominalEnumeration');
      }else{
        $nominalEnumeration=$discretizationHint->NominalEnumeration[0];
      }
      //vyreseni unikatnosti jmena
      if (count(@$nominalEnumeration->NominalBin)>0){
        $namesArr=array();
        $binIdX=0;
        foreach ($nominalEnumeration->NominalBin as $nominalBinX) {
          if ($binId!=$binIdX){
            $namesArr[]=(string)$nominalBinX->Name[0];
          } 
          $binIdX++;
        }
        while (in_array($name,$namesArr)) {
        	$name.='X';
        }
      }
      $nominalBin=$nominalEnumeration->NominalBin[$binId];
      $nominalBin->Name=$name;
      
      //ulozeni zmen
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'nominalEnumerationEditBin.html.php');
      $view=new BkefViewNominalEnumerationEditBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->action='edit';
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->binId=JRequest::getInt('binId');
      $view->display();
    } 
  }    
  
   /**
   * Funkce pro smazání value 
   */     
  function nominalEnumerationDeleteValue(){  /*DONE*/
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $binId=intval(JRequest::getInt('binId',-1));
      $vId=intval(JRequest::getInt('vId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->NominalEnumeration[0]->NominalBin[$binId]->Value[$vId]);
      
      //aktualizace BKEF lastmodified
      $author=$this->getUserName();
      $xmlLastModified=$xml->Header[0]->LastModified[0];
      $xmlLastModified->Timestamp=date('c');
      $xmlLastModified->Author=$author;
      //aktualizace lastmodified u metaatributu
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      $metaAttribute->LastModified[0]->Timestamp=date('c');
      $metaAttribute->LastModified[0]->Author=$author;
      //aktualizace lastmodified u formatu
      $format=$metaAttribute->Formats[0]->Format[$fId];
      $format->LastModified[0]->Timestamp=date('c');
      $format->LastModified[0]->Author=$author;
      //aktualizace lastmodified u discretization hintu
      $discretizationHint=$format->PreprocessingHints[0]->DiscretizationHint[$phId];
      $discretizationHint->LastModified[0]->Timestamp=date('c');
      $discretizationHint->LastModified[0]->Author=$author;
      
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
  }
  


  /****************************************************************************/
  /****************************************************************************/
}
?>

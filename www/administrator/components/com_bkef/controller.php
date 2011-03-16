<?php
/**
* @package gInclude
* @author Stanislav Vojíř - xvojs03
* @copyright Stanislav Vojíř, 2009
*
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
   *  Funkce pro akci "getArticle"
   */     
  function getArticle(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'getArticle.html.php');
	  $view = new BkefViewGetArticle();
    $view->setModel(new BkefModel(),true );
	  $view->display();
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function articles(){
    $_SESSION['ginclude']['article']='-1';
    $_SESSION['ginclude']['part']='-1';
    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'articles.html.php');
		
	  $view = new BkefViewArticles();
    $view->setModel(new BkefModel(),true );
	  $view->display();
  }
  
  /**
   *  Funkce pro akci "getArticle"
   */     
  function insert(){
    $_SESSION['ginclude']['article']=JRequest::getInt('article',-1);
    $_SESSION['ginclude']['part']=JRequest::getVar('part','-1');
    
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
  function selArticle(){
    $article=JRequest::getInt('article',-1);
    if ($article!=-1){
      /*už jsme vybrali článek, tak zobrazíme to, na co odkazuje"*/
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
  
  /**
   * Funkce pro smazání metaatributu
   */     
  function delMetaAttribute(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
                                                                              // unset($xml->MetaAttributes[0]->MetaAttribute[$maId]);
      $dom=dom_import_simplexml($xml->MetaAttributes[0]->MetaAttribute[$maId]);
      $dom->parentNode->removeChild($dom);
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
  
  function delChildMeta(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $childId=intval(JRequest::getInt('childId',-1));                                                                            
      $dom=dom_import_simplexml($xml->MetaAttributes[0]->MetaAttribute[$maId]->ChildMetaAttribute[$childId]);
      $dom->parentNode->removeChild($dom);
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
   * Funkce pro smazání formátu
   */     
  function delFormat(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]);
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
   * Funkce pro smazání value z ValueDescription
   */     
  function delVdValue(){
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
   * Funkce pro smazání value 
   */     
  function delExhaustiveEnumerationBinValue(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $vId=intval(JRequest::getInt('vId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0]->Bin[$bId]->Value[$vId]);
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
  }
 
  /**
   * Funkce pro smazání value 
   */     
  function delIntervalEnumerationBinValue(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $vId=intval(JRequest::getInt('vId',-1));
      $children=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->IntervalEnumeration[0]->IntervalBin[$bId]->children();
      unset($children[$vId]);
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
  }
    
  /**
   * Funkce pro smazání value z ExhaustiveEnumeration Bin
   */     
  function delExhaustiveEnumerationBin(){    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $children=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0]->children();
      unset($children[$bId]);
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delExhaustiveEnumerationBin.html.php');
      $view=new BkefViewDelExhaustiveEnumerationBin();           
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
   * Funkce pro smazání value z ExhaustiveEnumeration Bin
   */     
  function delIntervalEnumerationBin(){    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->IntervalEnumeration[0]->IntervalBin[$bId]);
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delIntervalEnumerationBin.html.php');
      $view=new BkefViewDelExhaustiveEnumerationBin();           
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
   * Funkce pro přidání value do ExhaustiveEnumeration Bin
   */     
  function addExhaustiveEnumerationBinValue(){    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //pridame hodnotu
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0]->Bin[$bId]->addChild('Value',checknumber(JRequest::getString('value')));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'addExhaustiveEnumerationBinValue.html.php');
      $view=new BkefViewAddExhaustiveEnumerationBinValue();           
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
   * Funkce pro přidání value do ExhaustiveEnumeration Bin
   */     
  function addIntervalEnumerationBinValue(){    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //pridame hodnotu
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $interval=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->IntervalEnumeration[0]->IntervalBin[$bId]->addChild('Interval');
      $lInterval=$interval->addChild('LeftBound');
      $lInterval->addAttribute('type',JRequest::getString('leftBoundType'));
      $lInterval->addAttribute('value',checknumber(JRequest::getString('leftBoundValue')));
      $rInterval=$interval->addChild('RightBound');
      $rInterval->addAttribute('type',JRequest::getString('rightBoundType'));
      $rInterval->addAttribute('value',checknumber(JRequest::getString('rightBoundValue')));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'addIntervalEnumerationBinValue.html.php');
      $view=new BkefViewAddIntervalEnumerationBinValue();           
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
   * Funkce pro přidání value do ExhaustiveEnumeration Bin
   */     
  function equidistant(){    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //pridame hodnotu
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->Equidistant[0])){
        $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->addChild('Equidistant');
      }
      $equidistant=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->Equidistant[0];
      if (!isset($equidistant->Start)){
        $equidistant->addChild('Start');
      }
      if (!isset($equidistant->End)){
        $equidistant->addChild('End');
      }
      if (!isset($equidistant->Step)){
        $equidistant->addChild('Step');
      }
      $equidistant->Start[0]['type']=JRequest::getString('leftBoundType');
      $equidistant->Start[0]=checknumber(JRequest::getString('leftBoundValue'));
      $equidistant->End[0]['type']=JRequest::getString('rightBoundType');
      $equidistant->End[0]=checknumber(JRequest::getString('rightBoundValue'));
      $equidistant->Step[0]=checknumber(JRequest::getString('step'));
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
   * Funkce pro smazání formátu
   */     
  function delValueDescription(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId]);
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
   */     
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

  /**
   * Funkce pro nastavení value do EquifrequentInterval
   */     
  function editEquifrequentInterval(){    
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //pridame hodnotu
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $equifrequentInterval=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->EquifrequentInterval[0];
      if (!isset($equifrequentInterval->Value[0])){
        $equifrequentInterval->addChild('Count',checknumber(JRequest::getString('count')));
      }else {
        $equifrequentInterval->Count[0]=checknumber(JRequest::getString('count'));
      }
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editEquifrequentInterval.html.php');
      $view=new BkefViewEditEquifrequentInterval();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->phId=JRequest::getInt('phId');
      $view->display();
    } 
       
  }

   /**
   * Funkce pro přidání Bin do ExhaustiveEnumeration
   */     
  function exhaustiveEnumerationAddBin(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='add'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $exhaustiveEnumeration=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0];
      $node=$exhaustiveEnumeration->addChild('Bin');
      $node->addAttribute('name',JRequest::getString('name'));
      $annotation=$node->addChild('Annotation');
      $annotation->addChild('Text',JRequest::getString('annotationText'));
      $annotation->addChild('Author',JRequest::getString('annotationAuthor'));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'exhaustiveEnumerationAddBin.html.php');
      $view=new BkefViewExhaustiveEnumerationAddBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->potvrzeni="add";
      $view->phId=JRequest::getInt('phId');
      $view->bId=-1;
      $view->display();
    } 
  }

   /**
   * Funkce pro přidání Bin do ExhaustiveEnumeration
   */     
  function intervalEnumerationAddBin(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='add'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $exhaustiveEnumeration=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->IntervalEnumeration[0];
      $node=$exhaustiveEnumeration->addChild('IntervalBin');
      $node->addAttribute('name',JRequest::getString('name'));
      $annotation=$node->addChild('Annotation');
      $annotation->addChild('Text',JRequest::getString('annotationText'));
      $annotation->addChild('Author',JRequest::getString('annotationAuthor'));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'intervalEnumerationAddBin.html.php');
      $view=new BkefViewIntervalEnumerationAddBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->potvrzeni="add";
      $view->phId=JRequest::getInt('phId');
      $view->bId=-1;
      $view->display();
    } 
  }
     /**
   * Funkce pro přidání Bin do ExhaustiveEnumeration
   */     
  function exhaustiveEnumerationEditBin(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $bin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0]->Bin[$bId];
      
      $bin['name']=JRequest::getString('name');
      if (!$bin->Annotation){
        $annotation=$bin->addChild('Annotation');
        $annotation->addChild('Text');
        $annotation->addChild('Author');
      }
      $bin->Annotation[0]->Text[0]=JRequest::getString('annotationText');
      $bin->Annotation[0]->Author[0]=JRequest::getString('annotationAuthor');
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'exhaustiveEnumerationAddBin.html.php');
      $view=new BkefViewExhaustiveEnumerationAddBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->potvrzeni="edit";
      $view->phId=JRequest::getInt('phId');
      $view->bId=JRequest::getInt('bId');
      $view->display();
    } 
  }

  /**
   * Funkce pro přidání Bin do ExhaustiveEnumeration
   */     
  function intervalEnumerationEditBin(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //ulozime
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=intval(JRequest::getInt('phId',-1));
      $bId=intval(JRequest::getInt('bId',-1));
      $bin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->IntervalEnumeration[0]->IntervalBin[$bId];
      
      $bin['name']=JRequest::getString('name');
      if (!$bin->Annotation){
        $annotation=$bin->addChild('Annotation');
        $annotation->addChild('Text');
        $annotation->addChild('Author');
      }
      $bin->Annotation[0]->Text[0]=JRequest::getString('annotationText');
      $bin->Annotation[0]->Author[0]=JRequest::getString('annotationAuthor');
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#preprocessingHints'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'intervalEnumerationAddBin.html.php');
      $view=new BkefViewIntervalEnumerationAddBin();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->potvrzeni="edit";
      $view->phId=JRequest::getInt('phId');
      $view->bId=JRequest::getInt('bId');
      $view->display();
    } 
  }  

   /**
   * Funkce pro úpravu anotace value description
   */     
  function addValueDescription(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vd=JRequest::getString('vd');
      $annotationText=JRequest::getString('annotationText');
      $annotationAuthor=JRequest::getString('annotationAuthor');
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0])){
        $vdNode=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->addChild('ValueDescriptions');
      }
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->addChild('ValueDescription');
     // exit(var_dump($valueDescription));
      $valueDescription['type']=(string)$vd;
      $valueDescription->addChild('Annotation');
      $valueDescription->Annotation[0]->addChild('Text',$annotationText);
      $valueDescription->Annotation[0]->addChild('Author',$annotationAuthor);
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
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]);
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
  function addPreprocessingHint(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0])){
        $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->addChild('PreprocessingHints');
      }
      $preprocessingHint=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->addChild('PreprocessingHint');
      $preprocessingHint['name']=JRequest::getString('name');
      /*if (JRequest::getString('type')=="IntervalEnumeration"){
        $preprocessingHint->addChild('DiscretizationHint')->addChild('IntervalEnumeration');
        $preprocessingHint->addChild('DiscretizationHint')->addChild('Equidistant');
      }else {  */
        $preprocessingHint->addChild('DiscretizationHint')->addChild((string)JRequest::getString('type'));
      /*}*/
      //exit(var_dump($preprocessingHint));
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
   * Funkce pro přidání PreprocessingHint
   */     
  function renamePreprocessingHint(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']==1){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $phId=JRequest::getInt('phId');
      $preprocessingHint=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId];
      $preprocessingHint['name']=JRequest::getString('name');
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
   * Funkce pro úpravu anotace value description
   */     
  function addValueDescriptionValue(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='value'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      $valueDescription->addChild('Value',checknumber(JRequest::getString('value')));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions'; 
    }elseif ($_POST['potvrzeni']=='interval'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $vdId=intval(JRequest::getInt('vdId',-1));
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      $interval=$valueDescription->addChild('Interval');
      $leftBound=$interval->addChild('LeftBound');
      $leftBound['type']=JRequest::getString('leftBoundType');
      $leftBound['value']=checknumber(JRequest::getString('leftBoundValue'));
      $rightBound=$interval->addChild('RightBound');
      $rightBound['type']=JRequest::getString('rightBoundType');
      $rightBound['value']=checknumber(JRequest::getString('rightBoundValue'));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#valueDescriptions'; 
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
   * Funkce pro editaci metaatrbutu
   */     
  function editMetaAttribute(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Variability=JRequest::getString('variability');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotation->Text=JRequest::getString('annotation');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotation->Author=JRequest::getString('annotationAuthor');
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
      $oldName=$xml->MetaAttributes[0]->MetaAttribute[$maId]['name'];
      /*kontrola influences*/
      if (count($xml->Influences[0]->Influence)>0)
        foreach ($xml->Influences[0]->Influence as $influence) {
        	foreach ($influence->MetaAttribute as $ma) {
        	  echo '<br /><br />porovnavam:'.$ma['name'].' '.$oldName;
         	  if ($ma['name'].''==$oldName.''){
              $ma['name']=$name; 
            }
          }
        }
      /*--kontrola influences*/
      $xml->MetaAttributes[0]->MetaAttribute[$maId]['name']=$name;
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
  function editGroupMetaAttribute(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='edit'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Variability=JRequest::getString('variability');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotation->Text=JRequest::getString('annotation');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotation->Author=JRequest::getString('annotationAuthor');
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
   * Funkce pro nastavení collation enumeration
   */     
  function editCollationEnumeration(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_GET['potvrzeni']=='moveUp'){
      $article=intval(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId'));
      $fId=intval(JRequest::getInt('fId'));
      $vId=intval(JRequest::getInt('vId'));
      $xml=$model->load($article);
      $collation=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Collation[0];
      if (isset($collation->Value[$vId])&&isset($collation->Value[$vId-1])){
        $hodnota1=(string)$collation->Value[$vId];
        $hodnota2=(string)$collation->Value[$vId-1];
        
        $collation->Value[$vId]=$hodnota2;
        $collation->Value[$vId-1]=$hodnota1;
        $model->save(JRequest::getInt('article'),$xml->asXML());
      }
      $this->_redirect='index.php?option=com_bkef&task=editCollationEnumeration&article='.JRequest::getInt('article').'&tmpl=component&maId='.$maId.'&fId='.$fId; 
    }elseif ($_GET['potvrzeni']=='moveDown'){
      $article=intval(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId'));
      $fId=intval(JRequest::getInt('fId'));
      $vId=intval(JRequest::getInt('vId'));
      
      $xml=$model->load(JRequest::getInt('article'));
      $collation=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Collation[0];
      if (isset($collation->Value[$vId])&&isset($collation->Value[$vId+1])){
        $hodnota1=(string)$collation->Value[$vId];
        $hodnota2=(string)$collation->Value[$vId+1];
        
        $collation->Value[$vId]=$hodnota2;
        $collation->Value[$vId+1]=$hodnota1;
        $model->save(JRequest::getInt('article'),$xml->asXML());
      }
      $this->_redirect='index.php?option=com_bkef&task=editCollationEnumeration&article='.JRequest::getInt('article').'&tmpl=component&maId='.$maId.'&fId='.$fId; 
    }elseif ($_POST['potvrzeni']=='1'){
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.JRequest::getInt('maId').'&fId='.JRequest::getInt('fId').'#basicInfo'; 
    }else {             
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editCollationEnumeration.html.php');
      $view=new BkefViewEditCollationEnumeration();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId');
      $view->display();
    } 
  } 
  
  /**
   * Funkce pro editaci metaatrbutu
   */     
  function newMetaAttribute(){
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
        	$namesArr[]=(string)$metaAttribute['name'];
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
      $ma['name']=$name;
      $ma['id']=$id;
      $ma['level']='0';
      $ma->Variability=JRequest::getString('variability');
      $ma->Annotation->Text=JRequest::getString('annotation');
      $ma->Annotation->Author=JRequest::getString('annotationAuthor');
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
  function newGroupMetaAttribute(){
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
        	$namesArr[]=(string)$metaAttribute['name'];
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
      $ma['name']=$name;
      $ma['id']=$id;
      $ma['level']='1';
      $ma->Variability=JRequest::getString('variability');
      $ma->Annotation->Text=JRequest::getString('annotation');
      $ma->Annotation->Author=JRequest::getString('annotationAuthor');
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
   * Funkce pro editaci formátu
   */     
  function editFormat(){
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
            $namesArr[]=(string)$format['name'];
          }
        	$fIdX++;
        }
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }  
      }
      /**/
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]['name']=$name;
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Author=JRequest::getString('author');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->DataType=JRequest::getString('dataType');
      $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueType=JRequest::getString('valueType');
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
   * Funkce pro přidání anotace k formátu
   */     
  function addAnnotation(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();         
		if ($_POST['potvrzeni']=='1'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      if (!isset($format->Annotations[0])){
        $annotations=$format->addChild('Annotations');
      }else {
        $annotations=$format->Annotations[0];
      }
      $annotation=$annotations->addChild('Annotation');
      $annotation->addChild('Author',JRequest::getString('annotationAuthor',''));
      $annotation->addChild('Text',JRequest::getString('annotationText',''));
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editAnnotation.html.php');
      $view=new BkefViewEditAnnotation();           
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
   * Funkce pro editaci anotace u formátu
   */     
  function editAnnotation(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      
      $annotation=$format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Annotations[0]->Annotation[$anId];
      $annotation->Author[0]=JRequest::getString('annotationAuthor','');
      $annotation->Text[0]=JRequest::getString('annotationText','');
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {             
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editAnnotation.html.php');
      $view=new BkefViewEditAnnotation();           
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
   * Funkce pro smazání anotace u formátu
   */     
  function delAnnotation(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $anId=intval(JRequest::getInt('anId',-1));
      
      unset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Annotations[0]->Annotation[$anId]);
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#annotations'; 
    }else {             
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'delAnnotation.html.php');
      $view=new BkefViewDelAnnotation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->anId=JRequest::getInt('anId');
      $view->display();
    } 
  }    

  /**
   * Funkce pro nastavení řazení
   */     
  function editCollation(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='1'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      
      $type=JRequest::getString('type');
      $sense=JRequest::getString('sense');
      if (!isset($format->Collation[0])){
        $format->addChild('Collation');
      }
      
      if (($type=='Enumeration')&&($format->Collation[0]['type']!='Enumeration')){
        //musime zkopirovat hodnoty...
        $format->Collation[0]['type']=$type;
        $format->Collation[0]['sense']=$sense;
        if (count($format->AllowedRange[0]->Enumeration[0]->Value)>0){
          foreach ($format->AllowedRange[0]->Enumeration[0]->Value as $value) {
          	$format->Collation[0]->addChild('Value',(string)$value);
          }
        }
      }else {
          $format->Collation[0]['sense']=$sense;
        if ($type=='Enumeration'){
          $enArr=split("\n", trim(JRequest::getString('enumeration')));
        }else {
          $format->Collation[0]['type']=$type;
        }
      }
      $model->save(JRequest::getInt('article'),$xml->asXML());
      $this->_redirect='index.php?option=com_bkef&task=format&article='.JRequest::getInt('article').'&maId='.$maId.'&fId='.$fId.'#basicInfo'; 
    }else {             
      //zobrazime dotaz
      require_once (JPATH_COMPONENT.DS.'views'.DS.'iframe'.DS.'editCollation.html.php');
      $view=new BkefViewEditCollation();           
      $view->xml=$model->load(JRequest::getInt('article'));
      $view->h1=JText::_('FORMAT_EDITATION_COLLATION');
      $view->article=JRequest::getInt('article');
      $view->maId=JRequest::getInt('maId');
      $view->fId=JRequest::getInt('fId',-1);
      $view->display();
    } 
  }
  
  /**
   * Funkce pro úpravu AllowedRange
   */     
  function editRange(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if (($_POST['potvrzeni']=='interval')||($_POST['potvrzeni']=='enumeration')){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));
      $fId=intval(JRequest::getInt('fId',-1));
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      //smazeme puvodni hodnoty
      $allowedRange=$format->AllowedRange[0];
      unset($allowedRange->Interval[0]);
      unset($allowedRange->Enumeration[0]);
      unset($allowedRange);
      $format->addChild('AllowedRange');
      $allowedRange=$format->AllowedRange[0];
      
      if ($_POST['potvrzeni']=='interval'){
        /*------------------------------------------------------*/
        //ulozime interval
        $interval=$allowedRange->addChild('Interval');
        $leftBound=$interval->addChild('LeftBound');
        $leftBound['type']=JRequest::getString('leftBoundType');
        $leftBound['value']=checknumber(JRequest::getString('leftBoundValue'));
        $rightBound=$interval->addChild('RightBound');
        $rightBound['type']=JRequest::getString('rightBoundType');
        $rightBound['value']=checknumber(JRequest::getString('rightBoundValue'));
        /*------------------------------------------------------*/
        /*zkontrolujeme, jestli to neni v rozporu s collation*/
        if (isset($format->Collation[0]))
          if ($format->Collation[0]['type']=='Enumeration'){
            unset($format->Collation[0]);
          }
        /*------------------------------------------------------*/
      }else {
        /*------------------------------------------------------*/
        //ulozime vycet prvku
        $enumArr=split("\n",JRequest::getString('enumeration'));
        $rangeArr=array();
        $enumeration=$allowedRange->addChild('Enumeration');
        if (count($enumArr)>0){
          foreach ($enumArr as $value) {
            if (trim($value)!=''){
              $enumeration->addChild('Value',trim($value));
              $rangeArr[]=trim($value);
            }
          }
        }
        /*------------------------------------------------------*/
        if (isset($format->Collation[0])){
          $collation=$format->Collation[0];
          if ($collation['type']=='Enumeration'){
            $valuesArr=array();
            /*musime zkontrolovat, jestli allowed range odpovida collation*/ 
            if (count($collation->Value)>0){
              $delArr=array();
              $vId=0;
              foreach ($collation->Value as $value) {
                $valStr=(string)$value;
              	if (!in_array($valStr,$rangeArr)){
                  $delArr[]=$vId;
                }else {
                  $valuesArr[]=$valStr;
                 // echo 'pridavam hodnotu'.$value.'<br /><br />';
                }
                $vId++;
              }
              for ($dId=count($delArr)-1;$dId>=0;$dId--){
                $deleteId=$delArr[$dId];
              	unset($format->Collation[0]->Value[$deleteId]);
              }
            }
            if (count($rangeArr)>0){
              foreach ($rangeArr as $value) {
                if (!in_array($value,$valuesArr)){
                  $collation->addChild('Value',$value);            
                }
              }
            }
          }
        }
        /*------------------------------------------------------*/
      }
      
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
  
  /**
   * Funkce pro editaci formátu
   */     
  function newFormat(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='new'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));                             
      $fId=intval(JRequest::getInt('fId',-1));        
      if (!isset($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0])){
        $xml->MetaAttributes[0]->MetaAttribute[$maId]->addChild('Formats');
      }            
      
      /*projdeme formaty a najdeme vsechna jmena*/
      $name=(string)JRequest::getString('name');
      if (count($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format)>0){
        $namesArr=array();
        foreach ($xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format as $format) {
        	$namesArr[]=(string)$format['name'];
        }
        while (in_array($name,$namesArr)){
          $name=$name.'X';
        }  
      }
      /**/
      
      $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->addChild('Format');
      
      $format['name']=$name;
      $format->Author=JRequest::getString('author');
      $format->DataType=JRequest::getString('dataType');
      $format->ValueType=JRequest::getString('valueType');
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
      $view->display();
    } 
  }  
  
  /**
   * Zobrazení jednoho metaatributu
   */     
  function metaAttribute(){
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
  function groupMetaAttribute(){
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
  
  function addChildMeta(){
    require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();
		if ($_POST['potvrzeni']=='new'){
      //skutecne smazeme
      $xml=$model->load(JRequest::getInt('article'));
      $maId=intval(JRequest::getInt('maId',-1));                             
      $metaAttribute=$xml->MetaAttributes[0]->MetaAttribute[$maId];
      
      $metaAttribute->addChild('ChildMetaAttribute')->addAttribute('id',intval(JRequest::getInt('childId')));
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
   * Zobrazení jednoho formátu
   */     
  function format(){
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
  

  
  
  function display()
    {
        parent::display();
    }

  /****************************************************************************/
  /****ČÁST STARAJÍCÍ SE O INFLUENCES******************************************/
  function influences(){
    $article=JRequest::getInt('article',-1);
    if ($article!=-1){
      /*zobrazíme Jakubovu transformaci*/
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
      $model=new BkefModel();    
      require_once (JPATH_COMPONENT.DS.'views'.DS.'influences'.DS.'influences.html.php');
      $view=new BkefViewInfluences();
      $article=JRequest::getInt('article');
      $_SESSION['articleId']=$article;
      $view->article=$article;
      $view->articleTitle=$model->loadTitle($article);
      $view->xmldocument=$model->load($article)->asXML();
      $user=& JFactory::getUser();
      $view->username=$user->name;
      $view->display(); 
    }else{
      /*zobrazime výběr článku*/                
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
		  require_once (JPATH_COMPONENT.DS.'views'.DS.'influences'.DS.'articles.html.php');
	    $view = new BkefViewSelectArticle();
      $view->setModel(new BkefModel(),true);
	    $view->display();
    }
  }

  function zpracujInfluences(){
    if (isset($_SESSION['articleId'])){
      require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
      $model=new BkefModel();    ;
      $article=$_SESSION['articleId'];
      $obsahSouboru=$model->load($article)->asXML();
    	//die($_POST["checkboxess"]);
      require_once (JPATH_COMPONENT.DS.'influences'.DS.'saveBKEF.php');
      
      $model->save($article,$obsahSouboru);
    }
    $this->_redirect='index.php?option=com_bkef&task=influences&article='.$article.'&additionalInfo='.$_POST["checkboxess"];
  }              

  function getTableMatrix(){
  	if (isset($_SESSION['articleId'])){
  	require_once (JPATH_COMPONENT.DS.'models'.DS.'bkef.php');
    $model=new BkefModel();    ;
    $article=$_SESSION['articleId'];
    $obsahSouboru=$model->load($article)->asXML();
    libxml_use_internal_errors(true);
	$xdoc = new DomDocument;
	$cestaKTransformaci = JPATH_COMPONENT.DS."influences".DS."bkef-matrix-influence.xsl";
    //echo $_POST["zaskrtnuto"];        
    //V případě, že by měla proměnná xmldocument obsahovat celý dokument jako string, je potřebná funkce na dalším řádku.
	$xdoc->loadXML($obsahSouboru); 
	//echo $xdoc->saveXML();
	$xdoc2 = new DomDocument(); 
	$xdoc2->load($cestaKTransformaci);
	$variables = $xdoc2->getElementsByTagName("variable");
	foreach($variables as $variable){
		//echo "Hele ".$variable->getAttribute("name")." <br>";
		if($variable->getAttribute("name") == "attribute"){
			$variable->setAttribute("select","'".$_POST["attribute"]."'");
		}
		if($variable->getAttribute("name") == "attributeII"){
			$variable->setAttribute("select","'".$_POST["attributeii"]."'");
		}
	}
	//echo $xdoc2->saveXML();
	//echo "<div>".$xdoc2->saveXML()."</div>";
	$xsl = new XSLTProcessor();
	$xsl->importStylesheet($xdoc2);
	if ($html = $xsl->transformToXML($xdoc)) {
		//Výpis výsledku transformace 
		echo $html;//."<div id=\"coZaskrtnout\" style=\"display: none;\">".$_GET["additionalInfo"]."</div>"
	} else {
		echo JText::_('TRANSFORMATION_ERROR');
	}
	libxml_clear_errors(); 
	}
  }
  
  /**
   * Funkce pro vytvoření nového BKEF článku
   */     
  function newArticle(){
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
        $articleContent='<'.'?xml version="1.0" encoding="UTF-8"?'.'><'.'?xml-stylesheet type="text/xsl" href="bkef-styl.xsl"?'.'><BKEF xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://keg.vse.cz/bkef" version="1.0" xsi:schemaLocation="http://keg.vse.cz/bkef bkef.xsd"><Header><Application name="BKEF Editor" version="1.1"/><Title>'.htmlentities($articleName).'</Title></Header><MetaAttributes></MetaAttributes><Influences></Influences></BKEF>';
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


  /****************************************************************************/
  /****************************************************************************/
}
?>

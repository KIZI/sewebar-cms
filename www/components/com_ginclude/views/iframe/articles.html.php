<?php
/**
 * HTML View class for the gInclude Component
 *  
 * @package    gInclude
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class GincludeViewArticles extends JView
{

  /**
   *  Funkce pro zobrazení přehledu článků pro jejich výběr
   */ 
  function articlesHtml(){
    global $mainframe;
    $model=$this->getModel();
    
    $limit = JRequest::getVar('limit',$mainframe->getCfg(list_limit));
    $limitstart=JRequest::getVar('limitstart',0);
    
    /*kontrola, jestli je v session nastavena sekce a kategorie -> pouze při prvním zobrazení*/
      if ((JRequest::getInt('section',-1)==-1)&&($_SESSION['ginclude']['section']>0)){
        JRequest::setVar('section',$_SESSION['ginclude']['section']);
        $_SESSION['ginclude']['section']=-1;
      }
      if ((JRequest::getInt('categorie',-1)==-1)&&($_SESSION['ginclude']['categorie']>0)){
        JRequest::setVar('categorie',$_SESSION['ginclude']['categorie']);
        $_SESSION['ginclude']['categorie']=-1;
      } 
    /**/
    
    $articles=$model->getArticles(JRequest::getInt('section',-1),JRequest::getInt('categorie',-1),JRequest::getString('filter',''),JRequest::getCmd('filter_order','title'),JRequest::getCmd('filter_order_Dir','asc'),$limitstart,$limit);
    $total=$model->getArticlesCount(JRequest::getInt('section',-1),JRequest::getInt('categorie',-1),JRequest::getString('filter',''));
    $result='';
    
    jimport('joomla.html.pagination');
    $pageNav = new JPagination($total,$limitstart,$limit);
    $result.= '<h3>'.JText::_(SELECT_ARTICLE).'</h3>';
    $result.= '<form action="index.php?option=com_ginclude&amp;task=articles&amp;tmpl=component" name="adminForm" id="adminForm" method="post">';
    $orderDir=JRequest::getCmd('filter_order_Dir','asc');
    if ($orderDir=='asc'){$orderDir2='desc';}else{$orderDir2='asc';}
    $result.= '<div style="position:relative;">'.JText::_(FILTER).': <input type="text" name="filter" value="'.JRequest::getString('filter','').'" id="filter" /><button onclick="this.form.submit();">OK</button><button onclick="document.getElementById(\'filter\').value=\'\';this.form.submit();">Reset</button>';
    $result.= '<div style="display:inline;position:absolute;right:5px;top:2px;"><select name="section" onchange="document.adminForm.submit();"><option value="-1">--'.JText::_(SELECT_SECTION).'--</option>';
    $sections=$model->getSections();
    if(count($sections))foreach ($sections as $key=>$value) {
    	$result.= '<option value="'.$key.'"';
    	if (JRequest::getInt('section',-1)==$key){
        $result.= ' selected="selected" ';
      }
      $result.= '>'.$value.'</option>';
    }
    $result.= '</select> ';
    $result.= '<select name="categorie" onchange="document.adminForm.submit();"><option value="-1">--'.JText::_(SELECT_CATEGORY).'--</option>';
    $categories=$model->getCategories(JRequest::getInt('section',-1));
    if (count($categories)>0)foreach ($categories as $key=>$value) {
    	$result.= '<option value="'.$key.'"';
    	if (JRequest::getInt('categorie',-1)==$key){
        $result.= ' selected="selected" ';
      }
      $result.= '>'.$value.'</option>';
    }
    $result.= '</select></div>';
    $result.= '</div>';
    $result.= '<table border="0" class="adminlist" cellspacing="1"><thead><tr><th><a href="javascript:tableOrdering(\'1\',\''.$orderDir2.'\',\'\');">'.JText::_('TITLE').'</a></th><th width="150"><a href="javascript:tableOrdering(\'2\',\''.$orderDir2.'\',\'\');">'.JText::_('SECTION').'</a></th><th width="150"><a href="javascript:tableOrdering(\'3\',\''.$orderDir2.'\',\'\');">'.JText::_('CATEGORY').'</a></th><th width="80"><a href="javascript:tableOrdering(\'created\',\''.$orderDir2.'\',\'\');">'.JText::_('DATE').'</a></th></tr></thead>';
    if (($total>0)&&(count($articles)>0)) foreach ($articles as $article) {
      if ($rowClass=='row0'){$rowClass='row1';}else{$rowClass='row0';}
      $result.= '<tr class="'.$rowClass.'"><td><a href="index.php?option=com_ginclude&amp;tmpl=component&amp;task=insert&amp;article='.$article->id.'">'.$article->title.'</td><td>'.$article->section.'</td><td>'.$article->categorie.'</td><td>'.$article->cdate.'</td></tr>';
    }
    $result.= '<tfoot><tr><td colspan="4">'.$pageNav->getListFooter().'</td></tr></tfoot></table>';
    $result.= '<input type="hidden" name="filter_order" value="'.JRequest::getCmd('filter_order','title').'" />';
    $result.= '<input type="hidden" name="filter_order_Dir" value="'.$orderDir.'" />';
    $result.= '</form>';
    
    return $result;
  } 

  function display($tpl = null)
  {   
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_ginclude/css/general.css');
        $doc->addStyleSheet('components/com_ginclude/css/component.css');
      }
      echo $this->articlesHTML();
      //parent::display($tpl);
  }
}

?>
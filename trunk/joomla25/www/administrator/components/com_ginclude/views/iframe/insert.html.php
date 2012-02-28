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
 
class GincludeViewInsert extends JView
{
  function insertHTML(){
    $article=$this->article;
    $model=$this->getModel();
    global $mainframe;
    $doc = & JFactory::getDocument();
        if (JPATH_BASE!=JPATH_ADMINISTRATOR){
          $doc->addStyleSheet('components/com_ginclude/css/general.css');
          $doc->addStyleSheet('components/com_ginclude/css/component.css');
        }
    if (@$_REQUEST['part']!=''){
      $part=$_REQUEST['part'];
    }else{
      $part=-1;
    }    
    
    $articleParts=$model->getParts($article->id,$part);
    $result='';
    $result.= '<div style="position:relative;"><h3>'.JText::_('ARTICLE').': '.$article->title.'</h3>';
    $result.= JText::_('SECTION').': <strong>'.$article->section.'</strong> '.JText::_('CATEGORY').': <strong>'.$article->categorie.'</strong> '.JText::_('CREATED').': <strong>'.$article->cdate.'</strong>';
    $result.= '<div style="position:absolute;right:5px;top:10px;"><button onclick="location.href=\'index.php?option=com_ginclude&task=articles&tmpl=component\';">'.JText::_('SELECT_OTHER_ARTICLE').'</button></div>';
    $result.= '</div>';
    
    if ($articleParts===false){
      /*clanek neni rozdelen na casti => nabidneme ho ke vlozeni cely*/
      $result.= '<div><a href="javascript:parent.gInclude(\''.$article->id.'\',\'-1\');">'.JText::_('INSERT_FULL_ARTICLE').'</a></div>';
    }else {
      /*nacetli jsme jednotlive sekce, tak zobrazime vyber*/
      /*strankovani*/  
       $limit = JRequest::getVar('limit',$mainframe->getCfg(list_limit));
       $limitstart=JRequest::getVar('limitstart',0);

       $articles=$model->getArticles(JRequest::getInt('section',-1),JRequest::getInt('categorie',-1),JRequest::getString('filter',''),JRequest::getCmd('filter_order','title'),JRequest::getCmd('filter_order_Dir','asc'),$limitstart,$limit);
       $total=0;  
       
      /**/
      $result.= '<form action="index.php" id="adminForm" name="adminForm">
            <input type="hidden" name="article" value="'.$article->id.'" />
            <input type="hidden" name="tmpl" value="component" />
            <input type="hidden" name="task" value="insert" />
            <input type="hidden" name="option" value="com_ginclude" />
            <table class="adminlist" cellspacing="1">';
      $result.= '<thead><tr><th style="text-align:left;">'.JText::_('ARTICLE_SECTION').': <select name="part" onchange="document.getElementById(\'adminForm\').submit();"><option value="-1">--'.JText::_('SELECT').'--</option>';
      if (count($articleParts['main'])>0)foreach ($articleParts['main'] as $key=>$value) {
      	$result.= '<option value="'.$key.'"';
      	if ($key==$part){
          $result.= ' selected="selected" ';
        }
        $result.= '>'.$value.'</option>';
      }
      $result.= '</select>';
      if ($part!=-1) { $result.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:parent.gInclude(\''.$article->id.'\',\''.$part.'\');" style="font-weight:normal;">'.JText::_('INSERT_SECTION_CONTENT').'</a>&nbsp;';}
      $result.='</th></tr></thead>';
      if ($part==-1){
        /*neni vybrana zadna sekce clanku -> musime vypsat info pro uzivatele*/
        $result.= '<tbody><tr><td>'.JText::_('ARTICLE_SECTION_SELECT').'</td></tr></tbody>';
      }elseif (count($articleParts['part'])>0){
        /*v dané sekci jsou vložitelné oblasti*/
        $result.= '<tbody>';
        $rowClass='row1';
        $pos=-1;
        $max=$limit+$limitstart;
        if (count($articleParts['part'])>0) foreach ($articleParts['part'] as $key=>$value) {
          $pos++;
          if (($pos>=$limitstart)&&($pos<$max)){
            if ($rowClass=='row0'){$rowClass='row1';}else{$rowClass='row0';}
            $result.= '<tr class="'.$rowClass.'"><td><a href="javascript:parent.gInclude(\''.$article->id.'\',\''.$key.'\');">'.$value.'</a></td></tr>';
          }	
        }
        $total=$pos+1;
        $result.= '</tbody>';
      }else {
        /*nejsou žádné konkrétní obsahy*/
        $result.= '<tbody><tr class="row0"><td><a href="javascript:parent.gInclude(\''.$article->id.'\',\''.$part.'\');">'.JText::_('INSERT_CONTENT').'...</a></td></tr></tbody>';	
      }
      jimport('joomla.html.pagination');
      if ($total>1){
        $pageNav = new JPagination($total,$limitstart,$limit);
        $result.= '<tfoot><tr><td>'.$pageNav->getListFooter().'</td></tr></tfoot></table>';
      } 
      $result.= '</table>';
      $result.= '</form>';
    }
    
    return $result;  
  }

  function display($tpl = null)
  {        
    echo $this->insertHTML();
  }
}

?>
<?php
/**
 * HTML View class for the gInclude Component
 *  
 * @package    gInclude
 * @license    GNU/GPL
 * @author Stanislav Vojíř
 * @copyright Stanislav Vojíř, 2009-2012
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
    $mainframe=JFactory::getApplication();
    $model=$this->getModel();
    
    $limit = JRequest::getVar('limit',$mainframe->getCfg(list_limit));
    $limitstart=JRequest::getVar('limitstart',0);
    
    /*kontrola, jestli je v session nastavena sekce a kategorie -> pouze při prvním zobrazení*/
      if ((@$_SESSION['ginclude']['hide_category']==1)&&($_SESSION['ginclude']['default_category'])){
        JRequest::setVar('category',$_SESSION['ginclude']['default_category']);
      }else{
        if ((JRequest::getInt('category',-1)==-1)&&($_SESSION['ginclude']['category']>0)){
          JRequest::setVar('category',$_SESSION['ginclude']['category']);
          $_SESSION['ginclude']['category']=-1;
        }
      }

      if ((JRequest::getInt('filterDelete',-1)==-1)){
        if (isset($_SESSION['ginclude']['filterDelete'])){
          JRequest::setVar('filterDelete',$_SESSION['ginclude']['filterDelete']);
        }else{
          JRequest::setVar('filterDelete',0);
        }
      }
    /**/
    $articles=$model->getArticles(JRequest::getInt('category',-1),JRequest::getString('filter',''),JRequest::getCmd('filter_order','title'),JRequest::getCmd('filter_order_Dir','asc'),$limitstart,$limit,false,(JRequest::getInt('filterDelete',0)==1),(JRequest::getInt('filterDelete',0)==2));
    $total=$model->getArticlesCount(JRequest::getInt('category',-1),JRequest::getString('filter',''),false,(JRequest::getInt('filterDelete',0)==2));

    $limitstartOriginal=$limitstart;
    while (empty($articles)&&($limitstart<$total-1)){
      $limitstart+=$limit;
      $articles=$model->getArticles(JRequest::getInt('category',-1),JRequest::getString('filter',''),JRequest::getCmd('filter_order','title'),JRequest::getCmd('filter_order_Dir','asc'),$limitstart,$limit,false,(JRequest::getInt('filterDelete',0)==1),(JRequest::getInt('filterDelete',0)==2));
      if (($limitstart>$total)&&($limitstartOriginal>0)&&(empty($articles))){
        $limitstart=-1*$limit;
        $limitstartOriginal=0;
      }
    }

    $result='';
    
    jimport('joomla.html.pagination');
    $pageNav = new JPagination($total,$limitstart,$limit);
    $result.= '<h3>'.JText::_(SELECT_ARTICLE).'</h3>';
    $result.= '<form action="index.php" name="adminForm" id="adminForm" method="get">
                <input type="hidden" name="option" value="com_ginclude" />
                <input type="hidden" name="task" value="articles" />
                <input type="hidden" name="tmpl" value="component" />';
    $orderDir=JRequest::getCmd('filter_order_Dir','asc');
    if ($orderDir=='asc'){$orderDir2='desc';}else{$orderDir2='asc';}
    $result.= '<div style="position:relative;">'.JText::_(FILTER).': <input type="text" name="filter" value="'.JRequest::getString('filter','').'" id="filter" /><button onclick="this.form.submit();">OK</button><button onclick="document.getElementById(\'filter\').value=\'\';this.form.submit();">Reset</button>';
    $result.= '<div style="display:inline;position:absolute;right:5px;top:2px;">';
    if (isset($_SESSION['ginclude']['hide_filterDelete'])&&(($_SESSION['ginclude']['filterDelete']==1)||($_SESSION['ginclude']['filterDelete']==2))){
      //mame skryt filtr
      $result.='<input type="hidden" name="filterDelete" value="'.JRequest::getInt('filterDelete',0).'" />';
    }else{
      //mame zobrazit filtr
      $result.= '<select name="filterDelete" onchange="document.adminForm.submit();">
                   <option value="0" '.((JRequest::getInt('filterDelete',0)==0)?'selected="selected"':'').'>'.JText::_('ALL_ARTICLES').'</option>
                   <option value="1" '.((JRequest::getInt('filterDelete',0)==1)?'selected="selected"':'').'>'.JText::_('ACCESSIBLE_ARTICLES').'</option>
                   <option value="2" '.((JRequest::getInt('filterDelete',0)==2)?'selected="selected"':'').'>'.JText::_('I_AM_AUTHOR').'</option>
                 </select>';
    }

    if (@$_SESSION['ginclude']['hide_category']){
      //mame skryt vyber kategorie
      $result.='<input type="hidden" name="category" value="'.JRequest::getInt('category',-1).'" />';
    }else{
      //zobrazujeme vyber kategorie
      $result.= ' <select name="category" onchange="document.adminForm.submit();"><option value="-1">--'.JText::_(SELECT_CATEGORY).'--</option>';
      /*vypsani jednotlivych kategorii*/
      $categories=$model->getCategories(true);
      $currentCatId=JRequest::getInt('category',-1);
      if(count($categories)>0)
        foreach ($categories as $catId=>$catArr) {
          $result.='<option value="'.$catId.'"';
          if ($catId==$currentCatId){
            $result.=' selected="selected"';
          }elseif($catArr['disabled']){
            $result.=' disabled="disabled"';
          }
          $result.='>'.$catArr['title'].'</option>';
        }
      /*--vypsani jednotlivych kategorii*/
      $result.= '</select>';
    }
    $result.= '</div>';
    $result.= '</div>';


    if (($total>0)&&(count($articles)>0)){
      $result.= '<table border="0" class="adminlist" cellspacing="1">
                 <thead>
                   <tr>
                     <th><a href="javascript:tableOrdering(\'1\',\''.$orderDir2.'\',\'\');">'.JText::_('TITLE').'</a></th>
                     <th width="150"><a href="javascript:tableOrdering(\'3\',\''.$orderDir2.'\',\'\');">'.JText::_('CATEGORY').'</a></th>
                     <th width="80"><a href="javascript:tableOrdering(\'created\',\''.$orderDir2.'\',\'\');">'.JText::_('DATE').'</a></th>
                   </tr>
                 </thead>';

      foreach ($articles as $article) {       
        if (isset($rowClass)&&($rowClass=='row0')){$rowClass='row1';}else{$rowClass='row0';}
        $result.= '<tr class="'.$rowClass.'">
                     <td>';
          if($article->locked>0){
            $result.=' <img src="media/" alt="Locked" />';//TODO
          }          
          if ($article->locked==2){
            $result.=' <span class="lockedArticle">'.$article->title.'</span>';
          }else{
            $result.=' <a href="index.php?option=com_ginclude&amp;tmpl=component&amp;task=insert&amp;article='.$article->id.'">'.$article->title.'</a>';
          }          
        $result.=   '</td>
                     <td>'.$article->categoryTitle.'</td>
                     <td>'.$article->cdate.'</td>
                   </tr>';
      }

      $result.= '<tfoot><tr><td colspan="4">'.$pageNav->getListFooter().'</td></tr></tfoot></table>';
    }else{
      $result.= '<p>'.JText::_('NO_ARTICLES_FOUND').'</p>';

    }

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
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

 
class GincludeViewArticle extends JView
{
  function getSelectButton($oldId){
    $link = 'index.php?option=com_ginclude&amp;task=reloadChangeArticle&amp;tmpl=component&amp;oldId='.$oldId;
		return '<a href="'.$link.'" rel="{handler: \'iframe\', size: {x: 700, y: 400}}" class="modal">'.JText::_('SELECT_OTHER_ARTICLE').'</a>';
  }

  function display($tpl = null)
  {        
    $doc = & JFactory::getDocument();
    $declaration	="
     function gSelectArticle(oldId,newId,title) {
       document.getElementById('art'+oldId).value=newId;
       document.getElementById('input'+oldId).value=title;
	     document.getElementById('sbox-window').close();
     }";
    $doc->addScriptDeclaration($declaration);
    JHTML::_('behavior.modal');

    /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_ginclude::_DEFAULT();
    }else{
      echo '<div class="componentheading">'.JText::_('GINCLUDE-UPDATE').'</div>';
      $doc = &JFactory::getDocument();
      $doc->addStyleSheet('components/com_ginclude/css/general.css');
      $doc->addStyleSheet('components/com_ginclude/css/component.css');
    }
    /**/
    
    $model=$this->getModel();
    $article=$model->getArticleDB($this->articleId);
    $article=$article[0];
    echo '<h3>'.JText::_('RELOAD_SELECT_ARTICLES0').'</h3>';
    echo '<table><tr><td width="100">'.JText::_('TITLE').':</td><td><strong>'.$article->title.'</strong></td><td rowspan="2" style="padding-left:50px;"><button onclick="location.href=\'index.php?option=com_ginclude&task=reload\';">'.JText::_('SELECT_OTHER_ARTICLE').'</button></td></tr>';
    echo '<tr><td>'.JText::_('CREATED').':</td><td>'.$article->cdate.'</td></tr></table>';
    echo '<h3>'.JText::_('RELOAD_SELECT_ARTICLES').'</h3>';
    
    
    $linkArr=$model->getArticleLinks($this->articleId);
    if (count($linkArr)>0){
      /*ve článku je nějaký odkaz => nabídneme aktualizaci*/
      echo '<div>'.JText::_('RELOAD_SELECT_ARTICLES_ABOUT').'</div>';
      echo '<form method="post" action="index.php?option=com_ginclude&amp;task=reloadParts">';
      echo '<input type="hidden" name="article" value="'.$this->articleId.'" />';
      echo '<table border="0" class="adminlist" cellspacing="1" style="margin-top:10px;margin-bottom:10px;"><tbody>';
      if(count($linkArr)>0)foreach ($linkArr as $artId=>$artTitle) {
        $art=$model->getArticleDB($artId);                       
        $art=$art[0];
        
        echo '<tr><td width="100">'.JText::_('ORIGINAL_ARTICLE').'</td><td><strong>'.$art->title.'</strong>';
        if (htmlspecialchars($art->title,ENT_QUOTES,'utf-8')!=$artTitle){echo '<span style="color:red;">&nbsp;-&nbsp;'.JText::_('ORIGINAL_ARTICLE_TITLE').' <strong>'.$artTitle.'</strong>&nbsp;!</span>';}
        echo '</td></tr>';
        echo '<tr class="row1"><td>'.JText::_('RELOADED_ARTICLE').'</td><td><input type="hidden" value="'.$art->id.'" name="art'.$art->id.'" id="art'.$art->id.'" /><input id="input'.$art->id.'" type="text" name="input'.$art->id.'" value="'.htmlspecialchars($art->title,ENT_QUOTES,'utf-8').'" style="width:250px;" readonly="readonly"/>&nbsp;&nbsp;&nbsp;'.$this->getSelectButton($art->id).'</td></tr>';	
      }
      echo '</tbody></table>';
      echo '<div"><input type="submit" value="'.JText::_('SHOW_RELOADABLE_PARTS').'" /></div></form>';
    }else{
      /*nenalezen žádný odkaz => zobrazíme upozornění...*/
      echo '<div>'.JText::_('NO_RELOADABLE_PARTS_ABOUT').'</div>';
    }    
  }
  
}
?>
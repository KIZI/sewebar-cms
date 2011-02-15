<?php

// no direct access 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the gInclude Component
 *  
 * @package    gInclude
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
class GincludeViewParts extends JView
{
  function display($tpl = null){        
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
        
    echo '<div>'.JText::_('RELOAD_PARTS_ABOUT').'</div>';
    /*css*/
    $doc = & JFactory::getDocument();
    $declaration	=
		"div.oldDiv {background-color:#F0C0FF;border:2px solid red;color:black;}
     div.newDiv {background-color:#C0FFA0;border:2px solid green;color:black;}
     hr#system-readmore {border:1px dotted red;}
     div.radioDiv {background-color:#ECECEC;border:2px solid black;padding:2px;color:black;}";
		$doc->addStyleDeclaration($declaration);    
    
    $article=$model->getArticleDB($this->articleId);
    $article=$article[0];
    echo '<div style="padding-left:10px;"><h2>'.$article->title.'</h2></div>';
    echo '<form action="index.php?option=com_ginclude&amp;task=reloadPartsSave" method="post">
          <input type="hidden" name="article" value="'.$this->articleId.'" />
          <div style="padding:10px;margin:5px;border:1px dotted gray;">';
    /*vypsani dat z modelu*/
    echo $model->getPartsView($this->articleId);
    echo '</div><div style="padding:10px;"><input type="submit" value="'.JText::_('SAVE_ARTICLE_CHANGES').'" /></div>';
    echo '</form>';        
  }
}

?>
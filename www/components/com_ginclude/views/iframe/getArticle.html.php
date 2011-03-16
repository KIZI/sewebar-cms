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
 
class GincludeViewGetArticle extends JView
{
  function display($tpl = null)
  {   
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_ginclude/css/general.css');
        $doc->addStyleSheet('components/com_ginclude/css/component.css');
      }
      $model=$this->getModel();  
      echo '<div class="gincludeDiv">';   
      echo $model->finalizeGetArticleContent($model->getArticleContent(JRequest::getInt('article',-1),JRequest::getVar('part',-1)),JRequest::getInt('article',-1),JRequest::getVar('part',-1));
      echo '</div>';
      //parent::display($tpl);
  }
}

?>
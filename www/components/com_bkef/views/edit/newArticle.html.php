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
           
class BkefViewNewArticle extends JView
{
  function display($tpl = null)
  {            
      $model=$this->getModel();       
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      JHTML::_('behavior.modal');
      $doc->addStyleSheet('components/com_bkef/css/main.css');
      
      
      
      echo '<h1>'.JText::_('NEW_BKEF_ARTICLE_H1').'</h1>';
      echo '<div class="infotext">'.JText::_('NEW_BKEF_ARTICLE_INFO').'</div>';
      echo '<form method="post" action="index.php?option=com_bkef&amp;task=newArticle">';
      echo '<strong>'.JText::_('NEW_BKEF_ARTICLE_NAME').'</strong><br />';
      echo '<input type="text" name="articleName" value="" style="width:600px;"/><br />';
      echo '<strong>'.JText::_('NEW_BKEF_ARTICLE_CONTENT').'</strong> '.JText::_('NEW_BKEF_ARTICLE_CONTENT2').'<br />';
      echo '<textarea name="articleContent" style="width:600px;height:300px;"></textarea><br />';
      echo '<br />';
      echo '<h3>'.JText::_('NEW_BKEF_ARTICLE_JOOMLA').'</h3>';
      echo '<div class="infotext">'.JText::_('NEW_BKEF_ARTICLE_SECTION_INFO').'</div>';
      echo '<table><tr><td><strong>'.JText::_('SECTIONCATEGORY').'&nbsp;&nbsp;</strong></td>';
      echo '<td><select name="articleSection">';
      echo '<option value="-1">--none--</option>';
      $sections=$model->getSections();
      if (count($sections)>0)foreach ($sections as $key=>$value) {
      	echo '<option value="'.$key.'_0" >'.$value.'</option>';
      	$categories=$model->getCategories($key);
      	if (count($categories)>0)foreach ($categories as $keyC=>$valueC){
          echo '<option value="'.$key.'_'.$keyC.'" >&nbsp;&nbsp;-&nbsp;'.$valueC.'</option>';
        }
      }
      echo '</select></td></tr>';
      echo '<tr><td><strong>'.JText::_('ARTICLE_STATE').'&nbsp;&nbsp;</strong></td>';
      echo '<td><select name="articleState"><option value="1">'.JText::_('PUBLISHED').'</option><option value="0">'.JText::_('UNPUBLISHED').'</option></select></td></tr></table>';
      echo '<br /><input type="submit" value="'.JText::_('NEW_BKEF_ARTICLE_SUBMIT').'"/></form>';
      ?>
      
      
      <?php
  }
}

?>
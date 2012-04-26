<?php
/**
 * HTML View class for the gInclude Component
 *  
 * @package BKEF 
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2011
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
           
class BkefViewDelPreprocessingHintAnnotation extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('DELETING_ANNOTATION').'</h1>';
      
      
      echo '<div>';
        echo JText::_('DELETING_ANNOTATION_QUESTION');
      echo '</div>    <br /><br />';
      
      ?>
      <form action="index.php?option=com_bkef&amp;task=delPreprocessingHintAnnotation" method="post" target="_parent" >
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $this->phId; ?>" />
        <input type="hidden" name="anId" value="<?php echo $this->anId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" id="potvrzeni" />
        <input type="submit" value="<?php echo JText::_('DELETE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
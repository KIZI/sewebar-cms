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
           
class BkefViewDelFormat extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('DELETING_FORMAT').'</h1>';
      
      $xml=$this->xml;
      $maId=$this->maId+0;
      $fId=intval($this->fId);
      
      echo '<div>';
      
        echo JText::_('DELETING_FORMAT_QUESTION1').'<strong>';
        echo $xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]['name']; 
        echo '</strong> '.JText::_('DELETING_FORMAT_QUESTION2').' <strong>';
        echo $xml->MetaAttributes[0]->MetaAttribute[$maId]['name']; 
        echo '</strong> ?';
      echo '</div>    <br /><br />';
      ?>
      <form action="index.php?option=com_bkef&amp;task=delFormat" method="post" target="_parent" >
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" id="potvrzeni" />
        <input type="submit" value="<?php echo JText::_('DELETE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
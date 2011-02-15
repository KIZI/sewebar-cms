<?php
/**
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
           
class BkefViewAddValueDescriptionValue extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>';
      if ($this->potvrzeni=='interval'){
        echo JText::_('ADDING_INTERVAL_H1');
      }else {
        echo JText::_('ADDING_VALUE_H1');
      }
      echo '</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $vdId=intval($this->vdId);
      

      ?>
      <form action="index.php?option=com_bkef&amp;task=addValueDescriptionValue" method="post" target="_parent" >
      <?php
      if ($this->potvrzeni=='value'){
        echo '<div>'.JText::_('ADD_VALUE').':</div>';
        echo '<input type="text" value="" name="value" title="'.JText::_('TITLE_ADD_VALUE_DESCRIPTION_VALUE_VALUE').'" /><br />';
        echo '<input type="hidden" name="potvrzeni" value="enumeration" id="potvrzeni" />';
      }else {
        echo '<table>';
        echo '<tr><td>Od:</td><td><select name="leftBoundType" title="'.JText::_('TITLE_ADD_VALUE_DESCRIPTION_VALUE_INTERVAL_LEFTBOUND').'">';
        echo '<option value="closed" title="closed" >&lt;</option>';
        echo '<option value="open" title="open" >(</option>';
        echo '</select></td><td><input name="leftBoundValue" value=""  title="'.JText::_('TITLE_ADD_VALUE_DESCRIPTION_VALUE_INTERVAL_LEFTVALUE').'"/></td></tr>';
        echo '<tr><td>Do:</td><td><select name="rightBoundType" title="'.JText::_('TITLE_ADD_VALUE_DESCRIPTION_VALUE_INTERVAL_RIGHTBOUND').'">';
        echo '<option value="closed" title="closed" >&gt;</option>';
        echo '<option value="open" title="open" >)</option>';
        echo '</select></td><td><input name="rightBoundValue" value="" title="'.JText::_('TITLE_ADD_VALUE_DESCRIPTION_VALUE_INTERVAL_RIGHTVALUE').'" /></td></tr>';
        echo '</table>';
        echo '<input type="hidden" name="potvrzeni" value="interval" id="potvrzeni" />';
      }
      
        
      ?>
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <input type="hidden" name="vdId" value="<?php echo $this->vdId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
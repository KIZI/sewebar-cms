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
           
class BkefViewAddIntervalEnumerationBinValue extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('ADD_INTERVAL_H1').'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $phId=intval($this->phId);
      $bId=intval($this->bId);
      if ($bId>-1){
        $bin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0]->Bin[$bId];
      }
      
?>
      <form action="index.php?option=com_bkef&amp;task=addIntervalEnumerationBinValue" method="post" target="_parent" >
        <?php
        echo '<table>';
        echo '<tr><td>'.JText::_('INTERVAL_START').':</td><td><select name="leftBoundType" title="'.JText::_('TITLE_ADD_INTERVAL_ENUMERATION_SELECT_LEFT').'">';
        echo '<option value="closed" title="closed" >&lt;</option>';
        echo '<option value="open" title="open" >(</option>';
        echo '</select></td><td><input name="leftBoundValue" value="" title="'.JText::_('TITLE_ADD_INTERVAL_ENUMERATION_INPUT_LEFT').'" /></td></tr>';
        echo '<tr><td>End:</td><td><select name="rightBoundType" title="'.JText::_('TITLE_ADD_INTERVAL_ENUMERATION_SELECT_RIGHT').'">';
        echo '<option value="closed" title="closed" >&gt;</option>';
        echo '<option value="open" title="open" >)</option>';
        echo '</select></td><td><input name="rightBoundValue" value="" title="'.JText::_('TITLE_ADD_INTERVAL_ENUMERATION_INPUT_RIGHT').'" /></td></tr>';
        echo '</table>';
        ?>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $phId; ?>" />
        <input type="hidden" name="bId" value="<?php echo $bId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" />
        <input type="submit" value="<?php echo JText::_('ADD_VALUE'); ?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
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
           
class BkefViewIntervalEnumerationEditBin extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>';
      if ($this->potvrzeni=="add"){
        echo JText::_('INTENUM_ADDBIN_H1');
      }else {
        echo JText::_('INTENUM_EDITBIN_H1');
      }
      echo '</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $phId=intval($this->phId);
      $binId=intval($this->binId);
      
      if (@$this->binId>-1){
        $value=(string)@$this->xml->MetaAttributes[0]->MetaAttribute[$this->maId]->Formats[0]->Format[$this->fId]->PreprocessingHints[0]->DiscretizationHint[$this->phId]->IntervalEnumeration[0]->IntervalBin[$this->binId]->Name[0];
      }else{
        $value='';
      }
      
?>
      <form action="index.php?option=com_bkef&amp;task=intervalEnumeration<?php if ($this->potvrzeni=='add') {echo 'Add';}else{echo 'Edit';}?>Bin" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('NAME');?></td>
            <td><input type="text" name="name" value="<?php echo $value; ?>" title="<?php echo JText::_('TITLE_INTENUM_ADDEDITBIN_NAME');?>" /></td>
          </tr>
        </table>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $phId; ?>" />
        <input type="hidden" name="binId" value="<?php echo $binId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" />
        <input type="submit" value="<?php echo JText::_('INTENUM_ADDEDITBIN_SUBMIT');?>" />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
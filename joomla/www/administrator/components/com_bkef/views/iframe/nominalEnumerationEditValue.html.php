<?php
/**
 *  
 * @package    BKEF
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2012
 *  
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
           
class BkefViewNominalEnumerationEditValue extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>';
      echo $this->h1;
      echo '</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $phId=intval($this->phId);
      $binId=intval($this->binId);
      //v pripade editace by bylo nutne vlozit konkretni hodnotu
      $value='';
?>
      <form action="index.php?option=com_bkef&amp;task=nominalEnumeration<?php if ($this->potvrzeni=='add') {echo 'Add';}else{echo 'Edit';}?>Value" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('VALUE');?></td>
            <td><input type="text" name="value" value="<?php echo @$value; ?>" title="<?php echo JText::_('TITLE_ADDEDIT_VALUE');?>" /></td>
          </tr>
        </table>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $phId; ?>" />
        <input type="hidden" name="binId" value="<?php echo $binId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" />
        <input type="submit" value="<?php echo JText::_('SAVE_VALUE');?>" />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
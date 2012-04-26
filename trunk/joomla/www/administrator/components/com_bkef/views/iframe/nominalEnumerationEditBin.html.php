<?php
/**
 * HTML View class for the gInclude Component
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
           
class BkefViewNominalEnumerationEditBin extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      if (@$this->binId>-1){
        $value=(string)@$this->xml->MetaAttributes[0]->MetaAttribute[$this->maId]->Formats[0]->Format[$this->fId]->PreprocessingHints[0]->DiscretizationHint[$this->phId]->NominalEnumeration[0]->NominalBin[$this->binId]->Name[0];
      }else{
        $value='';
      }
      
      
      echo '<h1>'.(($this->action=='add')?(JText::_('ADD_BIN')):(JText::_('EDIT_BIN'))).'</h1>';
?>
      <form action="index.php?option=com_bkef&amp;task=nominalEnumeration<?php echo ucfirst($this->action); ?>Bin" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('NAME');?></td>
            <td><input type="text" name="name" value="<?php echo @$value; ?>" title="<?php echo JText::_('TITLE_NOMINALENUM_ADDEDITBIN_NAME');?>" /></td>
          </tr>
        </table>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $this->phId; ?>" />
        <input type="hidden" name="binId" value="<?php echo $this->binId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" />
        <input type="submit" value="<?php echo JText::_('NOMINALENUM_ADDEDITBIN_SUBMIT');?>" />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
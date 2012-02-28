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
           
class BkefViewIntervalEnumerationAddBin extends JView
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
      $bId=intval($this->bId);
      $autor='';
      if ($bId>-1){
        $bin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->IntervalEnumeration[0]->IntervalBin[$bId];
        $autor=@$bin->Annotation[0]->Author[0];
      }else {
        $user=& JFactory::getUser();
        $autor=$user->name;
      }
      
?>
      <form action="index.php?option=com_bkef&amp;task=intervalEnumeration<?php if ($this->potvrzeni=='add') {echo 'Add';}else{echo 'Edit';}?>Bin" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('NAME');?></td>
            <td><input type="text" name="name" value="<?php echo @$bin['name']; ?>" title="<?php echo JText::_('TITLE_INTENUM_ADDEDITBIN_NAME');?>" /></td>
          </tr>
          <tr>
            <td><?php echo JText::_('ANNOTATION');?></td>
            <td><textarea name="annotationText" title="<?php echo JText::_('TITLE_INTENUM_ADDEDITBIN_ANNOTATION');?>" ><?php echo @$bin->Annotation[0]->Text[0]; ?></textarea></td>
          </tr>
          <tr>
            <td><?php echo JText::_('ANNOTATION_AUTHOR');?></td>
            <td><input type="text" title="<?php echo JText::_('TITLE_INTENUM_ADDEDITBIN_ANNOTATION_AUTHOR');?>" name="annotationAuthor" value="<?php echo $autor; ?>" /></td>
          </tr>
        </table>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $phId; ?>" />
        <input type="hidden" name="bId" value="<?php echo $bId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" />
        <input type="submit" value="<?php echo JText::_('INTENUM_ADDEDITBIN_SUBMIT');?>" />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
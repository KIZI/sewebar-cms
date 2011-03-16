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
           
class BkefViewExhaustiveEnumerationAddBin extends JView
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
        echo JText::_('ADD_BIN_H1');
      }else {
        echo JText::_('EDIT_BIN_H1');
      }
      echo '</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $phId=intval($this->phId);
      $bId=intval($this->bId);
      if ($bId>-1){
        $bin=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->PreprocessingHint[$phId]->DiscretizationHint[0]->ExhaustiveEnumeration[0]->Bin[$bId];
      }
      
?>
      <form action="index.php?option=com_bkef&amp;task=exhaustiveEnumeration<?php if ($this->potvrzeni=='add') {echo 'Add';}else{echo 'Edit';}?>Bin" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('NAME');?></td>
            <td><input type="text" name="name" title="<?php echo JText::_('TITLE_EXENUM_ADDBIN_NAME');?>" value="<?php echo @$bin['name']; ?>" /></td>
          </tr>
          <tr>
            <td>Annotation</td>
            <td><textarea name="annotationText" title="<?php echo JText::_('TITLE_EXENUM_ADDBIN_ANNOTATION');?>"><?php echo @$bin->Annotation[0]->Text[0]; ?></textarea></td>
          </tr>
          <tr>
            <td>Annotation Author</td>
            <td><input type="text" name="annotationAuthor" title="<?php echo JText::_('TITLE_EXENUM_ADDBIN_ANNOTATION_AUTHOR');?>" value="<?php echo @$bin->Annotation[0]->Author[0]; ?>" /></td>
          </tr>
        </table>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $phId; ?>" />
        <input type="hidden" name="bId" value="<?php echo $bId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" />
        <input type="submit" value="<?php echo JText::_('EXENUM_ADDBIN_ANNOTATION_SUBMIT');?>" />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
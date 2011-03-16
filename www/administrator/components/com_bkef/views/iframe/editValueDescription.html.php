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
           
class BkefViewEditValueDescription extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('EDITING_VALUE_DESCRIPTION').'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $vdId=intval($this->vdId);
      $valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      $autor='';
      if ($valueDescription->Annotation[0]->Author!=''){
        $autor=$valueDescription->Annotation[0]->Author;
      }else {
        $user=& JFactory::getUser();
        $autor=$user->name;
      }
?>
      <form action="index.php?option=com_bkef&amp;task=editValueDescription" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('ANNOTATION');?></td>
            <td><textarea name="annotation" title="<?php echo JText::_('TITLE_EDIT_VALUE_DESCRIPTION_ANNOTATION'); ?>"><?php echo $valueDescription->Annotation[0]->Text; ?></textarea></td>
          </tr>
          <tr>
            <td>Autor anotace</td>
            <td>
              <input type="text" name="annotationAuthor" title="<?php echo JText::_('TITLE_EDIT_VALUE_DESCRIPTION_ANNOTATION_AUTHOR'); ?>" value="<?php echo $autor; ?>" />
            </td>
          </tr>
        </table> <br />
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="vdId" value="<?php echo $vdId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" id="potvrzeni" />
        <input type="submit" value="<?php echo JText::_('SAVE'); ?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
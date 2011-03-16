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
           
class BkefViewEditAnnotation extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.$this->h1.'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $anId=intval($this->anId);
      if ($anId!=-1){
        $annotation=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Annotations[0]->Annotation[$anId];
      }
        
      $autor='';
      if (isset($annotation->Author[0])){
        $autor=$annotation->Author[0];
      }else {
        $user=& JFactory::getUser();
        $autor=$user->name;
      }
      
?>
      <form action="index.php?option=com_bkef&amp;task=<?php echo $this->akce;?>Annotation" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('ANNOTATION');?></td>
            <td><textarea name="annotationText" title="<?php echo JText::_('TITLE_EDIT_ANNOTATION_TEXT'); ?>" style="width:380px;height:150px;"><?php echo $annotation->Text[0]; ?></textarea></td>
          </tr>
          <tr>
            <td><?php echo JText::_('ANNOTATION_AUTHOR');?></td>
            <td>
              <input type="text" name="annotationAuthor" style="width:380px;" title="<?php echo JText::_('TITLE_EDIT_ANNOTATION_AUTHOR'); ?>" value="<?php echo $autor; ?>" />
            </td>
          </tr>
        </table> <br />
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="anId" value="<?php echo $anId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" id="potvrzeni" />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
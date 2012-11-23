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
           
class BkefViewEditMetaAttribute extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      $doc->addStyleSheet('components/com_bkef/css/main.css');
      
      echo '<h1>'.$this->h1.'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      
      $autor='';
      if ($maId>-1) {
        $autor=@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Annotation->Author;
      }else {
        $user=& JFactory::getUser();
        $autor=$user->name;
      }  
      ?>
      <form action="index.php?option=com_bkef&amp;task=<?php echo $this->potvrzeni?>MetaAttribute" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('METAATTRIBUTE_NAME');?>&nbsp;</td>
            <td>
              <input type="text" name="name" title="<?php echo JText::_('TITLE_EDIT_METAATTRIBUTE_NAME'); ?>" value="<?php if ($maId>-1) echo (string)@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Name; ?>" style="width:350px;font-weight:bold;"/>
            </td>
          </tr>
          <tr>
            <td></td>
            <td class="missing infotext"><?php echo JText::_('EDIT_METAATTRIBUTE_NAME_WARNING'); ?></td>
          </tr>
          <tr>
            <td><?php echo JText::_('VARIABILITY'); ?>&nbsp;</td>
            <td>
              <select name="variability" title="<?php echo JText::_('TITLE_EDIT_METAATTRIBUTE_VARIABILITY'); ?>">
                <option value="Stable"<?php if ($maId>-1) if(@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Variability=="Stable") {echo ' selected="selected" ';} ?>>Stable</option>
                <option value="Actionable"<?php if ($maId>-1) if(@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Variability=="Actionable") {echo ' selected="selected" ';} ?>>Actionable</option>
              </select>
            </td>
          </tr>
      <?php
        if (!($maId>-1)){
      ?>
          <tr>
            <td><?php echo JText::_('ANNOTATION'); ?></td>
            <td>
              <textarea name="annotation" title="<?php echo JText::_('TITLE_EDIT_METAATTRIBUTE_ANNOTATION'); ?>" style="width:350px;height:160px;"></textarea>
            </td>
          </tr>
          <tr>
            <td><?php echo JText::_('ANNOTATION_AUTHOR'); ?></td>
            <td>
              <input type="text" name="annotationAuthor" value="<?php echo $autor ?>" title="<?php echo JText::_('TITLE_EDIT_METAATTRIBUTE_ANNOTATION_AUTHOR'); ?>" style="width:350px;" />
            </td>
          </tr>
      <?php
        }
      ?>
        </table>
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" id="potvrzeni" />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
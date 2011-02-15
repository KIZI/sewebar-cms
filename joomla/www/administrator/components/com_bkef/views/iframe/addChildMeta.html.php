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
           
class BkefViewAddChildMeta extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('ADD_CHILD_META').'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      //$valueDescription=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->ValueDescriptions[0]->ValueDescription[$vdId];
      
?>
      <form action="index.php?option=com_bkef&amp;task=addChildMeta" method="post" target="_parent" >
        <?php echo JText::_('CHILD_META_TO_ADD'); ?>:<br />
        <select name="childId" title="<?php echo JText::_('TITLE_CHILD_META_TO_ADD'); ?>">
        <?php
          /*načteme ID, která už jsou ve skupině zařazená*/
          $childArr=array();
          foreach ($xml->MetaAttributes[0]->MetaAttribute[$maId]->ChildMetaattribute as $childMeta) {
          	$childArr[]=intval($childMeta['id']);
          }  
          /*načteme ostatní metaatr.*/
          $childId=0;
          foreach ($xml->MetaAttributes[0]->MetaAttribute as $meta){
            if (($meta['level']==0)&&(!in_array(intval($meta['id']),$childArr))){
              echo '<option value="'.intval($meta['id']).'">'.$meta['name'].'</option>';
            }
            $childId++;                
          }
        ?>
        </select>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="potvrzeni" value="new" />
        <input type="submit" value="<?php echo JText::_('ADD');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
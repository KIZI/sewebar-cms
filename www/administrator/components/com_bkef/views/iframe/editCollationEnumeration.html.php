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
           
class BkefViewEditCollationEnumeration extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      $doc->addStyleSheet('components/com_bkef/css/main.css');
      
      echo '<h1>'.JText::_('EDIT_COLLATION_VALUES_H1').'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $article=intval($this->article);
      $collation=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->Collation[0];
      echo '<div class="infotext">'.JText::_('EDIT_COLLATION_VALUES_INFO').'</div>';
      if (count($collation->Value)>0){
        echo '<table>';
        $vId=0;
        foreach ($collation->Value as $value) {
        	echo '<tr><td><strong>'.$value.'&nbsp;</strong></td><td>';
        	echo '<a href="index.php?option=com_bkef&amp;task=editCollationEnumeration&amp;article='.$article.'&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;potvrzeni=moveUp&amp;vId='.$vId.'">'.JText::_('EDIT_COLLATION_VALUES_UP').'</a>&nbsp;|&nbsp;';
        	echo '<a href="index.php?option=com_bkef&amp;task=editCollationEnumeration&amp;article='.$article.'&amp;maId='.$maId.'&amp;fId='.$fId.'&amp;potvrzeni=moveDown&amp;vId='.$vId.'">'.JText::_('EDIT_COLLATION_VALUES_DOWN').'</a>';
        	echo '</td></tr>';
          $vId++;
        }
        echo '</table>';
      }else {
        echo '<div class="missing infotext">'.JText::_('EDIT_COLLATION_VALUES_MISSING_INFO').'</div>';
      } 
    ?>
      <br />
      <form action="index.php?option=com_bkef&amp;task=editCollationEnumeration" method="post" target="_parent" >
        <input type="hidden" name="article" value="<?php echo $article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" />
        <input type="submit" value="<?php echo JText::_('EDIT_COLLATION_VALUES_DONE'); ?>" />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
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
           
class BkefViewEditRange extends JView
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
      
      if ($fId>-1){
        $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      }
      ?>
      <form action="index.php?option=com_bkef&amp;task=editRange" method="post" target="_parent" >
      <?php
      if (((@$format->AllowedRange[0]->Enumeration)||($_GET['type']=='enumeration'))&&($_GET['type']!='interval')){
        echo '<div>'.JText::_('EDIT_RANGE_INFO').'</div>';
        echo '<textarea name="enumeration" style="width:300px;height:200px;" title="'.JText::_('TITLE_EDIT_RANGE_TEXTAREA').'">';
        $text='';
        if (count($format->AllowedRange[0]->Enumeration[0]->Value)>0){
          foreach ($format->AllowedRange[0]->Enumeration[0]->Value as $value) {
          	$text.= $value."\n";
          }
        }
        echo trim($text);
        echo '</textarea><br />';
        echo '<input type="hidden" name="potvrzeni" value="enumeration" id="potvrzeni" />';
      }else {
        echo '<table>';
        echo '<tr><td>Od:</td><td><select name="leftBoundType" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_LEFTBOUND').'">';
        echo '<option value="closed" title="closed" ';
        if (@$format->AllowedRange[0]->Interval[0]->LeftBound[0]['type']=='closed'){
          echo ' selected="selected" ';
        }
        echo '>&lt;</option>';
        echo '<option value="open" title="open" ';
        if (@$format->AllowedRange[0]->Interval[0]->LeftBound[0]['type']=='open'){
          echo ' selected="selected" ';
        }
        echo '>(</option>';
        echo '</select></td><td><input name="leftBoundValue" value="'.$value['value'].'" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_LEFTVALUE').'" /></td></tr>';
        echo '<tr><td>Do:</td><td><select name="rightBoundType" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_RIGHTBOUND').'">';
        echo '<option value="closed" title="closed" ';
        if (@$format->AllowedRange[0]->Interval[0]->RightBound[0]['type']=='closed'){
          echo ' selected="selected" ';
        }
        echo '>&gt;</option>';
        echo '<option value="open" title="open" ';
        if (@$format->AllowedRange[0]->Interval[0]->RightBound[0]['type']=='open'){
          echo ' selected="selected" ';
        }
        echo '>)</option>';
        echo '</select></td><td><input name="rightBoundValue" value="'.$value['value'].'" title="'.JText::_('TITLE_EDIT_RANGE_INTERVAL_RIGHTVALUE').'" /></td></tr>';
        echo '</table>';
        echo '<input type="hidden" name="potvrzeni" value="interval" id="potvrzeni" />';
      }
      
        
      ?>
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <br />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
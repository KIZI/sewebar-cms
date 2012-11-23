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
           
class BkefViewEquidistant extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('EQUIDISTANT_INTERVAL_SET_PARAMS_H1').'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $phId=intval($this->phId);
      $equidistant=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId]->PreprocessingHints[0]->DiscretizationHint[$phId]->EquidistantInterval[0];
      
?>
      <form action="index.php?option=com_bkef&amp;task=equidistant" method="post" target="_parent" >
        <?php
        $closure=$equidistant['closure'];
        if (strpos($closure,'closed')===0){
          $closureLeft='closed';
          $closureRight=strtolower(substr($closure,6));
        }else{
          $closureLeft='open';
          $closureRight=strtolower(substr($closure,4));
        }
        
        echo '<table>';
        echo '<tr><td>'.JText::_('INTERVAL_START').':</td><td><select name="leftBoundType" type="'.JText::_('TITLE_EQUIDISTANT_INTERVAL_LEFTBOUND').'">';
        echo '<option value="closed" title="closed"';
        if ($closureLeft=='closed'){echo ' selected="selected" ';}
        echo '>&lt;</option>';
        echo '<option value="open" title="open" ';
        if ($closureLeft=='open'){echo ' selected="selected" ';}
        echo '>(</option>';
        echo '</select></td><td><input name="leftBoundValue" value="'.floatval((string)$equidistant->Start[0]).'" title="'.JText::_('TITLE_EQUIDISTANT_INTERVAL_LEFTVALUE').'" /></td></tr>';
        echo '<tr><td>'.JText::_('INTERVAL_END').':</td><td><select name="rightBoundType" type="'.JText::_('TITLE_EQUIDISTANT_INTERVAL_RIGHTBOUND').'">';
        echo '<option value="closed" title="closed" ';
        if ($closureRight=='closed'){echo ' selected="selected" ';}
        echo '>&gt;</option>';
        echo '<option value="open" title="open" ';
        if ($closureRight=='open'){echo ' selected="selected" ';}
        echo '>)</option>';
        echo '</select></td><td><input name="rightBoundValue" value="'.floatval((string)$equidistant->End[0]).'" type="'.JText::_('TITLE_EQUIDISTANT_INTERVAL_RIGHTVALUE').'" /></td></tr>';
        echo '<tr><td>Step:</td><td colspan="2"><input name="step" value="'.floatval((string)$equidistant->Step[0]).'" /></td></tr>';
        echo '</table>';
        ?>
        
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="phId" value="<?php echo $phId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
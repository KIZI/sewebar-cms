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
           
class BkefViewAddPreprocessingHint extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.JText::_('ADD_PREPROCESSING_HINT_H1').'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      $phId=intval($this->phId);
      
      ?>
      
      <form action="index.php?option=com_bkef&amp;task=addPreprocessingHint" method="post" target="_parent" >
        <?php echo JText::_('NAME');?>:<br />
        <input type="text" name="name" title="<?php echo JText::_('TITLE_ADD_PREPROCESSING_HINT_NAME'); ?>" /><br />
        <?php echo JText::_('PREPROCESSING_HINT_NEW_TYPE');?>:<br />
        <select name="type">
          <option value="ExhaustiveEnumeration"><?php echo JText::_('EXHAUSTIVE_ENUMERATION');?></option>
          <option value="IntervalEnumeration"><?php echo JText::_('INTERVAL_ENUMERATION');?></option>
          <option value="EquifrequentInterval"><?php echo JText::_('EQUIFREQUENT_INTERVAL');?></option>
          <option value="Equidistant"><?php echo JText::_('EQUIDISTANT');?></option>
        </select><br />
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $fId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" id="potvrzeni" />
        <input type="submit" value="Uložit..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>
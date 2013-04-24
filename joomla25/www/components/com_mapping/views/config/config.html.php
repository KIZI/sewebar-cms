<?php
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

 
class MappingViewConfig extends JView
{
  
  
  function display($tpl = null)
  {        
    /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_mapping::_DEFAULT();
    }else{
      echo '<div class="componentheading">'.JText::_('COM_MAPPING').'</div>';
      $doc = &JFactory::getDocument();
      $doc->addStyleSheet('components/com_mapping/css/general.css');
      $doc->addStyleSheet('components/com_mapping/css/component.css');
    } 
    /**/
    
    echo '<h1>'.JText::_('SETTINGS').'</h1>';
    if (@$this->message!=""){
      echo '<div class="message">'.$this->message.'</div>';
    }
    $configModel=$this->configModel;
    echo '<form method="post" action="#">';
    /**váhy jednotlivých mapovacích subkomponent**/
    echo '<h2>Mapovací subkomponenty</h2>';
    echo '<div class="warning">'.JText::_('WARNING_COEFICIENTS').'</div>';
    echo '<table>';
    $matchRates=$configModel->loadConfigs("matchRate");
    if (count($matchRates)>0){
      foreach ($matchRates as $matchRate) {
      	echo '<tr><td>'.$matchRate->name.'</td><td><input type="text" name="config#matchRate#'.$matchRate->name.'" value="'.$matchRate->value.'" /></td><td class="description">'.JText::_($matchRate->description).'</td></tr>';
      }
    }
    echo '</table>';
    /**mapovací třídy**/
    echo '<h2>'.JText::_('ASSIGN_CLASSES').'</h2>';
    echo '<div class="warning">'.JText::_('WARNING_CLASS_SELECTION').'</div>';
    echo '<table>';
    $mappingClasses=$configModel->loadConfigs("assignClass");
    if (count($mappingClasses)>0){
      foreach ($mappingClasses as $mappingClass) {
      	echo '<tr><td class="name">'.$mappingClass->name.'</td><td>';
      	echo '<select name="config#assignClass#'.$mappingClass->name.'">';
      	echo '<option value="0"'.(($mappingClass->value==0)?' selected="selected"':'').'>'.JText::_('DISABLED').'</option>';
      	echo '<option value="1"'.(($mappingClass->value==1)?' selected="selected"':'').'>'.JText::_('ALLOWED').'</option>';
      	echo '<option value="2"'.(($mappingClass->value==2)?' selected="selected"':'').'>'.JText::_('DEFAULT').'</option>';
        echo '</select>';
        echo '</td><td class="description">'.JText::_($mappingClass->description).'</td></tr>';
      }
    }
    echo '</table>';
    /**mapovací třídy**/
    echo '<h2>'.JText::_('VALUES_ASSIGN_CLASSES').'</h2>';
    echo '<div class="warning">'.JText::_('WARNING_CLASS_SELECTION').'</div>';
    echo '<table>';
    $valuesMappingClasses=$configModel->loadConfigs("valuesAssignClass");
    if (count($valuesMappingClasses)>0){
      foreach ($valuesMappingClasses as $valuesMappingClass) {
      	echo '<tr><td class="name">'.$valuesMappingClass->name.'</td><td>';
      	echo '<select name="config#valuesAssignClass#'.$valuesMappingClass->name.'">';
      	echo '<option value="0"'.(($valuesMappingClass->value==0)?' selected="selected"':'').'>'.JText::_('DISABLED').'</option>';
      	echo '<option value="1"'.(($valuesMappingClass->value==1)?' selected="selected"':'').'>'.JText::_('ALLOWED').'</option>';
      	echo '<option value="2"'.(($valuesMappingClass->value==2)?' selected="selected"':'').'>'.JText::_('DEFAULT').'</option>';
        echo '</select>';
        echo '</td><td class="description">'.JText::_($valuesMappingClass->description).'</td></tr>';
      }
    }
    echo '</table>';
    /**nastavení konstant**/
    echo '<h2>'.JText::_('CONSTANTS').'</h2>';
    echo '<div class="warning">'.JText::_('WARNING_CONSTANTS_SETTINGS').'</div>';
    echo '<table>';
    $constants=$configModel->loadConfigs("constant");
    if (count($constants)>0){
      foreach ($constants as $constant) {
      	echo '<tr><td>'.$constant->name.'</td><td><input type="text" name="config#constant#'.$constant->name.'" value="'.$constant->value.'"></td><td>'.JText::_($constant->description).'</td></tr>';
      }
    }
    echo '</table>';
    /****/
    echo '<input type="hidden" name="submitConfig" value="ok" />';
    echo '<input type="submit" value="'.JText::_('SAVE_SETTINGS').'" />&nbsp;<input type="reset" value="'.JText::_('RESET_SETTINGS').'" />';
    echo '</form>';
       
  }
  
}
?>

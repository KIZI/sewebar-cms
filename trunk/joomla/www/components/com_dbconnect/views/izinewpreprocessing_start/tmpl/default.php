<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('NEW_PREPROCESSING_FOR').' '.htmlspecialchars($this->pmmlName).'</h1>';
  
  echo '<div class="bigButtonsDiv">';      
  if (strtolower($this->format->DataType)=='string'){
    //jde o stringy 
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_nominalEnumeration&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('NOMINAL_ENUMERATION').'</a>';
  }else{
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_intervalEnumeration&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('INTERVAL_ENUMERATION').'</a>';
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_equidistantInterval&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('EQUIDISTANT_INTERVAL').'</a>';
  }
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newPreprocessingHint_eachValueOneCategory&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('EACH_VALUE_ONE_CATEGORY').'</a>';        
        
  echo '</div>';
?>
<?php 



defined('_JEXEC') or die('Restricted access');
  
  echo '<div id="iziDiv">';
  echo '<a onclick="parent.close();" href="#" class="backButton">'.JText::_('CANCEL').'</a>';
            
  
  echo '<h1 style="margin-top:50px;">'.JText::_('NEW_ATTRIBUTE_USING_COLUMN').' '.htmlspecialchars($this->pmmlName).'</h1>';
  echo '<div class="bigButtonsDiv">'; 
    if (count(@$this->format->PreprocessingHints->DiscretizationHint)>0){
      echo '<h2>'.JText::_('EXISTING_PREPROCESSINGS_FOR_THIS').'</h2>';
      foreach ($this->format->PreprocessingHints->DiscretizationHint as $discretizationHint) {
      	echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=showPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&tmpl=component&pmmlName='.$this->pmmlName.'&taskId='.$this->taskId.'&preprocessingName='.urlencode(((string)$discretizationHint->Name))).'">';
        echo ((string)$discretizationHint->Name);
        echo ' <span class="preprocessingHintType">('.$this->discretizationHintType($discretizationHint).')</span> ';
        echo '</a>';
      }
    }
    echo '<h2>'.JText::_('DEFINE_NEW_PREPROCESSING').'</h2>';      
    if (strtolower($this->format->DataType)=='string'){
      //jde o stringy 
      echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_nominalEnumeration&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('NOMINAL_ENUMERATION').'</a>';
    }else{
      echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_intervalEnumeration&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('INTERVAL_ENUMERATION').'</a>';
      echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_equidistantInterval&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('EQUIDISTANT_INTERVAL').'</a>';
      echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_nominalEnumeration&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('NOMINAL_ENUMERATION').'</a>';
    }
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newPreprocessingHint_eachValueOneCategory&tmpl=component&col='.urlencode($this->pmmlName).'&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&taskId='.$this->taskId).'">'.JText::_('EACH_VALUE_ONE_CATEGORY').'</a>';

//    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.$this->pmmlName.'&tmpl=component&taskId='.$this->taskId).'">'.JText::_('DEFINE_NEW_PREPROCESSING').'</a></td>';
    echo '</div>';
    
  echo '</div>';
  
?>
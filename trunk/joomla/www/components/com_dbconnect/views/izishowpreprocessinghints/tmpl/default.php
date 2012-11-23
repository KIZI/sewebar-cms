<?php 



defined('_JEXEC') or die('Restricted access');
  
  echo '<div id="iziDiv">';
  echo '<a onclick="parent.close();" href="#" class="backButton">'.JText::_('CANCEL').'</a>';
            
  
  echo '<h1 style="margin-top:50px;">'.JText::_('NEW_ATTRIBUTE_USING_COLUMN').' '.htmlspecialchars($this->pmmlName).'</h1>';
  echo '<div class="bigButtonsDiv">';
    echo '<div class="infoDiv">'.JText::_('SELECT_PREPROCESSING_HINT_INFO').'</div>';
    if (count($this->preprocessingHints->DiscretizationHint)>0){
      foreach ($this->preprocessingHints->DiscretizationHint as $discretizationHint) {
      	echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=showPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&tmpl=component&pmmlName='.$this->pmmlName.'&taskId='.$this->taskId.'&preprocessingName='.urlencode(((string)$discretizationHint->Name))).'">';
        echo ((string)$discretizationHint->Name);
        echo ' <span class="preprocessingHintType">('.$this->discretizationHintType($discretizationHint).')</span> ';
        echo '</a>';
      }
    }else{
      echo '<div class="infoDiv">'.JText::_('NO_PREPROCESSING_HINTS_FOUND').'</div>';
    }
    echo '<hr />';
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.$this->pmmlName.'&tmpl=component&taskId='.$this->taskId).'">'.JText::_('DEFINE_NEW_PREPROCESSING').'</a></td>';
    echo '</div>';
    
  echo '</div>';
  
?>
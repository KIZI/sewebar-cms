<?php 



defined('_JEXEC') or die('Restricted access');
  
  echo '<h1>'.JText::_('SELECT_PREPROCESING_HINT').'</h1>';
  
  if (count($this->preprocessingHints->DiscretizationHint)>0){
    echo '<table class="myAdminTable">';
    echo '<tr><th style="width:200px;">'.JText::_('NAME').'</th><th style="width:200px;">'.JText::_('DISCRETIZATION_TYPE').'</th><th>'.JText::_('ACTIONS').'</th></tr>';
    $rowClass=0;
    foreach ($this->preprocessingHints->DiscretizationHint as $discretizationHint) {
      echo '<tr class="row'.$rowClass.'">';
      echo '<td>'.((string)$discretizationHint->Name).'</td>';
      echo '<td>'.$this->discretizationHintType($discretizationHint).'</td>';
      echo '<td class="actionsTd"><a href="'.JRoute::_('index.php?option=com_dbconnect&task=selectPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.$this->pmmlName.'&taskId='.$this->taskId.'&preprocessingName='.urlencode(((string)$discretizationHint->Name))).'" target="_parent">'.JText::_('SELECT').'</a></td>';
      echo '</tr>';
      $rowClass=($rowClass+1)%2;
    }     
    echo '<tr>';
      echo '<td colspan="2">'.JText::_("IGNORE_PREPROCESSING").'</td>';
      echo '<td class="actionsTd"><a href="'.JRoute::_('index.php?option=com_dbconnect&task=selectPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.$this->pmmlName.'&taskId='.$this->taskId.'&preprocessingName=').'" target="_parent">'.JText::_('SELECT').'</a></td>';
      echo '</tr>';
    echo '</table>';
  }else{
    echo '<div class="error">'.JText::_('NO_PREPROCESSING_HINTS_FOUND').'</div>';
  }
  
  
  
  
  
  
  //TODO dodělat vypsání nepoužitých položek z TASK i BKEF
  
  
  
  
?>
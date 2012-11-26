<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId.'&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_NOMINAL_ENUMERATION').'</h1>';
  echo '  <table>
            <tr>
              <td>'.JText::_('DATAFIELD').'</td>
              <td><strong>'.htmlspecialchars($this->pmmlName).'</strong></td>
            </tr>
            <tr>
              <td>
                <label for="attributeName">'.JText::_('PREPROCESSING_NAME').'</label>
              </td>
              <td>
                <strong id="attributeName">'.htmlspecialchars((string)@$this->preprocessingHint->Name).'</strong>
              </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
            <tr>
            <tr>
              <td>'.JText::_('CREATED_LAST_MODIFIED').'</td>
              <td>';
              echo date(JText::_(DATETIME_FORMAT),strtotime(@$this->preprocessingHint->Created->Timestamp));
              if ((string)@$this->preprocessingHint->Created->Timestamp!=(string)@$this->preprocessingHint->LastModified->Timestamp){
                echo ' / '.date(JText::_(DATETIME_FORMAT),strtotime($this->preprocessingHint->LastModified->Timestamp));  
              }
  echo       '</td>
            </tr>
          </table>';
  
  if (count(@$this->preprocessingHint->NominalEnumeration->NominalBin)>0){
    foreach ($this->preprocessingHint->NominalEnumeration->NominalBin as $nominalBin){
    	echo '<div class="binDiv">';
      echo '<h3>'.@$nominalBin->Name.'</h3>'; 
      if (count($nominalBin->Value)>0){
        echo '<ul class="binValuesUl">';
        foreach ($nominalBin->Value as $value) {
        	echo '<li>'.$value.'</li>';
        }
        echo '</ul>';
      }else{
        echo '<div>'.JText::_('NO_VALUES_FOUND').'</div>';
      }  
      echo '</div>';
    }
  }else{
    echo '<div class="warnDiv">'.JText::_('NO_BINS_FOUND').'</div>';
  }
          
  echo   '<div class="formActionsDiv">
            <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=selectPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&preprocessingName='.urlencode((string)$this->preprocessingHint->Name).'&taskId='.urlencode($this->taskId)).'" class="button">'.JText::_('SELECT_PREPROCESSING').'</a>
            <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=editPreprocessingHint_nominalEnumeration&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&oldPhName='.urlencode((string)$this->preprocessingHint->Name).'&taskId='.urlencode($this->taskId)).'" class="button">'.JText::_('EDIT_PREPROCESSING').'</a>
          </div>
          
        ';
?>
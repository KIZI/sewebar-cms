<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId.'&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_INTERVAL_ENUMERATION').'</h1>';
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
                <strong>'.htmlspecialchars((string)@$this->preprocessingHint->Name).'</strong>
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
          
  if (count(@$this->preprocessingHint->IntervalEnumeration->IntervalBin)>0){
    foreach ($this->preprocessingHint->IntervalEnumeration->IntervalBin as $intervalBin){
    	echo '<div class="groupDiv">';
      echo '<h3>'.@$intervalBin->Name.'</h3>';
      if (count($intervalBin->Interval)>0){
        echo '<ul class="binValuesUl">';
        foreach ($intervalBin->Interval as $interval){
        	echo '<li>';
          if (substr(@$interval['closure'],0,4)=='open'){
            echo JText::_('INTERVAL_LEFT_OPEN');
            $closureRight=substr(@$interval['closure'],4);
          }else{
            echo JText::_('INTERVAL_LEFT_CLOSED');
            $closureRight=substr(@$interval['closure'],6);
          }  
          echo $interval['leftMargin'].' ; '.$interval['rightMargin'];
          if ($closureRight=='Closed'){
            echo JText::_('INTERVAL_RIGHT_CLOSED');
          }else{
            echo JText::_('INTERVAL_RIGHT_OPEN');
          }
          echo '</li>';
        }
        echo '</ul>';
      }else{
        echo '<div>'.JText::_('NO_INTERVALS_FOUND').'</div>';
      }  
      echo '</div>';
    }
  }else{
    echo '<div class="warnDiv">'.JText::_('NO_BINS_FOUND').'</div>';
  }              

  echo '  <div class="formActionsDiv">
            <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=selectPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&preprocessingName='.urlencode((string)$this->preprocessingHint->Name).'&taskId='.urlencode($this->taskId)).'" class="button">'.JText::_('SELECT_PREPROCESSING').'</a>
            <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=editPreprocessingHint_nominalEnumeration&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&oldPhName='.urlencode((string)$this->preprocessingHint->Name).'&taskId='.urlencode($this->taskId)).'" class="button">'.JText::_('EDIT_PREPROCESSING').'</a>
          </div>
          
        ';
?>
<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if ($this->oldPhName!=''){
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=showPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&preprocessingName='.urlencode((string)$this->oldPhName).'&taskId='.urlencode($this->taskId)).'" class="backButton">'.JText::_('BACK').'</a>';
  }else{
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId.'&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  }
  
  
  echo '<h1>'.JText::_('PREPROCESSING_EQUIDISTANT_INTERVALS').'</h1>';
  echo '<form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=editPreprocessingHint_equidistantInterval').'">
          <table>
            <tr>
              <td>'.JText::_('DATAFIELD').'</td>
              <td><strong>'.htmlspecialchars($this->pmmlName).'</strong></td>
            </tr>';
  if ($this->oldPhName!=''){
    echo '  <tr>
              <td>'.JText::_('OLD_PH_NAME').'</td>
              <td><strong>'.htmlspecialchars($this->oldPhName).'</strong></td>
            </tr>';
  }            
  echo '    <tr>
              <td>
                <label for="attributeName">'.JText::_('ATTRIBUTE_NAME').'</label>
              </td>
              <td>
                <input type="text" name="attributeName" id="attributeName" value="'.htmlspecialchars(((@$this->attributeName!='')?$this->attributeName:$this->pmmlName.': equidistant')).'" />
              </td>
            </tr>
            <tr>
              <td>
                <label for="start">'.JText::_('START').'</label>
              </td>
              <td>
                <input type="text" name="start" id="start" value="'.@$this->start.'" />
              </td>
            </tr>
            <tr>
              <td>
                <label for="end">'.JText::_('END').'</label>
              </td>
              <td>
                <input type="text" name="end" id="end" value="'.@$this->end.'" />
              </td>
            </tr>
            <tr>
              <td>
                <label for="step">'.JText::_('STEP').'</label>
              </td>
              <td>
                <input type="text" name="step" id="step" value="'.@$this->step.'" />
              </td>
            </tr>
          </table>
          <div class="formActionsDiv">
            <input type="submit" value="'.JText::_('SAVE_PREPROCESSING').'">
          </div>
          <input type="hidden" name="col" value="'.htmlspecialchars($this->pmmlName).'" />
          <input type="hidden" name="maName" value="'.htmlspecialchars($this->maName).'" />
          <input type="hidden" name="formatName" value="'.htmlspecialchars($this->formatName).'" />
          <input type="hidden" name="oldPhName" value="'.htmlspecialchars($this->oldPhName).'" />
          <input type="hidden" name="task_id" value="'.$this->taskId.'" />
        </form>';
?>
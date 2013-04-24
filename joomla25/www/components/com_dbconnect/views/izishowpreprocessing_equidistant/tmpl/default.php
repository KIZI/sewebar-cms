<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId.'&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_EQUIDISTANT_INTERVALS').'</h1>';
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
              <td>
                <label for="start">'.JText::_('START').'</label>
              </td>
              <td>
                <strong>'.(string)@$this->preprocessingHint->EquidistantInterval->Start.'</strong>
              </td>
            </tr>
            <tr>
              <td>
                <label for="end">'.JText::_('END').'</label>
              </td>
              <td>
                <strong>'.(string)@$this->preprocessingHint->EquidistantInterval->End.'</strong>
              </td>
            </tr>
            <tr>
              <td>
                <label for="step">'.JText::_('STEP').'</label>
              </td>
              <td>
                <strong>'.(string)@$this->preprocessingHint->EquidistantInterval->Step.'</strong>
              </td>
            </tr>
          </table>

          <div class="formActionsDiv">
            <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=selectPreprocessingHint&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&preprocessingName='.urlencode((string)$this->preprocessingHint->Name).'&taskId='.urlencode($this->taskId)).'" class="button">'.JText::_('SELECT_PREPROCESSING').'</a>
            <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&tmpl=component&task=editPreprocessingHint_equidistantInterval&maName='.urlencode($this->maName).'&formatName='.urlencode($this->formatName).'&pmmlName='.urlencode($this->pmmlName).'&oldPhName='.urlencode((string)$this->preprocessingHint->Name).'&taskId='.urlencode($this->taskId)).'" class="button">'.JText::_('EDIT_PREPROCESSING').'</a>
          </div>
          
        ';
?>
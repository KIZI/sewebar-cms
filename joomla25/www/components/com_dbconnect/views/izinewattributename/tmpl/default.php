<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  //TODO dodělat zavírací tlačítko!!!
  //echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('NEW_ATTRIBUTE_USING_PREPROCESSING').'</h1>';
  echo '<form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=selectPreprocessingHint').'">
          <table>
            <tr>
              <td>'.JText::_('DATAFIELD').'</td>
              <td><strong>'.htmlspecialchars($this->pmmlName).'</strong></td>
            </tr>
            <tr>
              <td>'.JText::_('PREPROCESSING').'</td>
              <td><strong>'.htmlspecialchars($this->preprocessingName).'</strong></td>
            </tr>
            <tr>
              <td>
                <label for="attributeName">'.JText::_('ATTRIBUTE_NAME').'</label>
              </td>
              <td>
                <input type="text" name="attributeName" id="attributeName" value="'.htmlspecialchars($this->pmmlName).':'.htmlspecialchars($this->preprocessingName).'" />
              </td>
            </tr>
          </table>
          <div class="formActionsDiv">
            <input type="submit" value="'.JText::_('CREATE_ATTRIBUTE').'">
          </div>
          <input type="hidden" name="preprocessingName" value="'.htmlspecialchars($this->preprocessingName).'" />
          <input type="hidden" name="col" value="'.htmlspecialchars($this->pmmlName).'" />
          <input type="hidden" name="maName" value="'.htmlspecialchars($this->maName).'" />
          <input type="hidden" name="formatName" value="'.htmlspecialchars($this->formatName).'" />
          <input type="hidden" name="taskId" value="'.$this->taskId.'" />
        </form>';
?>
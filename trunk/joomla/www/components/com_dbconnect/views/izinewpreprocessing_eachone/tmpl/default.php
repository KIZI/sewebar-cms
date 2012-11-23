<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('PREPROCESSING_EACH_VALUE_ONE_CATEGORY').'</h1>';
  echo '<form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newPreprocessingHint_eachValueOneCategory').'">
          <table>
            <tr>
              <td>'.JText::_('DATAFIELD').'</td>
              <td><strong>'.htmlspecialchars($this->pmmlName).'</strong></td>
            </tr>
            <tr>
              <td>
                <label for="attributeName">'.JText::_('ATTRIBUTE_NAME').'</label>
              </td>
              <td>
                <input type="text" name="attributeName" id="attributeName" value="'.htmlspecialchars($this->pmmlName).': each-one" />
              </td>
            </tr>
          </table>
          <div class="formActionsDiv">
            <input type="submit" value="'.JText::_('SAVE_PREPROCESSING').'">
          </div>
          <input type="hidden" name="col" value="'.$this->pmmlName.'" />
          <input type="hidden" name="maName" value="'.$this->maName.'" />
          <input type="hidden" name="formatName" value="'.$this->formatName.'" />
          <input type="hidden" name="task_id" value="'.$this->taskId.'" />
        </form>';
?>
<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newAttribute&col='.urlencode($this->pmmlName).'&taskId='.$this->taskId).'&tmpl=component" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<script type="text/javascript">
          window.addEvent(\'domready\',function() {
              getAttributesNames(\''.$this->kbiId.'\');
            }
          );
        </script>';
  
  echo '<h1>'.JText::_('PREPROCESSING_EACH_VALUE_ONE_CATEGORY').'</h1>';
  echo '<form method="post" onsubmit="return eachoneInputCheck();" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newPreprocessingHint_eachValueOneCategory').'">
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
                <input type="text" name="attributeName" id="attributeName" value="'.htmlspecialchars($this->pmmlName).': each-one" onkeyup="checkAttributeNameShow();" />
                <span id="attributeNameExists">'.JText::_('ATTRIBUTE_NAME_EXISTS').'</span>
                <span id="attributeNameNotSet">'.JText::_('ATTRIBUTE_NAME_NOT_SET').'</span>
                <span id="attributeNameNotChecked">'.JText::_('ATTRIBUTE_NAME_NOT_CHECKED').'</span>
              </td>
            </tr>
          </table> <br /><br />
          <div class="formActionsDiv">
            <input type="submit" value="'.JText::_('SAVE_PREPROCESSING').'">
          </div>
          <input type="hidden" name="col" value="'.$this->pmmlName.'" />
          <input type="hidden" name="maName" value="'.$this->maName.'" />
          <input type="hidden" name="formatName" value="'.$this->formatName.'" />
          <input type="hidden" name="task_id" value="'.$this->taskId.'" />
        </form>';
?>
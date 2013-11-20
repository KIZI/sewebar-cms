<?php 
defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('CONFIG_VALUES').'</h1>';
  if (count($this->configItems)){
    echo '<form method="POST">
          <input type="hidden" name="option" value="com_dbconnect" />
          <input type="hidden" name="controller" value="config" />
          <input type="hidden" name="task" value="saveConfigList" />
          <table>
            <tr>
              <th>'.JText::_('NAME').'</th>
              <th>'.JText::_('VALUE').'</th>
            </tr>';

    foreach ($this->configItems as $configItem){
      echo '<tr>
              <td>
                <label for="config_'.$configItem->name.'">'.JText::_($configItem->title).'</label>
              </td>
              <td>
                <input type="text" name="config_'.$configItem->name.'" id="config_'.$configItem->name.'" value="'.htmlspecialchars($configItem->value).'" />
              </td>
            </tr>';
    }
    echo '<tr>
            <td colspan="2">
              <input type="submit" value="'.JText::_('SAVE').'" />
              <input type="reset" value="'.JText::_('RESET').'" />
            </td>
          </tr>';
    echo '</table>
          </form>';
  }


?>


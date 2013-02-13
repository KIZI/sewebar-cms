<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component&close='.JRequest::getVar('close','')).'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('TASK_DETAILS').'</h1>';
  
  echo '<h2>'.htmlspecialchars($this->task->name).'</h2>';
  echo '<table>
          <tr>
            <td>'.JText::_('CREATED').'</td>
            <td><strong>'.date(JText::_('DATETIME_FORMAT'),strtotime($this->task->created)).'</strong></td>
          </tr>
          <tr>
            <td colspan="2"><h3>'.JText::_('DB_TABLE_DETAILS').'</h3></td>
          </tr>
          <tr>
            <td>'.JText::_('DB_NAME').'</td>
            <td><strong>'.$this->connection->db_name.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('DB_TABLE').'</td>
            <td><strong>'.$this->connection->table.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('DB_SERVER').'</td>
            <td><strong>'.$this->connection->server.'</strong></td>
          </tr>
        </table>';
      
  echo '<div class="bigButtonsDiv">
          <a href="'.str_replace(array('{$server}','{$1}'), array('http://'.$_SERVER['HTTP_HOST'],$kbiSource), JText::_('IZI_MINER_URL')).'" target="_parent">'.JText::_('RUN_TASK').'</a>
        </div>';
  
  
  echo '</div>';
?>
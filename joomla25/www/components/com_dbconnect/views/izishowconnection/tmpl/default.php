<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listConnections&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('DB_CONNECTION_DETAILS').'</h1>';
  
  echo '<h2>'.htmlspecialchars($this->task->name).'</h2>';
  echo '<table class="centerTable">
          <tr>
            <td>'.JText::_('CREATED').'</td>
            <td><strong>'.date(JText::_('DATETIME_FORMAT'),strtotime($this->connection->created)).'</strong></td>
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
      
  echo '<div class="center"  style="margin-top:20px;">
          <a class="bigButton" href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newDmTask&tmpl=component&connection_id='.$this->connection->id).'">'.JText::_('CREATE_NEW_TASK').'</a>
        </div>';
  
  
  echo '</div>';
?>
<?php 
  defined('_JEXEC') or die('Restricted access');
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newDatasource').'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('DB_CONNECTIONS').'</h1>';
  $headerLink='index.php?option=com_dbconnect&task=listConnections&order=';
  if ($this->connections&&(count($this->connections)>0)){
    echo '<table>';
    echo '<tr>
            <th><a href="'.JRoute::_($headerLink.'db_type').'">'.JText::_('DB_TYPE').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'server').'">'.JText::_('DB_SERVER').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'username').'">'.JText::_('DB_USER').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'db_name').'">'.JText::_('DB_NAME').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'table').'">'.JText::_('DB_TABLE').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'shared').'">'.JText::_('SHARED').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'created').'">'.JText::_('CREATED_MODIFIED').'</a></th>
            <th>'.JText::_('ACTIONS').'</th>
          </tr>';
    foreach ($this->connections as $connection) {
    	echo '<tr>
              <td>'.$connection->db_type.'</td>
              <td>'.$connection->server.'</td>
              <td>'.$connection->username.'</td>
              <td>'.$connection->db_name.'</td>
              <td>'.$connection->table.'</td>
              <td>'.($connection->shared?'ok':'-').'</td>
              <td>'.date(JText::_('DATETIME_FORMAT',strtotime($connection->created))).'</td>
              <td>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newDMTask&connection_id='.$connection->id).'">'.JText::_('NEW_DM_TASK').'</a>';
      echo'   </td>
            </tr>';
    }      
    echo '</table>';
  }else{
    echo '<div>'.JText::_('NO_DB_CONNECTIONS_FOUND_INFO').'</div>';
  }
?>
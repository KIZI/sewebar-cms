<?php 
/**
* @package helloworld02
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DB_CONNECTIONS').'</h1>';
  
  echo '<div class="actionsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&task=newDatabase').'">'.JText::_('SETUP_NEW_DB_CONNECTION').'...</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&task=newDatabase&quickDMTask=ok').'">'.JText::_('SETUP_NEW_QUICK_CONNECTION').'</a>
        </div>';
  
  $headerLink='index.php?option=com_dbconnect&task=listConnections&order=';
  if ($this->connections&&(count($this->connections)>0)){
    echo '<table class="myAdminTable looser">';
    echo '<tr>
            <th><a href="'.JRoute::_($headerLink.'db_type').'">'.JText::_('DB_TYPE').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'server').'">'.JText::_('DB_SERVER').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'username').'">'.JText::_('DB_USER').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'db_name').'">'.JText::_('DB_NAME').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'table').'">'.JText::_('DB_TABLE').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'shared').'">'.JText::_('SHARED').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'created').'">'.JText::_('CREATED_MODIFIED').'</a></th>
            <th>Akce</th>
          </tr>';
    $rowClass=0;
    foreach ($this->connections as $connection) {
    	echo '<tr class="row'.$rowClass.'">
              <td>'.$connection->db_type.'</td>
              <td>'.$connection->server.'</td>
              <td>'.$connection->username.'</td>
              <td>'.$connection->db_name.'</td>
              <td>'.$connection->table.'</td>
              <td>'.($connection->shared?'ok':'-').'</td>
              <td>'.date(JText::_('DATETIME_FORMAT'),strtotime($connection->created)).'</td>
              <td class="actionsTd">
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=showTable&tmpl=component&connection_id='.$connection->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('SHOW_DB_TABLE_PREVIEW').'</a>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=newDMTask&connection_id='.$connection->id).'">'.JText::_('NEW_DM_TASK').'</a>';
      if ($connection->uid==$this->userId){
        echo '  <a href="'.JRoute::_('index.php?option=com_dbconnect&task=shareConnection&connection_id='.$connection->id).'">'.($connection->shared?(JText::_('DISABLE_SHARING')):(JText::_('ENABLE_SHARING'))).'</a>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=deleteConnection&tmpl=component&connection_id='.$connection->id).'" class="modal"  rel="{handler: \'iframe\', size: {x: 320, y: 250}}">'.JText::_('DELETE').'</a>';
      }      
      echo'   </td>
            </tr>';
      $rowClass=($rowClass+1)%2;      
    }      
    echo '</table>';
  }else{
    echo '<div class="info">'.JText::_('NO_DB_CONNECTIONS_FOUND_INFO').'</div>';
  }
  
?>
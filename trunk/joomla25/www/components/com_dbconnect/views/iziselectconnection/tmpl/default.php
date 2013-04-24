<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('MYSQL_DB_CONNECTIONS').'</h1>';
  $headerLink='index.php?option=com_dbconnect&task=listConnections&order=';
  echo '<h2 class="center">'.JText::_('EXISTING_DB_CONNECTIONS').'</h2>';
  if ($this->connections&&(count($this->connections)>0)){
    echo '<div class="bigButtonsDiv">';
    foreach ($this->connections as $connection) {
    	echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=showConnection&connection_id='.$connection->id.'&tmpl=component').'">';
      echo '  <span class="name">'.htmlspecialchars($connection->db_name).'.<strong>'.htmlspecialchars($connection->table).'</strong></span>';
      echo '  <span class="server">'.strtolower(JText::_('USER')).': '.htmlspecialchars($connection->username).', '.strtolower(JText::_('SERVER')).': '.htmlspecialchars($connection->server).'</span>';
      echo '</a>';
    }
    echo '</div>';
  }else{
    echo '<div class="center">'.JText::_('NO_DB_CONNECTIONS_FOUND_INFO').'</div>';
  }
  echo '<h2 class="center">'.JText::_('NEW_DB_CONNECTION').'</h2>';
  echo '<div class="bigButtonsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newDatabase&tmpl=component').'">'.JText::_('SETUP_NEW_DB_CONNECTION').'</a>
        </div>';    
  
  echo '</div>';
?>
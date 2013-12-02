<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('SELECT_GROUP_FOR_USER').' '.$this->userGroup->title.'</h1>';
  
  if (($this->groups)&&(count($this->groups)>0)){
    echo '<table class="myAdminTable">';
    echo '<tr>
            <th style="width:200px;">'.JText::_("TITLE").'</th>
            <th>'.JText::_("USERS_COUNT").'</th>
            <th>'.JText::_("ACTIONS").'</th>
          </tr>';
    foreach ($this->groups as $group) {
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td>'.$group->title.'</td>
              <td>'.$group->usersCount.'</td>
              <td>';  
      if ($group->id==$this->currentUserGroup->id){
        echo '<strong>'.JText::_("CURRENT_GROUP").'</strong>';
      }else{
        echo '<a target="_parent" class="button" href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=selectUsersUserGroup&user=".$this->user->id."&group=".$group->id).'" >'.JText::_('SELECT').'</a>'; 
      }        
      echo '  </td>
            </tr>';
      $row=($row+1)%2;      
    }      
    echo '</table>';      
  }else{
    echo '<div class="legendDiv">'.JText::_("NO_USER_GROUPS_FOUND").'</div>';
  }
        
?>
<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('ADD_USERS_INTO_GROUP').' '.$this->userGroup->title.'</h1>';
  
  if (($this->users)&&(count($this->users)>0)){
    echo '<table class="myAdminTable">';
    echo '<tr>
            <th>'.JText::_("NAME").'</th>
            <th>'.JText::_("USERNAME").'</th>
            <th>'.JText::_("EMAIL").'</th>
            <th>'.JText::_("ACTIONS").'</th>
          </tr>';
    foreach ($this->users as $user) {
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td>'.$user->name.'</td>
              <td>'.$user->username.'</td>
              <td>'.$user->email.'</td>
              <td>
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=addUserIntoGroup&user=".$user->id."&group=".$this->userGroup->id).'" >'.JText::_('ADD').'</a>
              </td>
            </tr>';
      $row=($row+1)%2;      
    }      
    echo '</table>';      
  }else{
    echo '<div class="legendDiv">'.JText::_("NO_USERS_FOUND").'</div>';
  }
        
?>
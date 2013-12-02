<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('USERS').'</h1>';
                                              
  echo '<div>'.JText::_("PARENT_USER_GROUP").': <strong>'.@$this->parentUserGroup->title.'</strong> <a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=selectParentUserGroup').'">'.JText::_("SELECT_OTHER").'</a></div>';
  
  echo '<h2>'.JText::_("EXISTING_USERS").'</h2>';
  
  if ($this->users){
    echo '<table class="myAdminTable">';
    echo '<tr>
            <th style="width:130px;">'.JText::_("NAME").'</th>
            <th style="width:130px;">'.JText::_("USERNAME").'</th>
            <th style="width:160px;">'.JText::_("EMAIL").'</th>
            <th>'.JText::_("GROUP").'</th>
            <th>'.JText::_("ACTIONS").'</th>
          </tr>';
    foreach ($this->users as $user) {
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td>'.$user->name.'</td>
              <td>'.$user->username.'</td>
              <td>'.$user->email.'</td>
              <td>';
      $userGroup=$this->adminModel->getUsersGroup($user->id,$this->parentUserGroup->id);
      if ($userGroup){
        echo $userGroup->title;
      }else{
        echo '-';
      }              
      echo    '</td>
              <td class="actionsTd">
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=selectUsersUserGroup&tmpl=component&user=".$user->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 400, y: 400}}">'.JText::_("SELECT_GROUP").'</a>
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=removeUsersUserGroup&tmpl=component&user=".$user->id).'" onclick="return confirm(\''.JText::_("REALLY_REMOVE_QUESTION").'\');">'.JText::_("REMOVE_GROUP").'</a>
              </td>
            </tr>';
      $row=($row+1)%2;      
    }
    echo '</table>';
  }else{
    echo '<div class="legendDiv">'.JText::_("NO_USERS_FOUND").'</dov>';
  }
  
  
  echo '<h2>'.JText::_("ADD_USERS").'</h2>';
  echo '<div class="legendDiv">'.JText::_("ADD_USERS_INFO").'</div>';
  echo '<form method="post" action="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=addUsers").'">
          <div>
            <textarea name="users" class="usersTextArea"></textarea>
          </div>
          <div>
            <input type="hidden" name="potvrzeni" value="ok" />
            <input type="submit" value="'.JText::_("ADD_USERS").'" class="button" />
          </div>
        </form>';
        
  
?>
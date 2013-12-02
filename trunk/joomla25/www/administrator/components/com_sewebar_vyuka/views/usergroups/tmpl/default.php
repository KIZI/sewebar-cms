<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('USER_GROUPS').'</h1>';
                                              
  echo '<div>'.JText::_("PARENT_USER_GROUP").': <strong>'.@$this->parentUserGroup->title.'</strong> <a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=selectParentUserGroup').'">'.JText::_("SELECT_OTHER").'</a></div>';
  
  echo '<h2>'.JText::_("EXISTING_USER_GROUPS").'</h2>';
  
  if ($this->userGroups){
    echo '<table class="myAdminTable">';
    echo '<tr>
            <th style="width:200px;">'.JText::_("TITLE").'</th>
            <th>'.JText::_("USERS_COUNT").'</th>
            <th>'.JText::_("ACTIONS").'</th>
          </tr>';
    foreach ($this->userGroups as $userGroup) {
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td>'.$userGroup->title.'</td>
              <td>'.$userGroup->usersCount.'</td>
              <td class="actionsTd">
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=addUserIntoGroup&tmpl=component&group=".$userGroup->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 400, y: 400}}">'.JText::_("ADD_USER").'</a>
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=usersInGroup&tmpl=component&group=".$userGroup->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 400, y: 400}}">'.JText::_("USERS_IN_GROUP").'</a>
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=deleteUserGroup&tmpl=component&group=".$userGroup->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 400, y: 200}}">'.JText::_("DELETE_GROUP").'</a>
              </td>
            </tr>';
      $row=($row+1)%2;      
    }
    echo '</table>';
  }else{
    echo '<div class="legendDiv">'.JText::_("NO_USER_GROUPS_FOUND").'</dov>';
  }
  
  
  echo '<h2>'.JText::_("ADD_USER_GROUPS").'</h2>';
  echo '<div class="legendDiv">'.JText::_("ADD_USER_GROUPS_INFO").'</div>';
  echo '<form method="post" action="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=addUserGroups").'">
          <div>
            <textarea name="groups" class="groupsTextArea"></textarea>
          </div>
          <div>
            <input type="hidden" name="potvrzeni" value="ok" />
            <input type="submit" value="'.JText::_("ADD_USER_GROUPS").'" class="button" />
          </div>
        </form>';
        
  
?>
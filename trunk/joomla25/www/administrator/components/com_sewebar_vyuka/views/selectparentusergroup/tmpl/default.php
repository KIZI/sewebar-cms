<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('SELECT_PARENT_USER_GROUP').'</h1>';
    
  echo '<h2>'.JText::_("CREATE_NEW_PARENT_USER_GROUP").'</h2>';
  echo '<form method="post" action="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=selectParentUserGroup").'">
          <table>
            <tr>
              <td><label for="newGroupTitle">'.JText::_("TITLE").'</label></td>
              <td><input id="newGroupTitle" name="newGroupTitle" value="" /></td>
              <td><input type="submit" value="'.JText::_("CREATE_GROUP").'" class="button" /></td>
            </tr>
          </table>
          <input type="hidden" name="potvrzeni" value="ok" />
        </form>';
        
        
  
  echo '<h2>'.JText::_("EXISTING_USER_GROUPS").'</h2>';
  
  if ($this->userGroups){                            
    echo '<table class="myAdminTable">';
    echo '<tr>
            <th style="min-width:200px;">'.JText::_("TITLE").'</th>
            <th>'.JText::_("ACTIONS").'</th>
          </tr>';
    foreach ($this->userGroups as $userGroup) {       
    	echo '<tr'.($row==1?' class="row1"':'').'>                           
              <td><strong>'.$userGroup->title.'</strong></td>
              <td class="actionsTd">
                <a href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=selectParentUserGroup&group=".$userGroup->id).'">'.JText::_("SELECT").'</a>
              </td>
            </tr>';
      $row=($row+1)%2;     
    }
    echo '</table>';
  }else{
    echo '<div class="legendDiv">'.JText::_("NO_USER_GROUPS_FOUND").'</dov>';
  }
  

        
  
?>
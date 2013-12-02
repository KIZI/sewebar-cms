<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DELETE_USER_GROUP').'</h1>';
  
  echo '<div>'.JText::_("DELETE_USER_GROUP_QUESTION").'</div>
        <div style="margin:20px;"><strong>'.@$this->userGroup->title.'</strong></div>';
  
 
  echo '<form method="post" target="_parent" action="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=deleteUserGroup").'">
          <div>
            <input type="hidden" name="potvrzeni" value="ok" />
            <input type="hidden" name="group" value="'.$this->userGroup->id.'" />
            <input type="submit" value="'.JText::_("DELETE").'" class="button" /><a target="_parent" href="'.JRoute::_("index.php?option=com_sewebar_vyuka&task=usergroups").'" class="button">'.JText::_("CANCEL").'</a>
          </div>
        </form>';
        
?>
<?php 
defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('CLONE_DM_TASK').'</h1>';
  
  echo '<p>'.JText::_('CLONE_DM_TASK_INFO').' <strong>'.$this->task->name.'</strong>?</p>';
  echo '<form method="post" target="_parent" action="'.JRoute::_('index.php?option=com_dbconnect&task=cloneDMTask').'">
          <p>'.JText::_('NEW_DM_TASK_NAME').'<input type="text" name="name" value="'.$this->taskName.'" style="width:300px;" /></p>
          <input type="hidden" name="task_id" value="'.$this->task->id.'" />
          <p><input type="submit" name="action" value="'.JText::_('CREATE_COPY').'" />&nbsp;<input type="submit" name="action" value="'.JText::_('STORNO').'" /></p>
        </form>';
  /*--*/

?>


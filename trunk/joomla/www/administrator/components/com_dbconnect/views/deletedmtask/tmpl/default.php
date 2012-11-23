<?php 
defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DELETE_DM_TASK').'</h1>';
  
  echo '<p>'.JText::_('DELETE_DM_TASK_QUESTION').' <strong>'.$this->task->name.'</strong>?</p>';
  echo '<form method="post" target="_parent" action="'.JRoute::_('index.php?option=com_dbconnect&task=deleteDMTask').'">
          <input type="hidden" name="task_id" value="'.$this->task->id.'" />
          <input type="submit" name="xx" value="'.JText::_('DELETE').'" />&nbsp;<input type="submit" name="xx" value="'.JText::_('STORNO').'" />
        </form>';
  /*--*/

?>


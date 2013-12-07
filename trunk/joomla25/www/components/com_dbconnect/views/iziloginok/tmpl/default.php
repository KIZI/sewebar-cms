<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<h1>'.$this->title.'</h1>';
  
  echo '<div class="center">'.$this->text.'</div><br />';
  
  echo '<div class="bigButtonsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component&close=no').'">'.JText::_('MINING_TASK_DASHBOARD').'</a>
          <a href="'.JRoute::_('/').'" target="_parent">'.JText::_('GOTO_EASYMINER_HOMEPAGE').'</a>
        </div>';
        
  echo '</div>';
?>
<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  echo '<h1>'.$this->title.'</h1>';
  
  echo '<div class="center">'.$this->text.'</div><br />';
  
  echo '<div class="bigButtonsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component&close=no').'">'.JText::_('START_NEW_TASK').'</a>
          <a href="'.JRoute::_('index.php').'">'.JText::_('GOTO_JOOMLA').'</a>
        </div>';
        
  echo '</div>';
?>
<?php 

  defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('ERROR').'</h1>';

  echo '<div class="error">'.$this->error.'</div>';
  echo '<div class="actionsDiv">
          <a href="javascript:parent.history.go(-1);">&lt; '.JText::_('BACK').'</a>
        </div>';
  
?>

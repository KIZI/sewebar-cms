<?php 

  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
    echo '<a href="javascript:parent.history.go(-1);" class="backButton">&lt; '.JText::_('BACK').'</a>';
    echo '<h1>'.JText::_('ERROR').'</h1>';
  
    echo '<div class="error">'.$this->error.'</div>';
    echo '<div class="center" style="margin-top:30px;">
            <a href="javascript:parent.history.go(-1);" class="bigButton" >&lt; '.JText::_('BACK').'</a>
          </div>';
  echo '</div>';
  
?>

<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1 class="error">'.$this->title.'</h1>';
  echo '<div class="error">'.$this->text.'</div>';
  echo '<div class="center" style="margin-top:20px;">';
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:reloadParent()" class="button">'.JText::_('OK').'</a>';
  }
  echo '</div>';
  echo '</div>';  
?>
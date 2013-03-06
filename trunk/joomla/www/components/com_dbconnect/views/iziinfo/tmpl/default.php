<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="#" onclick="parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1 >'.$this->title.'</h1>';
  echo '<div >'.$this->text.'</div>';
  echo '<div style="margin-top:20px;">';
  
  if ($this->link){
    echo '<a href="'.$this->link.'" class="button">'.JText::_('OK').'</a>';
  }elseif (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="#" onclick="reloadParent()" class="button">'.JText::_('OK').'</a>';
  }
  echo '</div>';
  echo '</div>';  
?>
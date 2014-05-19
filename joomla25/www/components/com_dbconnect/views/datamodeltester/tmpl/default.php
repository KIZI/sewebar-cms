<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  if (JRequest::getVar('close',@$this->close)!='no'){
    echo '<a href="javascript:parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>Test classification model</h1>';

  echo '<div class="font-style:italic;">In following code, variable blocks <strong>%RULEID%</strong> should be replaced with IDs (while saving into drl rules base)</div>';
  echo '<div>';

  //--vypsani jednotlivych pravidel
  echo '</div>';
  echo '</div>';  
?>
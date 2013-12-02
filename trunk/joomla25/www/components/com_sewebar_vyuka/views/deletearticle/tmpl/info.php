<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DELETE_ARTICLE').'</h1>';
  
  echo '<p>';
  if (@$this->confirm=="delete"){
    echo JText::_('ARTICLE_DELETED');
  }elseif(@$this->confirm=="storno"){
    echo JText::_('ARTICLE_DELETING_FORBIDDEN');
  }
  echo '</p>';
  echo '<a href="javascript:parent.closeSqueezeBox();" class="button">'.JText::_('CLOSE').'</a>';
?>
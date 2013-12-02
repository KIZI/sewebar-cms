<?php 
                                              
defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('NEW_ARTICLE').'</h1>';
  
  echo '<p>';
  if (@$this->confirm=="created"){
    echo JText::_('ARTICLE_CREATED');
  }elseif(@$this->confirm=="storno"){
    echo JText::_('ARTICLE_CREATING_ERROR');
  }
  echo '</p>';
  echo '<a href="javascript:parent.closeSqueezeBox();" class="button">'.JText::_('CLOSE').'</a>';
?>
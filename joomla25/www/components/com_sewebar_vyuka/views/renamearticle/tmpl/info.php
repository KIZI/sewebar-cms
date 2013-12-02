<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('RENAME_ARTICLE').'</h1>';
  
  echo '<p>';
  if (@$this->confirm=="rename"){
    echo JText::_('ARTICLE_RENAMED').' <strong>'.$this->article->title.'</strong>';
  }elseif(@$this->confirm=="storno"){
    echo JText::_('ARTICLE_RENAME_FORBIDDEN');
  }
  echo '</p>';
  echo '<a href="javascript:parent.closeSqueezeBox();" class="button">'.JText::_('CLOSE').'</a>';
?>
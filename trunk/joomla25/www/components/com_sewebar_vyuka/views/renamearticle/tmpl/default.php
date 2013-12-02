<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('RENAME_ARTICLE').'</h1>';
  
  echo '<p>'.JText::_("RENAME_ARTICLE").' <strong>'.$this->article->title.'</strong> '.JText::_('TO_TITLE').':</p>';
  echo '<form method="post">
          <input type="hidden" name="id" value="'.$this->article->id.'" />
          <input type="hidden" name="confirm" value="rename" />
          <input type="text" name="title" value="'.str_replace('"',"'",$this->article->title).'" style="width:300px;"/>&nbsp;&nbsp;
          <input type="submit" value="'.JText::_('RENAME').'" class="button" />&nbsp;<a href="javascript:parent.closeSqueezeBox();" class="button">'.JText::_('STORNO').'</a>
        </form>';
?>
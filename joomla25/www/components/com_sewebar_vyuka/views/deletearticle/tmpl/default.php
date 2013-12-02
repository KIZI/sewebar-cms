<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DELETE_ARTICLE').'</h1>';
  
  echo '<p>'.JText::_("DELETE_ARTICLE_ASK").' <strong>'.$this->article->title.'</strong>?</p>';
  echo '<form method="post">
          <input type="hidden" name="id" value="'.$this->article->id.'" />
          <input type="hidden" name="confirm" value="delete" />
          <input type="submit" value="'.JText::_('DELETE').'" class="button" />&nbsp;<a href="javascript:parent.closeSqueezeBox();" class="button">'.JText::_('STORNO').'</a>
        </form>';
?>
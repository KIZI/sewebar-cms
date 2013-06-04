<?php 
                             
defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('NEW_ARTICLE').'</h1>';
                       
  echo '<form method="post">
          <input type="hidden" name="catId" value="'.@$this->categoryId.'" />
          '.JText::_('TITLE').'&nbsp;&nbsp;
          <input type="text" name="title" value="" /><input type="submit" value="'.JText::_('CREATE_ARTICLE').'" class="button" />
        </form>';     
?>
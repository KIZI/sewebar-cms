<?php 
                             
defined('_JEXEC') or die('Restricted access');

echo '<div id="iziDiv">';
  echo '<h1>'.JText::_('NEW_ARTICLE').'</h1>';
  
  if (isset($this->error)){
    echo '<div class="errorDiv">'.$this->error.'</div>';
  }
                       
  echo '<form method="post">
          <input type="hidden" name="catId" value="'.@$this->categoryId.'" />
          <input type="hidden" name="kbi" value="'.@$this->kbiId.'" />
          <input type="hidden" name="todo" value="newReportArticle" />
          <label for="newArticleTitle">'.JText::_('TITLE').'</label>&nbsp;&nbsp;
          <input type="text" name="title" id="newArticleTitle" value="" />
          <input type="submit" value="'.JText::_('CREATE_ARTICLE').'" class="button" />
        </form>';    
echo '</div>';         
?>
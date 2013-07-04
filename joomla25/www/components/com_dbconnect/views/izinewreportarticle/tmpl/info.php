<?php 
                             
defined('_JEXEC') or die('Restricted access');

echo '<div id="iziDiv">';
  echo '<h1>'.JText::_('NEW_ARTICLE').'</h1>';
  
  if ($this->result=='ok'){
    echo '<div class="infoDiv">'.JText::_('NEW_REPORT_ARTICLE_CREATED').'</div>';
  }else{
    echo '<div class="errorDiv">'.JText::_('NEW_REPORT_ARTICLE_NOT_CREATED').'</div>';
  }
  echo '<div class="actionsDiv">';
  if (($this->result=='ok')&&(isset($this->editUrl))){
    echo '<a href="'.$this->editUrl.'" class="button" target="_blank">'.JText::_('EDIT_ARTICLE').'</a>';
  }
  echo '  <a class="button" href="javascript:parent.reloadReports();parent.close();">OK</a>
        </div>';
echo '</div>';              
?>
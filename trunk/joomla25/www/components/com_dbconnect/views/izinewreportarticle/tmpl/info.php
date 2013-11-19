<?php 
                             
defined('_JEXEC') or die('Restricted access');

echo '<div id="iziDiv">';
  echo '<h1>'.JText::_('NEW_ARTICLE').'</h1>';

  echo '<div class="center">';
  if ($this->result=='ok'){
    echo '<div class="infoDiv">'.JText::_('NEW_REPORT_ARTICLE_CREATED').'</div>';
  }else{
    echo '<div class="errorDiv">'.JText::_('NEW_REPORT_ARTICLE_NOT_CREATED').'</div>';
  }
  echo '</div>';
  echo '<div class="center" style="margin-top:20px;">';
  if (($this->result=='ok')&&(isset($this->editUrl))){
    echo '<a href="'.$this->editUrl.'" class="bigButton" target="_blank">'.JText::_('EDIT_ARTICLE').'</a>';
  }
  echo '  <a class="bigButton" href="javascript:parent.reloadReports();parent.close();">OK</a>
        </div>';
echo '</div>';              
?>
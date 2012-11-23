<?php 



defined('_JEXEC') or die('Restricted access');
  
  echo '<h1>'.JText::_('QUICK_TASK_GENERATED_H1').'</h1>';
  echo '<p>'.JText::_('QUICK_TASK_GENERATED_INFO').'</p>';
  
  
  if (@$this->redirectUrl!=''){
    echo '<script type="text/javascript">
            function redirectToUrl(){
              location.href="'.$this->redirectUrl.'";
            }
            var t=setTimeout("redirectToUrl();",5000);
          </script>';      
    echo '<p>'.JText::_('QUICK_TASK_GENERATED_REDIRECT_INFO').'
            <div class="spinner"></div>
          </p>';      
    echo '<a href="'.$this->redirectUrl.'" class="button">'.JText::_('GENERATE_KBI_SOURCE').'</a>';
  }else{
    echo '<table class="myPlainTable">';
    echo '<tr>
            <td>'.JText::_('BKEF_ARTICLE').': </td>
            <td><strong>'.$this->bkefArticleTitle.'</strong></td>
            <td class="actionsTd"><a href="'.JRoute::_('index.php?option=com_bkef&task=selArticle&article='.$this->task->bkef_article).'">'.JText::_('EDIT_BKEF').'...</a></td>
          </tr>';
    echo '<tr>
            <td>'.JText::_('FML_ARTICLE').': </td>
            <td><strong>'.$this->fmlArticleTitle.'</strong></td>
            <td class="actionsTd"></td>
          </tr>';      
    echo '</table>';
    echo '<div class="actionsDiv" style="margin-top:30px;">
            <a href="'.JRoute::_('index.php?option=com_dbconnect&task=listDMTasks').'">'.JText::_('SHOW_DM_TASKS_LIST').'...</a>
          </div>';
  } 
  
  
  
?>
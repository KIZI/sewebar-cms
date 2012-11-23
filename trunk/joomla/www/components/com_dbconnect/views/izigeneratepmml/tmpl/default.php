<?php 



defined('_JEXEC') or die('Restricted access');
  
  echo '<h1>'.JText::_('TASK_PREPARED').'</h1>';
  echo '<p>'.JText::_('TASK_GENERATED_INFO').'
          <div class="spinner"></div>
        </p>';
  echo '<script type="text/javascript">
          function redirectToUrl(){
            parent.location.href="'.$this->redirectUrl.'";
          }
          var t=setTimeout("redirectToUrl();",5000);
        </script>';
        
  echo '<a href="'.$this->redirectUrl.'" target="_parent" class="button">'.JText::_('REDIRECT').'</a>';
  
  
?>
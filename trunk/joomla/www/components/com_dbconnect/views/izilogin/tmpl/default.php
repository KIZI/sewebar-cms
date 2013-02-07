<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  ?>
  <script type="text/javascript">
  //<![CDATA[

    function reloadParent(){
      parent.close();
      ///parent.reload();
    }
  //]]>
  </script>
  
  <?php
  if (JRequest::getVar('back','')=='ok'){
    $backLink=true;
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component').'" class="backButton">'.JText::_('BACK').'</a>';
  }else{
    echo '<a href="#" onclick="reloadParent()" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>'.JText::_('LOGIN_USER').'</h1>';
  if (@$this->errorMessage!=''){
    echo '<div class="error">'.$this->errorMessage.'</div>';
  }
  echo '<form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=login&tmpl=component'.($backLink?'&back=ok':'')).'">
          <table id="loginTable">
            <tr>
              <td><label for="username">'.JText::_('USERNAME').'</label></td>
              <td><input id="username" type="text" name="username" value="'.$this->username.'" /></td>              
            </tr>
            <tr>  
              <td><label for="password">'.JText::_('PASSWORD').'</label></td>
              <td><input id="password" type="password" name="password" value="" /></td>
            </tr>
            <tr>
              <td></td>
              <td class="center">
                <input type="submit" value="'.JText::_('LOGIN').'" class="button" />';
                
  if ($backLink){
    echo ' <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component').'" class="cancelButton">'.JText::_('CANCEL').'</a>';
  }else{         
    echo ' <a href="#" onclick="reloadParent()" class="cancelButton">'.JText::_('CANCEL').'</a>';
  }              
  echo '      </td>
            </tr>
          </table>
        </form>';
  echo '<div class="noAccountDiv">'.JText::_('DONT_HAVE_ACCOUNT').' <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=register&tmpl=component'.($backLink?'&back=ok':'')).'">'.JText::_('REGISTER_NEW_ACCOUNT').'...</a></div>';      
        
  echo '</div>';
?>
<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  
  if (JRequest::getVar('back','')=='ok'){
    $backLink=true;
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=login&tmpl=component&back=ok').'" class="backButton">'.JText::_('BACK').'</a>';
  }else{
    echo '<a href="#" onclick="parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  }
                         
  echo '<h1>'.JText::_('REGISTER_USER').'</h1>';
  echo '<form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=register&tmpl=component'.($backLink?'&back=ok':'')).'">
          <input type="hidden" name="sent" value="ok" />
          <table id="loginTable">';
  if (@$this->errorMessage!=''){
    echo '  <tr><td colspan="2"><div class="error">'.$this->errorMessage.'</div></td></tr>';
  }        
  echo '    <tr>
              <td><label for="name">'.JText::_('NAME').'</label></td>
              <td><input id="name" type="text" name="name" value="'.$this->name.'" /></td>              
            </tr>
            <tr>
              <td><label for="username">'.JText::_('USERNAME').'</label></td>
              <td><input id="username" type="text" name="username" value="'.$this->username.'" /></td>              
            </tr>
            <tr>
              <td><label for="email">'.JText::_('EMAIL').'</label></td>
              <td><input id="email" type="text" name="email" value="'.$this->email.'" /></td>              
            </tr>
            <tr>  
              <td><label for="password">'.JText::_('PASSWORD').'</label></td>
              <td><input id="password" type="password" name="password" value="" /></td>
            </tr>
            <tr>  
              <td><label for="password2">'.JText::_('RE_PASSWORD').'</label></td>
              <td><input id="password2" type="password" name="password2" value="" /></td>
            </tr>
            <tr>
              <td></td>
              <td class="center">
                <input type="submit" value="'.JText::_('REGISTER').'" class="button" />';
                
  if ($backLink){
    echo ' <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=login&tmpl=component&back=ok').'" class="cancelButton">'.JText::_('CANCEL').'</a>';
  }else{         
    echo ' <a href="#" onclick="reloadParent()" class="cancelButton">'.JText::_('CANCEL').'</a>';
  }              
  echo '      </td>
            </tr>
          </table>
        </form>';      
        
  echo '</div>';
?>
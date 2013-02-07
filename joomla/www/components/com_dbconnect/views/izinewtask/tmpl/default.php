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
  if (JRequest::getVar('close','')!='no'){
    echo '<a href="#" onclick="reloadParent()" class="backButton">'.JText::_('CLOSE').'</a>';
  }
  
  echo '<h1>'.JText::_('NEW_TASK').'</h1>';
  
  echo '<div id="newTaskUserDiv">';
  if ($this->user->id>0){
    //máme přihlášeného uživatele
    echo '<h2>';
    if ($this->user->name!=$this->user->username){
      echo $this->user->name.' ('.$this->user->username.')';
    }else{
      echo $this->user->name;
    }
    echo '</h2>';
    echo '<p>'.JText::_('NEW_TASK_USER_INFO');
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=logout').'" class="button">'.JText::_('LOGOUT').'</a>';
    echo '</p>';
  }else{
    //jde o anonyma
    echo '<h2>'.JText::_('ANONYMOUS_USER').'</h2>';
    echo '<p>'.JText::_('NEW_TASK_ANONYMOUS_USER_INFO');
    echo ' <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=login').'" class="button">'.JText::_('LOGIN_USER').'</a>';
    echo '</p>';
  }
  echo '</div>';
  
  echo '<div class="bigButtonsDiv">
          <h2>'.JText::_('EXISTING_TASKS').'</h2>';
  if ($this->user->id>0){
    echo '<a href="">'.JText::_('SHOW_EXISTING_TASKS').'</a>';
  }else{
    echo '<p>'.JText::_('ANONYMOUS_USER_TASKS_INFO').'</p>';
  }        
  echo '  <h2>'.JText::_('NEW_DATASOURCE').'</h2>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadDemoCSV&tmpl=component').'">Upload DEMO file</a>
          <!--<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listDMTasks&tmpl=component').'">'.JText::_('EXISTING_TASKS').'</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listConnections&tmpl=component').'">'.JText::_('NEW_TASK_USING_EXISTING_DATA').'</a>-->
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadCSV&tmpl=component').'">'.JText::_('UPLOAD_CSV_FILE').'</a>
        </div>';
        
  echo '</div>';
?>
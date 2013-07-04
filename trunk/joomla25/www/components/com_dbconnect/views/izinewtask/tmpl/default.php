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
    
    function showExistingTasksList(){
      $('tasksList').style.display='block';
      $('showTasksA').style.display='none';
    }
  //]]>
  </script>
  <style type="text/css">
  /* <![CDATA[ */
    div#tasksList{
      display: none;
    }
  /* ]]> */
  </style>
  
  <?php
  if (JRequest::getVar('close','')!='no'){
    echo '<a href="javascript:reloadParent()" class="backButton">'.JText::_('CLOSE').'</a>';
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
    echo ' <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=logout&tmpl=component').'" class="button">'.JText::_('LOGOUT').'</a>';
    echo '</p>';
  }else{
    //jde o anonyma
    echo '<h2>'.JText::_('ANONYMOUS_USER').'</h2>';
    echo '<p>'.JText::_('NEW_TASK_ANONYMOUS_USER_INFO');
    echo ' <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=user&task=login&tmpl=component').'" class="button">'.JText::_('LOGIN_USER').'</a>';
    echo '</p>';
  }
  echo '</div>';
  
  echo '<div class="bigButtonsDiv">
          <h2>'.JText::_('EXISTING_TASKS').'</h2>';
  if ($this->user->id>0){
    echo '<a href="javascript:showExistingTasksList();" id="showTasksA" >'.JText::_('SHOW_EXISTING_TASKS').'</a>';
    echo '<div id="tasksList">';
    if ($this->tasks&&(count($this->tasks)>0)){
      foreach($this->tasks as $task){
        echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=showTask&tmpl=component&task_id='.$task->id).'">'.htmlspecialchars($task->name).'</a>';
      }
    }else{
      echo JText::_('NO_DM_TASKS_FOUND_INFO');
    }
    echo '</div>';
    /*<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listDMTasks&tmpl=component').'">'.JText::_('EXISTING_TASKS').'</a>*/
  }else{
    echo '<p>'.JText::_('ANONYMOUS_USER_TASKS_INFO').'</p>';
  }        
  echo '  <h2>'.JText::_('NEW_DATASOURCE').'</h2>';
  if ($this->user->id>0){
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listConnections&tmpl=component').'">'.JText::_('NEW_TASK_USING_EXISTING_DATA').'</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadDemoCSV&tmpl=component').'">Upload DEMO file</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadCSV&tmpl=component').'">'.JText::_('UPLOAD_CSV_FILE').'</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listConnections&tmpl=component').'">'.JText::_('USE_MYSQL_DATABASE').'</a>';
  }else{
    echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadDemoCSV&tmpl=component').'">Upload DEMO file</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadCSV&tmpl=component').'">'.JText::_('UPLOAD_CSV_FILE').'</a>';
  }
  echo '  
        </div>';
        
  echo '</div>';
?>
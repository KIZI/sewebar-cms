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
  echo '<a href="#" onclick="reloadParent()" class="backButton">'.JText::_('CLOSE').'</a>';
  
  echo '<h1>'.JText::_('NEW_DATASOURCE').'</h1>';
  
  echo '<div class="bigButtonsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadDemoCSV&tmpl=component').'">Upload DEMO file</a>
          <!--<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listDMTasks&tmpl=component').'">'.JText::_('EXISTING_TASKS').'</a>
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=listConnections&tmpl=component').'">'.JText::_('NEW_TASK_USING_EXISTING_DATA').'</a>-->
          <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadCSV&tmpl=component').'">'.JText::_('UPLOAD_CSV_FILE').'</a>
        </div>';
        
  echo '</div>';
?>
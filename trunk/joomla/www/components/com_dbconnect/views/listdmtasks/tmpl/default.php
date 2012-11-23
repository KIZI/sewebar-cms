<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DB_DM_TASKS').'</h1>';
  $headerLink='index.php?option=com_dbconnect&task=listDMTasks&order=';
  echo '<div class="actionsDiv">
          <a href="'.JRoute::_('index.php?option=com_dbconnect&task=selectDBConnection').'">'.JText::_('NEW_DM_TASK').'...</a>
        </div>';
  if ($this->tasks&&(count($this->tasks)>0)){
    echo '<table class="myAdminTable looser">';
    echo '<tr>
            <th><a href="'.JRoute::_($headerLink.'name').'">'.JText::_('DM_TASK_NAME').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'fml_article').'">'.JText::_('MAPPING').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'created').'">'.JText::_('CREATED_MODIFIED').'</a></th>
            <th colspan="2">'.JText::_('ACTIONS').'</th>
          </tr>';
    $rowClass=0;      
    foreach ($this->tasks as $task) {                      
    	echo '<tr class="row'.$rowClass.'">
              <td><strong>'.$task->name.'</strong></td>
              <td>'.(($task->fml_article>0)?(JText::_('SET_UP_SHORT')):'-').'</td>
              <td>'.date(JText::_('DATETIME_FORMAT'),strtotime($task->created)).'</td>
              <td class="actionsTd">';
                /*vyreseni jednotlivych tlacitek*/
                if ($task->kbi_source>0){
                  echo '<a href="" onclick="return false;" class="disabled">'.JText::_('EDIT_DM_TASK').'</a> ';
                  echo '<a href="" onclick="return false;" class="disabled">'.JText::_('SETUP_MAPPING').'</a> ';
                }else{
                  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&task=editDMTask&task_id='.$task->id).'">'.JText::_('EDIT_DM_TASK').'</a> ';
                  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&task=mapping&task_id='.$task->id).'">'.JText::_('SETUP_MAPPING').'</a> ';
                }
                if (($task->fml_article>0)&&($task->kbi_source<=0)){
                  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&task=preprocessing&task_id='.$task->id).'">'.JText::_('SETUP_PREPROCESSING').'</a> ';
                }else{
                  echo '<a href="" onclick="return false;" class="disabled">'.JText::_('SETUP_PREPROCESSING').'</a> ';
                }
                if ($task->kbi_source>0){
                  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&task=gotoARDesigner&task_id='.$task->id).'">'.JText::_('GOTO_IZI_MINER').'</a> ';
                }else{
                  echo '<a href="" onclick="return false;" class="disabled">'.JText::_('GOTO_IZI_MINER').'</a> ';
                }
                /*--vyreseni jednotlivych tlacitek*/ 
                       
        echo '</td>
              <td class="actionsTd">      
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=cloneDMTask&tmpl=component&task_id='.$task->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 300}}">'.JText::_('CLONE_TASK').'</a>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=showConnectionInfo&tmpl=component&connection_id='.$task->db_table).'" class="modal" rel="{handler: \'iframe\', size: {x: 300, y: 200}}">'.JText::_('DB_CONNECTION').'</a>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=showTable&tmpl=component&connection_id='.$task->db_table).'" class="modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('SHOW_DB_TABLE_PREVIEW').'</a>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&task=deleteDMTask&tmpl=component&task_id='.$task->id).'" class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 200}}">'.JText::_('DELETE').'</a>
              </td>
            </tr>';
      $rowClass=($rowClass+1)%2;      
    }      
    echo '</table>';
  }else{
    echo '<div>'.JText::_('NO_DM_TASKS_FOUND_INFO').'</div>';
  }
  
?>
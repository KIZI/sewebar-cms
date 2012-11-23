<?php 

defined('_JEXEC') or die('Restricted access');

  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newDatasource').'" class="backButton">'.JText::_('BACK').'</a>';

  echo '<h1>'.JText::_('DB_DM_TASKS').'</h1>';
  $headerLink='index.php?option=com_dbconnect&controller=izi&task=IziListDMTasks&order=';

  if ($this->tasks&&(count($this->tasks)>0)){
    echo '<table class="myAdminTable looser">';
    echo '<tr>
            <th><a href="'.JRoute::_($headerLink.'name').'">'.JText::_('DM_TASK_NAME').'</a></th>
            <th><a href="'.JRoute::_($headerLink.'created').'">'.JText::_('CREATED_MODIFIED').'</a></th>
            <th colspan="2">'.JText::_('ACTIONS').'</th>
          </tr>';
    $rowClass=0;      
    foreach ($this->tasks as $task) {                      
    	echo '<tr class="row'.$rowClass.'">
              <td><strong>'.$task->name.'</strong></td>
              <td>'.date(JText::_('DATETIME_FORMAT'),strtotime($task->created)).'</td>
              <td class="actionsTd">';
                /*vyreseni jednotlivych tlacitek*/
                if ($task->kbi_source>0){
                  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&task=gotoARDesigner&task_id='.$task->id).'">'.JText::_('GOTO_IZI_MINER').'</a> ';
                  echo '<a href="" onclick="return false;" class="disabled">'.JText::_('EDIT_DM_TASK').'</a> ';
                }else{
                  echo '<a href="" onclick="return false;" class="disabled">'.JText::_('GOTO_IZI_MINER').'</a> ';
                  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&task=editDMTask&tmpl=component&controller=izi&task_id='.$task->id).'">'.JText::_('EDIT_DM_TASK').'</a> ';
                }
        //TODO dodělání jednotlivých akcí - zobrazení náhledu tabulky                       
        echo '  <a href="'.JRoute::_('index.php?option=com_dbconnect&task=cloneDMTask&controller=izi&tmpl=component&task_id='.$task->id).'" >'.JText::_('CLONE_DM_TASK').'</a>';
                /*--vyreseni jednotlivych tlacitek*/ 
        echo '</td>
            </tr>';
      $rowClass=($rowClass+1)%2;      
    }      
    echo '</table>';
  }else{
    echo '<div>'.JText::_('NO_DM_TASKS_FOUND_INFO').'</div>';
  }
  
?>
<?php 
defined('_JEXEC') or die('Restricted access');
echo '<div id="iziDiv">';
  
  echo '<h1>'.JText::_('NEW_QUICK_DM_TASK_H1').'</h1>';
  echo '<div class="infoDiv">'.JText::_('NEW_IZI_QUICK_DM_TASK_INFO').'</div>';
  if ($this->columns&&(count($this->columns)>0)){
    //máme nějaké info o sloupcích - zobrazíme jejich výběr 
    echo '<div id="quickdmtaskDiv">';
    echo '  <form method="post">';
    echo '    <input type="hidden" value="'.$this->connection->id.'" name="connection_id" />';
    echo '    <input type="hidden" value="'.$this->dmTask->id.'" name="task_id" />';
    echo '    <input type="hidden" value="ok" name="save" />';
    echo '    <input type="hidden" value="'.@$this->dmTask->id.'" name="task_id" />';
    echo '    <table>
                  <tr>
                    <td style="padding-right:20px;"><label for="name">'.JText::_('TASK_NAME').'</label></td>
                    <td>
                      <input type="text" name="name" id="name" value="'.$this->connection->table.' ('.date(JText::_('DATETIME_FORMAT')).')" maxlength="60" style="min-width:250px;"  />
                    </td>
                  </tr>
                </table>';
    echo '    <h2>'.JText::_('SELECT_DM_TASK_COLUMNS_FOR_AUTOPREPROCESSING').'</h2>';
    echo '    <div class="selectorsDiv">
                <a href="javascript:selectAllColumns();">'.JText::_('SELECT_ALL').'</a>&nbsp;|&nbsp;<a href="javascript:selectNoneColumns();">'.JText::_('SELECT_NONE').'</a>
              </div>';
    echo '    <table class="myAdminTable">';
    echo '      <tr>
                  <th>'.JText::_('USE').'</th>
                  <th>'.JText::_('COLUMN_NAME').'</th>
                  <th>'.JText::_('DATATYPE').'</th>
                  <th>'.JText::_('DM_DATATYPE').'</th>
                </tr>';                               //TODO lokalizace
                $rowClass=0;        
                $primaryKeyName=$this->connection->primary_key;
                foreach ($this->columns as $column) {
                  $disabled=($primaryKeyName==$column['Field']);
                    
                	$columnData=@$this->columnsData[$column['Field']];  
                  echo '<tr class="row'.$rowClass.'">';
                	echo '<td>
                          <input type="checkbox" name="'.$column['Field'].'_useColDM"  id="'.$column['Field'].'_use" '.($disabled?'disabled="disabled"':'').' value="1" onchange="iziShowItemText(this);" class="columnCheckbox" '.((@$columnData['use']||@$_POST[$column['Field'].'_use'])?'checked="checked"':'').' />
                          <input type="hidden" name="'.$column['Field'].'_useColDM_colName" value="'.$column['Field'].'" />
                        </td>';
                	echo '<td '.($disabled?'class="disabled"':'').'><label for="'.$column['Field'].'_use"><strong>'.$column['Field'].'</strong></label></td>';
                	echo '<td '.($disabled?'class="disabled"':'').'>'.$column['Type'].'</td>';
                	if ($columnData['type']!=""){
                    $columnType=$columnData['type'];
                  }else{
                    $columnType=$this->unidbModel->getGenericDataType($column['Type']);
                  }                                                                  
                	echo '<td>
                          <select name="'.$column['Field'].'_type" id="'.$column['Field'].'_type">
                            <option value="string" '.($columnType=="string"?' selected="selected"':'').'>String</option>
                            <option value="integer" '.($columnType=="integer"?' selected="selected"':'').'>Integer</option>
                            <option value="float" '.($columnType=="float"?' selected="selected"':'').'>Float</option>
                            <option value="boolean" '.($columnType=="boolean"?' selected="selected"':'').'>Boolean</option>
                          </select>
                        </td>';
                	echo '</tr>';
                  $rowClass=($rowClass+1)%2;
                }       
    echo '    </table>
              <input type="hidden" name="generatePreprocessing" value="ok" />
              <input type="hidden" name="from" value="izi" />'; 
    echo '    <div class="center"><input type="submit" value="'.JText::_('SAVE_TASK').'" class="bigButton" /></div>';
    echo '  </form>
          </div>';
  }
  
echo '</div>';

?>
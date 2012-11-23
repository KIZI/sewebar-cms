<?php 

  defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('NEW_DABATASE_CONNECTION').'</h1>';
  echo '<h2>'.JText::_('DATABASE_SETTINGS').'</h2>';
  echo '<form method="post" id="select_primary_key_form">
          <table class="myPlainTable">
            <tr>
              <td>'.JText::_('DB_TYPE').'</td>
              <td><input type="text" name="db_type" value="'.JRequest::getVar('db_type','').'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td>'.JText::_('DB_SERVER').'</td>
              <td><input type="text" name="db_server" value="'.JRequest::getVar('db_server','').'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td>'.JText::_('USERNAME').'</td>
              <td><input type="text" name="db_username" value="'.JRequest::getVar('db_username','').'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td>'.JText::_('PASSWORD').'</td>
              <td><input type="password" name="db_password" value="'.JRequest::getVar('db_password','').'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td>'.JText::_('DATABASE_NAME').'</td>
              <td><input type="text" name="db_database" value="'.JRequest::getVar('db_database','').'" readonly="readonly" /></td>
            </tr>
            <tr>
              <td>'.JText::_('TABLE_NAME').'</td>
              <td><input type="text" name="db_table" value="'.JRequest::getVar('db_table','').'" readonly="readonly" /> - <a href="'.JRoute::_('index.php?option=com_dbconnect&task=showTable&db_type='.JRequest::getVar('db_type').'&db_server='.JRequest::getVar('db_server').'&db_username='.JRequest::getVar('db_username').'&db_password='.urlencode(base64_encode(JRequest::getVar('db_password'))).'&db_table='.JRequest::getVar('db_table').'&db_database='.JRequest::getVar('db_database')).'" class="modal">'.JText::_('SHOW_DB_TABLE_PREVIEW').'</a></td>
            </tr>
            <tr>
              <td>'.JText::_('SHARED_CONNECTION').'</td>
              <td><input type="checkbox" name="db_shared_connection" value="1" readonly="readonly" disabled="disabled" '.(JRequest::getVar('db_shared_connection','')==1?'checked="checked"':'').' /></td>
            </tr>
          </table>';
  echo '  <h2>'.JText::_('SELECT_TABLE_PRIMARY_KEY').'</h2>';  
  if (($this->columns)&&count($this->columns>0)){
    echo '<table class="myAdminTable">';
    echo '<tr><th></th><th>'.JText::_('COLUMN_NAME').'</th><th>'.JText::_('DATATYPE').'</th><th>'.JText::_('KEY').'</th><th>'.JText::_('ACTIONS').'</th></tr>';
    foreach ($this->columns as $column){       
      echo '<tr>
              <td><input type="radio" name="db_primary_key" value="'.$column['Field'].'" id="db_primary_key_'.str_replace(' ','',$column['Field']).'" class="primaryKeyRadio" /></td>
              <td><strong>'.$column['Field'].'</strong></td>
              <td>'.$column['Type'].'</td>
              <td>'.$column['Key'].'</td>
              <td class="actionsTd">
                <a href="javascript:selectPrimaryKey(\''.str_replace(' ','',$column['Field']).'\');">'.JText::_('SELECT').'</a>
              </td>
            </tr>';	
    }
    echo '</table>'; 
    echo '<div class="actionsDiv"><input type="submit" value="'.JText::_('CONTINUE').'" class="button disabled" id="dbSubmitButton" disabled="disabled" /></div>';
  }else{
    echo '<div class="error">'.JText::_('ERROR_COLUMNS_INFO_INFO').'</div>';
    echo '<div class="actionsDiv"><a href="parent.history.go(-1);">&lt;&nbsp;'.JText::_('BACK').'</a></div>'; 
  }
  echo '<input type="hidden" name="quickDMTask" value="'.@$this->quickDMTask.'">';
  echo '<input type="hidden" name="step" value="3" />';
  echo '<input type="hidden" name="save" value="connection" />';
  echo '</form>';
  

?> 


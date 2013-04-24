<?php 

  defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('NEW_DABATASE_CONNECTION').'</h1>';

  echo '<form method="post">
        <table class="myPlainTable">
          <tr>
            <td>'.JText::_('DB_TYPE').'</td>
            <td>
              <select name="db_type">';
              foreach ($this->dbTypes as $key=>$name) {
              	echo '<option value="'.$key.'">'.$name.'</option>';
              }
  echo '      </select></td>
          </tr>
          <tr>
            <td>'.JText::_('DB_SERVER').'</td>
            <td><input type="text" name="db_server" value="'.JRequest::getVar('db_server','localhost').'" /></td>
          </tr>
          <tr>
            <td>'.JText::_('USERNAME').'</td>
            <td><input type="text" name="db_username" value="'.JRequest::getVar('db_username','').'" /></td>
          </tr>
          <tr>
            <td>'.JText::_('PASSWORD').'</td>
            <td><input type="password" name="db_password" value="'.JRequest::getVar('db_password','').'" /></td>
          </tr>
          <tr>
            <td>'.JText::_('DATABASE_NAME').'</td>
            <td><input type="text" name="db_database" value="'.JRequest::getVar('db_database','').'" /></td>
          </tr>
          <tr>
            <td>'.JText::_('SHARED_CONNECTION').'</td>
            <td><input type="checkbox" name="db_shared_connection" value="1" '.(JRequest::getVar('db_shared_connection','')==1?'checked="checked"':'').' /></td>
          </tr>
        </table>
        <input type="hidden" name="quickDMTask" value="'.@$this->quickDMTask.'">
        <input type="hidden" name="step" value="1" />
        <div class="actionsDiv">
          <input type="submit" value="'.JText::_('CONTINUE').'" class="button" />
        </div>
        </form>
        ';
  

?> 
<?php
 
//DEVNOTE: list all records and make url.  
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row = &$this->items[$i];	?>			
			<a href="http://<?php echo $row->url; ?>" title="<?php echo $row->title.' '.$row->desript_view?>">
					<?php echo $row->title; ?></a><BR />
<?php 
  }
?>		


<?php 
                             
defined('_JEXEC') or die('Restricted access');

  echo '<div id="iziDiv">';
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component').'" class="backButton">'.JText::_('< BACK').'</a>';
  
  echo '<h1>'.JText::_('UPLOAD_CSV_FILE').'</h1>';
  echo '<div id="iziuploadcsv2Div">
          <form method="post" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=uploadCSV_import').'" >
            <input type="hidden" name="file" value="'.$this->fileData->id.'" />
            <table style="width:300px;">
              <tr>
                <td colspan="2"><h2>'.JText::_('IMPORT_CONFIG').'</h2></td>
              </tr>
              <tr>
                <td style="padding-right:20px;">'.JText::_('UPLOADED_CSV_FILE').'</td>
                <td><strong>'.$this->fileData->filename.'</strong></td>
              </tr>
              <tr>
                <td><label for="table_name">'.JText::_('TABLE_NAME').'</label></td>
                <td><input type="text" id="table_name" name="table_name" value="'.$this->table_name.'" /></td>
              </tr>
              <tr>
                <td><label for="encoding">'.JText::_('ENCODING').'</label></td>
                <td>
                  <select name="encoding" onchange="uploadcsv2preview();" id="encoding">
                    <option value="utf8" selected="selected">UTF-8</option>
                    <option value="cp1250">WIN 1250</option>
                    <option value="iso-8859-1">ISO 8859-1</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td><label for="delimitier">'.JText::_('CSV_DELIMITIER').'</label></td>
                <td>
                  <select name="delimitier" id="delimitier" onchange="delimitierChange();uploadcsv2preview();">
                    <option value=";"'.(($this->delimitier==';')?' selected="selected"':'').'>'.JText::_('DELIMITIER_SEMICOLON').'</option>
                    <option value=","'.(($this->delimitier==',')?' selected="selected"':'').'>'.JText::_('DELIMITIER_COMMA').'</option>
                    <option value="\t"'.(($this->delimitier=='\t')?' selected="selected"':'').'>'.JText::_('DELIMITIER_TAB').'</option>
                    <option value="|"'.(($this->delimitier=='|')?' selected="selected"':'').'>'.JText::_('DELIMITIER_LINE').'</option>
                    <option value=""'.((!in_array($this->delimitier,array(';',',','|','\t')))?' selected="selected"':'').'>'.JText::_('DELIMITIER_OTHER').'</option>
                  </select>
                  <input type="text" onfocus="setTimer(this);" style="display:none;" onblur="clearTimer(this);" value="'.htmlspecialchars($this->delimitier).'" id="delimitier_text" name="delimitier_text" maxlength="3" />
                </td>
              </tr>
              <tr>
                <td><label for="enclosure">'.JText::_('CSV_ENCLOSURE').'</label></td>
                <td>
                  <input type="text" onkeydown="checkParamsChange(this);"  onchange="checkParamsChange(this);" value="'.htmlspecialchars($this->enclosure).'" id="enclosure" name="enclosure" maxlength="1" />
                </td>
              </tr>
              <tr>
                <td><label for="escape">'.JText::_('CSV_ESCAPE_CHARACTER').'</label></td>
                <td>
                  <input type="text" onkeydown="checkParamsChange(this);" onchange="checkParamsChange(this);" value="'.htmlspecialchars($this->escapeChar).'" id="escape" name="escape" maxlength="1" />
                </td>
              </tr>
              <tr>
                <td colspan="2"><h2>'.JText::_('IMPORT_STATS').'</h2></td>
              </tr>
              <tr>
                <td>'.JText::_('ROWS_COUNT').'</td>
                <td>
                  <strong id="rowsCount"></strong>
                </td>
              </tr>
              <tr>
                <td>'.JText::_('COLS_COUNT').'</td>
                <td>
                  <strong id="colsCount"></strong>
                </td>
              </tr>
              <tr id="colsCountWarning"><td colspan="2">'.JText::_('WARNING_TOO_MUCH_COLUMNS').'</td></tr>
            </table>
            <input type="submit" value="'.JText::_('IMPORT_DATA_INTO_DATABASE').'" class="bigButton" />
          </form>
          
          <a href="javascript:uploadcsv2preview()" id="reloadPreviewA">'.JText::_('RELOAD_PREVIEW').'</a>
        
          <div id="previewDiv"></div>
        </div>
        
        ';
   echo '</div>';   
?>
<?php 
                             
defined('_JEXEC') or die('Restricted access');

  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTester&tmpl=component&kbi='.$this->kbi.'&lmtask='.$this->lmtask.'&rules='.$this->rules).'" class="backButton">'.JText::_('BACK').'</a>';
  
  echo '<h1>'.JText::_('UPLOAD_CSV_FILE').'</h1>';
                       
  echo '<div id="wrapper">
        	<form method="post" class="uploadForm" action="'.JRoute::_('index.php?option=com_dbconnect&controller=data&task=modelTesterUploadCSV&tmpl=component').'" enctype="multipart/form-data">
        	  <input type="hidden" name="kbi" value="'.htmlspecialchars($this->kbi).'" />
        	  <input type="hidden" name="rules" value="'.htmlspecialchars($this->rules).'" />
        	  <input type="hidden" name="lmtask" value="'.htmlspecialchars($this->lmtask).'" />
        		<div class="formRow">
        			<label for="url" class="floated">'.JText::_('FILE').': </label>
        			<input type="file" id="url" name="url" /><br />
        		</div>
        
        		<div class="formRow">
        			<input type="submit" id="_submit" name="_submit" value="'.JText::_('UPLOAD_SELECTED_CSV_FILE').'" class="bigButton" />
        		</div>
          </form>
        </div>';
  
  echo '</div>';     
?>
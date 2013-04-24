<?php 
                             
defined('_JEXEC') or die('Restricted access');

  echo '<div id="iziDiv">';
  
  echo '<a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=newTask&tmpl=component').'" class="backButton">'.JText::_('< BACK').'</a>';
  
  echo '<h1>'.JText::_('UPLOAD_CSV_FILE').'</h1>';
                       
  echo '<div id="wrapper">
        	<form method="post" class="uploadForm" action="'.JRoute::_('index.php?controller=izi&task=uploadCSV&tmpl=component').'" enctype="multipart/form-data">        
        		<div class="formRow">
        			<label for="url" class="floated">File: </label>
        			<input type="file" id="url" name="url" /><br />
        		</div>
        
        		<div class="formRow">
        			<input type="submit" id="_submit" name="_submit" value="'.JText::_('UPLOAD_SELECTED_CSV_FILE').'" class="bigButton" />
        		</div>
          </form>
        </div>';
  
  echo '</div>';     
?>
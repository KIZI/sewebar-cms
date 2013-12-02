<?php 
                             
defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('UPLOAD_PMML_FILES').'</h1>';
                       
  echo '<div id="wrapper">
        	<form method="post" action="'.JRoute::_('index.php?task=uploadPmmlFiles2&tmpl=component&catid='.$this->categoryId).'" enctype="multipart/form-data">        
        		<div class="formRow">
        			<label for="url" class="floated">File: </label>
        			<input type="file" id="url" name="url[]" multiple="multiple" /><br />
        		</div>
        
        		<div class="formRow">
        			<input type="submit" id="_submit" name="_submit" value="'.JText::_('UPLOAD_FILES').'" />
        		</div>
          </form>
        </div>';     
?>
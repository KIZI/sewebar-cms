<?php
jimport( 'joomla.application.component.view' );
                       
/**
 * @package Joomla
 * @subpackage Config
 */
class dataViewDataModelTesterUploadCSV extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('UPLOAD_CSV_FILE') );
                                                                /*
    JHtml::stylesheet('upload.css','media/com_dbconnect/css/');
    
    JHtml::script('mootools-core.js','media/com_dbconnect/js/');
    JHtml::script('core.js','media/com_dbconnect/js/');
    JHtml::script('mootools-more.js','media/com_dbconnect/js/');
    JHtml::script('Request.File.js','media/com_dbconnect/js/');
    JHtml::script('Form.MultipleFileInput.js','media/com_dbconnect/js/');
    JHtml::script('Form.Upload.js','media/com_dbconnect/js/');
    
    $document->addScriptDeclaration("window.addEvent('domready', function(){
                                    	var upload = new Form.Upload('url', {
                                    		onComplete: function(){
                                    			alert('Completed uploading the Files');
                                          location.href='".JRoute::_('index.php?task=uploadPmmlFiles2&tmpl=component&catid='.$this->categoryId,false)."'
                                    		}
                                    	});   
                                    
                                    	if (!upload.isModern()){  
                                    		// Use something like
                                    	}
                                    
                                    });");    */
		
    parent::display();		
  }
}
?>

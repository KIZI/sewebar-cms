<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class articlesViewUploadPmmlFiles extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                                  
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('UPLOAD_PMML_FILES'));

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    JHtml::stylesheet('upload.css','media/com_sewebar_vyuka/css/');
    
    JHtml::script('mootools-core.js','',true);
    JHtml::script('Request.File.js','media/com_sewebar_vyuka/js/');
    JHtml::script('Form.MultipleFileInput.js','media/com_sewebar_vyuka/js/');
    JHtml::script('Form.Upload.js','media/com_sewebar_vyuka/js/');
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
                                    
                                    });");
                                        
      
    parent::display();		
  }
}
?>

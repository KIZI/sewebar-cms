<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziNewPreprocessing_EachOne extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_PREPROCESSING') );
    
    
    $document->addScriptDeclaration("var lang=".json_encode(array(
                                                              'INPUT_VALID_ATTRIBUTE_NAME'=>JText::_('INPUT_VALID_ATTRIBUTE_NAME')
                                                            )).";");
                                                            
    JHtml::script('eachone.js','media/com_dbconnect/js/');
    JHtml::script('uniquenames.js','media/com_dbconnect/js/');
    
    parent::display();		
  }
}
?>

<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziNewPreprocessing_Equidistant extends JView
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
                                                              'START_VALUE_IS_NOT_NUMBER'=>JText::_('START_VALUE_IS_NOT_NUMBER'),
                                                              'END_VALUE_IS_NOT_NUMBER'=>JText::_('END_VALUE_IS_NOT_NUMBER'),
                                                              'START_VALUE_BIGGER_THAN_END'=>JText::_('START_VALUE_BIGGER_THAN_END'),
                                                              'STEP_VALUE_IS_NOT_NUMBER'=>JText::_('STEP_VALUE_IS_NOT_NUMBER'),
                                                              'STEP_VALUE_IS_TOO_BIG'=>JText::_('STEP_VALUE_IS_TOO_BIG'),
                                                              'INPUT_VALID_ATTRIBUTE_NAME'=>JText::_('INPUT_VALID_ATTRIBUTE_NAME')
                                                            )).";");
    JHtml::script('equidistant.js','media/com_dbconnect/js/');   
    JHtml::script('uniquenames.js','media/com_dbconnect/js/');                                                     
    
    parent::display();		
  }
}
?>

<?php
jimport( 'joomla.application.component.view' );
                                 
class iziViewIziNewPreprocessing_IntervalEnumeration extends JView
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
                                                              'BLANK_BIN_NAME'=>JText::_('BLANK_BIN_NAME'),
                                                              'BIN_NAME_HAS_BEEN_USED_FOR_OTHER_BIN'=>JText::_('BIN_NAME_HAS_BEEN_USED_FOR_OTHER_BIN'),
                                                              'INTERVAL_TO_ADD'=>JText::_('INTERVAL_TO_ADD'),
                                                              'REALLY_DELETE_SELECTED_GROUP'=>JText::_('REALLY_DELETE_SELECTED_GROUP'),
                                                              'INTERVAL_LEFT_CLOSED'=>JText::_('INTERVAL_LEFT_CLOSED'),
                                                              'INTERVAL_LEFT_OPEN'=>JText::_('INTERVAL_LEFT_OPEN'),
                                                              'INTERVAL_RIGHT_CLOSED'=>JText::_('INTERVAL_RIGHT_CLOSED'),
                                                              'INTERVAL_RIGHT_OPEN'=>JText::_('INTERVAL_RIGHT_OPEN'),
                                                              'ADD_TO_GROUP'=>JText::_('ADD_TO_GROUP'),
                                                              'CANCEL'=>JText::_('CANCEL'),
                                                              'ADD_INTERVAL'=>JText::_('ADD_INTERVAL'),
                                                              'START_VALUE_IS_NOT_NUMBER'=>JText::_('START_VALUE_IS_NOT_NUMBER'),
                                                              'END_VALUE_IS_NOT_NUMBER'=>JText::_('END_VALUE_IS_NOT_NUMBER'),
                                                              'START_VALUE_BIGGER_THAN_END_OR_SAME'=>JText::_('START_VALUE_BIGGER_THAN_END_OR_SAME'),
                                                              'OVERLAP_WITH_INTERVALS'=>JText::_('OVERLAP_WITH_INTERVALS'),
                                                              'OVERLAP_WITH_INTERVAL'=>JText::_('OVERLAP_WITH_INTERVAL'),
                                                              'ADD_ITEM'=>JText::_('ADD_ITEM'),
                                                              'DELETE_GROUP'=>JText::_('DELETE_GROUP'),
                                                              'GROUP_NAME'=>JText::_('GROUP_NAME'),
                                                              'NOT_SUBMITTED_INTERVAL_WARNING'=>JText::_('NOT_SUBMITTED_INTERVAL_WARNING'),
                                                              'NO_GROUPED_INTERVALS_FOUND'=>JText::_('NO_GROUPED_INTERVALS_FOUND'),
                                                              'BLANK_INTERVAL_GROUPS_WARNING'=>JText::_('BLANK_INTERVAL_GROUPS_WARNING'),
                                                              'GROUP_NAME'=>JText::_('GROUP_NAME'),
                                                              'ADD_ITEM'=>JText::_('ADD_ITEM'),
                                                              'DELETE'=>JText::_('DELETE'),
                                                              'INPUT_VALID_ATTRIBUTE_NAME'=>JText::_('INPUT_VALID_ATTRIBUTE_NAME')  
                                                            )).";");
    JHtml::script('intervalenumeration.js','media/com_dbconnect/js/');
    JHtml::script('uniquenames.js','media/com_dbconnect/js/');
    
    parent::display();		
  }
}
?>

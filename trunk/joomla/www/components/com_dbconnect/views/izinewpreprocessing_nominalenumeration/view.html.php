<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziNewPreprocessing_NominalEnumeration extends JView
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
                                                              'DELETE_GROUP'=>JText::_('DELETE_GROUP'),
                                                              'GROUP_NAME'=>JText::_('GROUP_NAME'),
                                                              'DELETE'=>JText::_('DELETE'),
                                                              'NO_ITEMS_TO_ADD'=>JText::_('NO_ITEMS_TO_ADD'),
                                                              'VALUE_TO_ADD'=>JText::_('VALUE_TO_ADD'),
                                                              'ADD_TO_GROUP'=>JText::_('ADD_TO_GROUP'),
                                                              'CANCEL'=>JText::_('CANCEL'),
                                                              'REALLY_DELETE_SELECTED_GROUP'=>JText::_('REALLY_DELETE_SELECTED_GROUP'),
                                                              'ADD_ITEM'=>JText::_('ADD_ITEM'),
                                                              'DELETE'=>JText::_('DELETE'),
                                                              'NOT_SUBMITTED_VALUE_WARNING'=>JText::_('NOT_SUBMITTED_VALUE_WARNING'),
                                                              'NO_GROUPED_VALUES_FOUND'=>JText::_('NO_GROUPED_VALUES_FOUND'),
                                                              'BLANK_NOMINAL_GROUPS_WARNING'=>JText::_('BLANK_NOMINAL_GROUPS_WARNING')
                                                            )));
                                                            
    JHtml::script('nominalenumeration.js','media/com_dbconnect/js/');
    
    parent::display();		
  }
}
?>

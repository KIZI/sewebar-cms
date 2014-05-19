<?php
jimport( 'joomla.application.component.view' );
                         
                                
class dataViewModelTester extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TEST_MODEL') );
    
    parent::display();		
  }
}
?>

<?php
jimport( 'joomla.application.component.view' );


class dataViewDataModelTester extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    JHtml::stylesheet('datamodeltester.css','components/com_dbconnect/media/css/');
    JHtml::script('https://www.google.com/jsapi');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TEST_MODEL') );
    $document->addScript(JURI::base().'components/com_dbconnect/media/js/datamodeltester.js');

    parent::display();		
  }
}
?>

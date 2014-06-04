<?php
jimport( 'joomla.application.component.view' );


class dataViewBRBaseShow extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    JHtml::stylesheet('datamodeltester.css','components/com_dbconnect/media/css/');

		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TEST_MODEL') );

    parent::display();		
  }
}
?>

<?php
jimport( 'joomla.application.component.view' );
                                  
class dbconnectViewshowConnectionInfo extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DB_CONNECTION') );

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

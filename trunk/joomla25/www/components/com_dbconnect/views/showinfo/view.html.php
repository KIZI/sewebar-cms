<?php
jimport( 'joomla.application.component.view' );
                                  
class dataViewShowInfo extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		//$document = & JFactory::getDocument();
		//$document->setTitle( JText::_('TABLE_PREVIEW') );
    parent::display();		
  }
}
?>

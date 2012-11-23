<?php
jimport( 'joomla.application.component.view' );
                                  
class IziViewUserInfo extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{     
		$document = JFactory::getDocument();
    $document->setType('raw');
    //DEVNOTE:call parent display
    parent::display();		
  }
}
?>

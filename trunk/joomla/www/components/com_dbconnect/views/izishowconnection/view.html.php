<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziShowConnection extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DB_CONNECTION_DETAILS') );

    parent::display();		
  }
}
?>

<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziInfo extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('INFO') );
    
    parent::display();		
  }
}
?>

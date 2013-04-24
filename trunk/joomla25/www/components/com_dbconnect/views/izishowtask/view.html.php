<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziShowTask extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TASK_DETAILS') );

    parent::display();		
  }
}
?>

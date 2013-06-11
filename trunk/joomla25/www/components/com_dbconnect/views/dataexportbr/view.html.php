<?php
jimport( 'joomla.application.component.view' );
                         
                                
class dataViewDataExportBR extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('EXPORT_BR') );
    
    parent::display();		
  }
}
?>

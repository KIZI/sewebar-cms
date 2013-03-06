<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziPreviewAttribute extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('PREVIEW_COLUMN') );
    
    parent::display();		
  }
}
?>

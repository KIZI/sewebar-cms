<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziNewPreprocessing_Equidistant extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_PREPROCESSING') );
    
    parent::display();		
  }
}
?>

<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziNewReportArticle extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                                  
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_ARTICLE'));
      
    parent::display();		
  }
}
?>

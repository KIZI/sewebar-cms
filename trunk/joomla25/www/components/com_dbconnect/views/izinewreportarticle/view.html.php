<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectViewIziNewReportArticle extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                                  
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('NEW_ARTICLE'));
                                        
    if((@$this->confirm=="created")||(@$this->confirm=="storno")){    
      $this->setLayout("info");
    }
      
    parent::display();		
  }
}
?>

<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewDeleteUserGroup extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DELETE_USER_GROUP') );
    
    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

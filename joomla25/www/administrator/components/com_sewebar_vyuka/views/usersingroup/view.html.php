<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewUsersInGroup extends JView
{
	/**
	 * Display the view
	 */
	function display()        
	{                        
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('USERS_IN_GROUP').' '.@$this->userGroup->title);

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    
    $adminModel=$this->getModel('Admin','sewebarModel');
    $this->assignRef('users',$adminModel->usersInGroup(@$this->userGroup->id));
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

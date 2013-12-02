<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewAddUsersIntoGroup extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('ADD_USERS_INTO_GROUP').' '.@$this->userGroup->title);

    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    
    $adminModel=$this->getModel('Admin','sewebarModel');
    $this->assignRef('users',$adminModel->usersInGroup(@$this->parentUserGroupId,@$this->userGroup->parent_id));
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

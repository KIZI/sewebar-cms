<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewSelectUsersUserGroup extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('SELECT_GROUP_FOR_USER').' '.@$this->userGroup->title);
    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    
    $adminModel=$this->getModel('Admin','sewebarModel');
    $this->assignRef('groups',$adminModel->getUserGroups($this->parentUserGroup->id,true));
    $this->assign("currentUserGroup",$adminModel->getUsersGroup($this->user->id,$this->parentUserGroup->id));
    
		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

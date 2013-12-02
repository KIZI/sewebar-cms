<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewUsers extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('USERS') );

    //TOOLBAR, CSS
    JHTML::_('behavior.modal');
    
    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JToolBarHelper::title(JText::_( 'USER_GROUPS' ),'sewebar_vyuka');
    }
    
    
    $adminModel=$this->getModel('Admin','sewebarModel');  
                                        
    $userGroups=$adminModel->usersInGroup($this->parentUserGroup->id);   
    $this->assignRef('users',$userGroups); 		
    $this->assignRef("adminModel",$adminModel);

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

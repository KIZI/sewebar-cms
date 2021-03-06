<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewUserGroups extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('USER_GROUPS') );

    //TOOLBAR, CSS
    JHTML::_('behavior.modal');
    
    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JToolBarHelper::title(JText::_( 'USER_GROUPS' ),'sewebar_vyuka');
    }
    
    
    $adminModel=$this->getModel('Admin','sewebarModel');  
                                        
    $userGroups=$adminModel->getUserGroups($this->parentUserGroup->id,true);   
    $this->assignRef('userGroups',$userGroups); 		

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

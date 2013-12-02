<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class adminViewSelectParentUserGroup extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{                      
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('SELECT_PARENT_USER_GROUP') );

    //TOOLBAR, CSS
    JHTML::_('behavior.modal');
    
    JHtml::stylesheet('main.css','media/com_sewebar_vyuka/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JToolBarHelper::title(JText::_( 'SELECT_PARENT_USER_GROUP' ),'sewebar_vyuka');
    }
    
                                                                 
    $adminModel=$this->getModel('Admin','sewebarModel');                     
    $this->assignRef("userGroups",$adminModel->getUserGroups($this->rootGroupId)); 		

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

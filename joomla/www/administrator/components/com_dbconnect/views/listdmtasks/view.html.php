<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectViewlistDMTasks extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DB_DM_TASKS') );

    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_dbconnect/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JHtml::stylesheet('admin.css','media/com_dbconnect/css/');
      JToolBarHelper::title(JText::_( 'DB_DM_TASKS' ),'dbconnect');
    }
    //

    JHTML::_('behavior.modal');
    
    $user =& JFactory::getUser();
    $this->assign('userId',$user->get('id'));

		$tasksModel=$this->getModel('Tasks','unidbModel');
    $tasks=$tasksModel->getTasks(JRequest::getVar('order','id'));
  	$this->assignRef('tasks',	$tasks); 		

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

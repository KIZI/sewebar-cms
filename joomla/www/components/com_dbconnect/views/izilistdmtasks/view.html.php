<?php

jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class iziViewIziListDMTasks extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DB_DM_TASKS') );
    
    $user =& JFactory::getUser();
    $this->assign('userId',$user->get('id'));

		$tasksModel=$this->getModel('Tasks','unidbModel');
    $tasks=$tasksModel->getTasks(JRequest::getVar('order','id'));
  	$this->assignRef('tasks',	$tasks); 	
    
    $this->setLayout(JRequest::getVar('layout','default'));	

    parent::display();		
  }
}
?>

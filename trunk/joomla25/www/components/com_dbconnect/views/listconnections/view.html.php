<?php
jimport( 'joomla.application.component.view' );
                                  
/**
 * @package Joomla
 * @subpackage Config
 */
class dbconnectViewlistConnections extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('DB_CONNECTIONS') );

    //TOOLBAR, CSS
    JHtml::stylesheet('main.css','media/com_dbconnect/css/');
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      JHtml::stylesheet('admin.css','media/com_dbconnect/css/');
      JToolBarHelper::title(JText::_( 'DB_CONNECTIONS' ),'dbconnect');
    }
    //

    JHTML::_('behavior.modal');
    
    $user =& JFactory::getUser();
    $this->assign('userId',$user->get('id'));

		$connectionsModel=$this->getModel('connections','unidbModel');
    $connections=$connectionsModel->getConnections(JRequest::getVar('order','id'));
  	$this->assignRef('connections',	$connections); 		

		//DEVNOTE:call parent display
    parent::display();		
  }
}
?>

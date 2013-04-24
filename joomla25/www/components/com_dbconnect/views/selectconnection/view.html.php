<?php
jimport( 'joomla.application.component.view' );
                                  
class dbconnectViewselectConnection extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('SELECT_DB_CONNECTION') );

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

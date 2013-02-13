<?php
jimport( 'joomla.application.component.view' );
                                  
class iziViewIziSelectConnection extends JView
{
	/**
	 * Display the view
	 */
	function display()
	{
    JHtml::stylesheet('izi.css','media/com_dbconnect/css/');
    
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('SELECT_DB_CONNECTION') );

    $user =& JFactory::getUser();
    $this->assign('userId',$user->get('id'));

		$connectionsModel=$this->getModel('connections','unidbModel');
    $connections=$connectionsModel->getConnections(JRequest::getVar('order','id'));
  	$this->assignRef('connections',	$connections); 		

    parent::display();		
  }
}
?>

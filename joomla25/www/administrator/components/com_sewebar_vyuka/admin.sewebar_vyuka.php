<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: Make sure the user is authorized to view this page
//$user = & JFactory::getUser();
//if (!$user->authorize( 'com_helloworld02', 'manage' )) {
//	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
//}

//DEVNOTE: 
// specific controller?
// Require specific controller if requested
//if no controller then default controller = 'dbconnect'
$controller = JRequest::getVar('controller','admin' ); 
  
// Create the controller 
$controller = $controller.'Controller';  
                                             
//set the controller page  
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	
//create a new class of classname and set the default task:display
$controller = new $controller(array('default_task' => 'info') );

// Perform the Request task
$controller->execute( JRequest::getVar('task'));
                                            
// Redirect if set by the controller
$controller->redirect(); 
?>

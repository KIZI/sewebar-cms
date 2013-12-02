<?php
/**
* @package helloworld02
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

/*
 * DEVNOTE: This is the 'main' file. 
 * It's the one that will be called when we go to the HELLOWORD component. 
 */

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
$controller = JRequest::getVar('controller','main' ); 
  
// Create the controller 
$controller = $controller.'Controller';  
                                             
//set the controller page  
require_once (JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php');
	
//create a new class of classname and set the default task:display
$controller = new $controller(array('default_task' => 'listConnections') );

// Perform the Request task
$controller->execute( JRequest::getVar('task'));
                                             
// Redirect if set by the controller
$controller->redirect(); 
?>

<?php

/*
 * DEVNOTE: This is the 'main' file. 
 * It's the one that will be called when we go to the HELLOWORD component. 
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load the html class
// DEVNOTE: This will include the admin.helloworld.html.php file, 
// so now we can use anything that it provides!
//require_once( JApplicationHelper::getPath( 'admin_html' ) );
require_once JPATH_COMPONENT.DS.'controller.php'; 


// Create the controller
$controller   = new MappingController();
 
// Perform the Request task
$controller->execute( JRequest::getVar('task','selArticles'));
 
// Redirect if set by the controller
$controller->redirect();
?>

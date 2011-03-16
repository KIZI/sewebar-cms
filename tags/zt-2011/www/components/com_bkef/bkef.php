<?php
/**
 * @package BKEF
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *
 * Vstupní script komponenty BKEF sloužící pro vkládání obsahu.
 */

/* ověření, jestli je skript spouštěn v rámci instance joomly a ne samostatně */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// Require the base controller 
require_once( JPATH_COMPONENT.DS.'controller.php' );
 
// Create the controller
$controller   = new BkefController();
 
// Perform the Request task
$controller->execute( JRequest::getVar('task','selArticle'));
 
// Redirect if set by the controller
$controller->redirect();

?>
<?php
/**
 * @package gInclude
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *
 * Vstupní script komponenty gInclude sloužící pro vkládání obsahu.
 */

/* ověření, jestli je skript spouštěn v rámci instance joomly a ne samostatně */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// Require the base controller 
require_once( JPATH_COMPONENT.DS.'controller.php' );
 
// Create the controller
$controller   = new GincludeController();
 
// Perform the Request task
$controller->execute( JRequest::getVar('task','reload'));
 
// Redirect if set by the controller
$controller->redirect();

?>
<?php
/**
 * Entry point for component. It switches to corresponding controller given by paramter.
 * 
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
JModel::addIncludePath(JPATH_COMPONENT.DS.'models');

$controller = JRequest::getCmd( 'controller', 'sources' );

require_once( JPATH_COMPONENT.DS.'controllers'.DS."$controller.php" );

switch($controller)
{
	case 'demo':
		$controller = new KbiControllerDemo();
		break;
	case 'server':
	default:
		$controller = new KbiControllerServer();
		break;
}

// Perform the Request task
$controller->execute( JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();

?>
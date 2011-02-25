<?php
/**
 * Entry point for component. It switches to corresponding controller given by paramter.
 *
 * @version		$Id: kbi.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the com_content helper library
require_once (JPATH_COMPONENT.DS.'controller.php');

// Create the controller
$controller = new ARDesignerController();

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

?>
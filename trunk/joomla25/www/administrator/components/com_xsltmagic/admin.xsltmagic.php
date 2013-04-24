<?php
/**
 * @version		$Id:$
 * @package		com_xsltmagic
 * @author		David Fišer
 * @copyright	Copyright (C) 2011 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
   	$lang 		= JFactory::getLanguage();
	$lang->load('com_xsltMagic');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
JModel::addIncludePath(JPATH_COMPONENT.DS.'models');

$controller = JRequest::getCmd('controller', 'xslts');

require_once(JPATH_COMPONENT.DS.'controllers'.DS."$controller.php");

switch($controller){
    case 'xslts':
        $controller = new XsltMagicControllerXslts();
        break;
    case 'magic':
        $controller = new XsltMagicControllerMagic();
        break;
}

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>
<?php
/**
 * @version		$Id: jucene.php 
 * @package		Joomla
 * @subpackage	Jucene
 * @copyright	Copyright (C) 2005 - 2010 Lukáš Beránek. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
defined('_JEXEC') or die ('Restricted acces');

//load the default component controller
require_once JPATH_COMPONENT.DS.'controllers'.DS.'controller.php';

if($specController = JRequest::getVar('controller')){

	require_once JPATH_COMPONENT.DS.'controllers'.DS.$specController.'.php';
	
}

// Create new controller or specific one
$classname	= 'JuceneController'.$specController;
$controller = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
//$controller->redirect();
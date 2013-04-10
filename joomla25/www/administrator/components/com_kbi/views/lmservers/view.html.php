<?php
/**
 * @version		$Id: view.html.php 15 2011-02-11 00:57:01Z hazucha.andrej@gmail.com $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

/**
 * Admin list view for sources.
 *
 * @package com_kbi
 */
class KbiViewLmservers extends JView
{
	function setToolbar()
	{
		JToolBarHelper::title( JText::_( 'LispMiner Connect servers' ), 'generic.png' );
		JToolBarHelper::deleteList('', 'remove');
		//JToolBarHelper::editListX( 'edit' );
		JToolBarHelper::addNewX( 'add' );
		//JToolBarHelper::( '' );
		//JToolBarHelper::help( 'screen.banners.client' );
	}

	function display($tpl = NULL)
	{
		self::setToolbar();
		parent::display($tpl);
	}
}
?>
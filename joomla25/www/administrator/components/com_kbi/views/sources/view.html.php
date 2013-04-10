<?php
/**
 * @version		$Id$
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
JLoader::import('KBIntegrator', JPATH_LIBRARIES . DS . 'kbi');

/**
 * Admin list view for sources.
 *
 * @package com_kbi
 */
class KbiViewSources extends JView
{
	function setToolbar()
	{
		JToolBarHelper::title( JText::_( 'KBI Sources' ), 'generic.png' );
		JToolBarHelper::custom('import', 'unarchive', '', 'Import', false, true);
		JToolBarHelper::custom('export', 'archive', '', 'Export', true, true);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( '', 'remove' );
		JToolBarHelper::editListX( 'edit' );
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
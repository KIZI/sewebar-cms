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

/**
 * Admin list view for XSLTs.
 *
 * @package com_kbi
 */
class KbiViewXslts extends JView
{
	function setToolbar()
	{
		JToolBarHelper::title( JText::_( 'KBI XSLT' ), 'generic.png' );
		JToolBarHelper::custom('import', 'unarchive', '', 'Import', true, true);
		JToolBarHelper::custom('export', 'archive', '', 'Export', true, true);
		JToolBarHelper::deleteList( '', 'remove' );
		JToolBarHelper::editListX( 'edit' );
		JToolBarHelper::addNewX( 'add' );
		//JToolBarHelper::help( 'screen.banners.client' );
	}

	function display($tpl = NULL)
	{
		self::setToolbar();
		parent::display($tpl);
	}
}
?>
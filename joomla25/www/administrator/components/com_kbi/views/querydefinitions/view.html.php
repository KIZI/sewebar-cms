<?php
/**
 * @version		$Id: view.html.php 180 2011-04-12 09:33:58Z hazucha.andrej@gmail.com $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Admin list view for queries.
 *
 * @package com_kbi
 */
class KbiViewQuerydefinitions extends JView
{
	function setToolbar()
	{
		JToolBarHelper::title(JText::_( 'KBI Query Definitions' ), 'generic.png');
		JToolBarHelper::custom('export', 'archive', '', 'Export', true, true);
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		//JToolBarHelper::help( 'screen.banners.client' );
	}

	function display($tpl = NULL)
	{
		self::setToolbar();
		parent::display($tpl);
	}
}
?>